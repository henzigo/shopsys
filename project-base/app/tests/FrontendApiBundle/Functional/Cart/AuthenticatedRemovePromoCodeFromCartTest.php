<?php

declare(strict_types=1);

namespace Tests\FrontendApiBundle\Functional\Cart;

use App\DataFixtures\Demo\ProductDataFixture;
use App\DataFixtures\Demo\PromoCodeDataFixture;
use App\Model\Order\PromoCode\PromoCode;
use App\Model\Order\PromoCode\PromoCodeFacade;
use App\Model\Product\Product;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Tests\FrontendApiBundle\Test\GraphQlWithLoginTestCase;
use Tests\FrontendApiBundle\Test\PromoCodeAssertionTrait;

class AuthenticatedRemovePromoCodeFromCartTest extends GraphQlWithLoginTestCase
{
    use PromoCodeAssertionTrait;

    /**
     * @inject
     */
    private PromoCodeFacade $promoCodeFacade;

    public function testRemovePromoCodeFromCart(): void
    {
        $promoCode = $this->applyValidPromoCodeToCustomerCart();

        $response = $this->getResponseContentForGql(__DIR__ . '/graphql/RemovePromoCodeFromCart.graphql', [
            'promoCode' => $promoCode->getCode(),
        ]);
        $data = $this->getResponseDataForGraphQlType($response, 'RemovePromoCodeFromCart');

        self::assertNotNull($this->promoCodeFacade->findPromoCodeByCodeAndDomain($promoCode->getCode(), Domain::FIRST_DOMAIN_ID));

        self::assertNull($data['promoCode']);
    }

    public function testPromoCodeIsRemovedFromCartAfterDeletion(): void
    {
        $promoCode = $this->applyValidPromoCodeToCustomerCart();

        $this->em->remove($promoCode);
        $this->em->flush();

        $response = $this->getResponseContentForGql(__DIR__ . '/graphql/GetCart.graphql');
        $data = $this->getResponseDataForGraphQlType($response, 'cart');

        self::assertNull($data['promoCode']);

        // if promo code is deleted, CartWatcher cannot possibly know about it and report modification
        self::assertEmpty($data['modifications']['promoCodeModifications']['noLongerApplicablePromoCode']);
    }

    /**
     * @return \App\Model\Order\PromoCode\PromoCode
     */
    public function applyValidPromoCodeToCustomerCart(): PromoCode
    {
        $promoCodeReference = PromoCodeDataFixture::VALID_PROMO_CODE;

        $promoCode = $this->getReferenceForDomain($promoCodeReference, 1, PromoCode::class);

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . 1, Product::class);

        $this->getResponseContentForGql(__DIR__ . '/../_graphql/mutation/AddToCartMutation.graphql', [
            'productUuid' => $product->getUuid(),
            'quantity' => 2,
        ]);

        $response = $this->getResponseContentForGql(__DIR__ . '/graphql/ApplyPromoCodeToCart.graphql', [
            'promoCode' => $promoCode->getCode(),
        ]);
        $data = $this->getResponseDataForGraphQlType($response, 'ApplyPromoCodeToCart');

        self::assertPromoCode($promoCode, $data['promoCode']);

        // refresh promo code, so we're able to work with it as with an entity
        return $this->getReferenceForDomain($promoCodeReference, 1, PromoCode::class);
    }
}
