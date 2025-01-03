import { ExtendedNextLink } from 'components/Basic/ExtendedNextLink/ExtendedNextLink';
import { Image } from 'components/Basic/Image/Image';
import { Spinbox } from 'components/Forms/Spinbox/Spinbox';
import { RemoveCartItemButton } from 'components/Pages/Cart/RemoveCartItemButton';
import { TIDs } from 'cypress/tids';
import { TypeCartItemFragment } from 'graphql/requests/cart/fragments/CartItemFragment.generated';
import { TypeAvailabilityStatusEnum } from 'graphql/types';
import useTranslation from 'next-translate/useTranslation';
import { MouseEventHandler, useEffect, useRef, useState } from 'react';
import { twJoin } from 'tailwind-merge';
import { AddToCart } from 'utils/cart/useAddToCart';
import { useFormatPrice } from 'utils/formatting/useFormatPrice';
import { isPriceVisible, mapPriceForCalculations } from 'utils/mappers/price';
import { useDebounce } from 'utils/useDebounce';

type CartListItemProps = {
    item: TypeCartItemFragment;
    listIndex: number;
    onRemoveFromCart: MouseEventHandler<HTMLButtonElement>;
    onAddToCart: AddToCart;
};

export const CartListItem: FC<CartListItemProps> = ({
    item: { product, quantity, uuid },
    listIndex,
    onRemoveFromCart,
    onAddToCart,
}) => {
    const spinboxRef = useRef<HTMLInputElement>(null);
    const [spinboxValue, setSpinboxValue] = useState<number>();
    const debouncedSpinboxValue = useDebounce(spinboxValue, 500);
    const { t } = useTranslation();
    const formatPrice = useFormatPrice();
    const productSlug = product.__typename === 'Variant' ? product.mainVariant!.slug : product.slug;

    useEffect(() => {
        if (debouncedSpinboxValue !== undefined && spinboxRef.current?.valueAsNumber !== quantity) {
            onAddToCart(product.uuid, debouncedSpinboxValue, listIndex, true);
        }
    }, [debouncedSpinboxValue]);

    useEffect(() => {
        if (quantity > 0 && spinboxRef.current) {
            spinboxRef.current.valueAsNumber = quantity;
        }
    }, [quantity]);

    return (
        <div
            className="relative flex flex-row flex-wrap items-center gap-4 border-b border-borderAccent py-5 vl:flex-nowrap"
            tid={TIDs.pages_cart_list_item_ + product.catalogNumber}
        >
            <div className="flex flex-1 basis-full gap-1 pr-8 vl:basis-auto vl:pr-0">
                <div className="flex h-12 w-24 shrink-0">
                    <ExtendedNextLink
                        className="relative h-full w-full"
                        href={productSlug}
                        tid={TIDs.cart_list_item_image}
                        type="product"
                    >
                        <Image
                            alt={product.mainImage?.name || product.fullName}
                            className="mx-auto h-12 object-contain"
                            height={48}
                            src={product.mainImage?.url}
                            width={84}
                        />
                    </ExtendedNextLink>
                </div>

                <div className="flex flex-col items-start gap-4 text-sm font-bold vl:flex-1 vl:flex-row vl:items-center">
                    <div className="h-full text-left vl:w-[16.875rem]" tid={TIDs.pages_cart_list_item_name}>
                        <ExtendedNextLink
                            className="text-sm font-bold uppercase leading-4"
                            href={productSlug}
                            type="product"
                        >
                            {product.fullName}
                        </ExtendedNextLink>

                        <div className="text-sm text-textDisabled">
                            {t('Code')}: {product.catalogNumber}
                        </div>
                    </div>

                    <div
                        className={twJoin(
                            'block flex-1 vl:text-center',
                            product.availability.status === TypeAvailabilityStatusEnum.OutOfStock &&
                                'text-availabilityOutOfStock',
                        )}
                    >
                        {product.availability.name}

                        {!!product.availableStoresCount && (
                            <span className="ml-1 inline font-normal vl:ml-0 vl:block">
                                {t('or at {{ count }} stores', {
                                    count: product.availableStoresCount,
                                })}
                            </span>
                        )}
                    </div>
                </div>
            </div>

            <div className="flex w-28 items-center vl:w-36">
                <Spinbox
                    defaultValue={quantity}
                    id={uuid}
                    min={1}
                    ref={spinboxRef}
                    step={1}
                    onChangeValueCallback={setSpinboxValue}
                />
            </div>

            {isPriceVisible(product.price.priceWithVat) && (
                <div className="flex items-center justify-end text-sm vl:w-32">
                    {formatPrice(product.price.priceWithVat) + '\u00A0/\u00A0' + product.unit.name}
                </div>
            )}

            {isPriceVisible(product.price.priceWithVat) && (
                <div
                    className="ml-auto flex items-center justify-end text-sm text-price lg:text-base vl:w-32"
                    tid={TIDs.pages_cart_list_item_totalprice}
                >
                    {formatPrice(mapPriceForCalculations(product.price.priceWithVat) * quantity)}
                </div>
            )}

            <RemoveCartItemButton
                className="absolute right-0 top-5 flex items-center vl:static"
                onRemoveFromCart={onRemoveFromCart}
            />
        </div>
    );
};
