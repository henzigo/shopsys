import {
    fillEmailInThirdStep,
    fillCustomerInformationInThirdStep,
    fillBillingAdressInThirdStep,
    fillInNoteInThirdStep,
    clickOnSendOrderButton,
    clickOnOrderDetailButtonOnThankYouPage,
    fillRegistrationInfoAfterOrder,
    changeOrderDetailDynamicPartsToStaticDemodata,
    changeOrderConfirmationDynamicPartsToStaticDemodata,
    submitRegistrationFormAfterOrder,
    goToOrderDetailFromOrderList,
    mouseOverUserMenuButton,
    checkOrderConfirmationStatusText,
    checkOrderDetailFromOrderPage,
    checkOrderDetailFromOrderPageWithComplaintButton,
    checkOrderDetailFromOrderPageWithPromoCode,
} from './orderSupport';
import { transport, payment, customer1, orderNote, url, promoCode, password, order } from 'fixtures/demodata';
import { generateCustomerRegistrationData } from 'fixtures/generators';
import {
    checkAndHideSuccessToast,
    checkUrl,
    goToEditProfileFromHeader,
    initializePersistStoreInLocalStorageToDefaultValues,
    loseFocus,
    takeSnapshotAndCompare,
} from 'support';
import { TIDs } from 'tids';

describe('Create Order Tests', () => {
    beforeEach(() => {
        initializePersistStoreInLocalStorageToDefaultValues();
        cy.addProductToCartForTest().then((cart) => cy.storeCartUuidInLocalStorage(cart.uuid));
    });

    it('[Anon Registered Home Cash] create order as unlogged user with a registered email (transport to home, cash on delivery) and check it in order detail', function () {
        cy.preselectTransportForTest(transport.czechPost.uuid);
        cy.preselectPaymentForTest(payment.onDelivery.uuid);
        cy.visitAndWaitForStableAndInteractiveDOM(url.order.contactInformation);

        fillEmailInThirdStep(customer1.emailRegistered);
        fillCustomerInformationInThirdStep(customer1.phone, customer1.firstName, customer1.lastName);
        fillBillingAdressInThirdStep(customer1.billingStreet, customer1.billingCity, customer1.billingPostCode);
        fillInNoteInThirdStep(orderNote);
        loseFocus();
        takeSnapshotAndCompare(this.test?.title, 'filled contact information form', {
            blackout: [
                { tid: TIDs.order_summary_transport_and_payment_image },
                { tid: TIDs.order_summary_cart_item_image },
            ],
        });

        clickOnSendOrderButton();
        cy.waitForStableAndInteractiveDOM();
        changeOrderConfirmationDynamicPartsToStaticDemodata();
        checkOrderConfirmationStatusText(order.confirmation.czechPost);

        clickOnOrderDetailButtonOnThankYouPage();
        cy.waitForStableAndInteractiveDOM();
        changeOrderDetailDynamicPartsToStaticDemodata();
        checkOrderDetailFromOrderPage(transport.czechPost.name, payment.onDelivery.name, orderNote);
    });

    it('[Anon Home Cash] create order as unlogged user (transport to home, cash on delivery) and check it in order detail', function () {
        cy.preselectTransportForTest(transport.czechPost.uuid);
        cy.preselectPaymentForTest(payment.onDelivery.uuid);
        cy.visitAndWaitForStableAndInteractiveDOM(url.order.contactInformation);

        fillEmailInThirdStep(customer1.email);
        fillCustomerInformationInThirdStep(customer1.phone, customer1.firstName, customer1.lastName);
        fillBillingAdressInThirdStep(customer1.billingStreet, customer1.billingCity, customer1.billingPostCode);
        fillInNoteInThirdStep(orderNote);
        loseFocus();
        takeSnapshotAndCompare(this.test?.title, 'filled contact information form', {
            blackout: [
                { tid: TIDs.order_summary_transport_and_payment_image },
                { tid: TIDs.order_summary_cart_item_image },
            ],
        });

        clickOnSendOrderButton();
        cy.waitForStableAndInteractiveDOM();
        changeOrderConfirmationDynamicPartsToStaticDemodata();
        checkOrderConfirmationStatusText(order.confirmation.czechPost);

        clickOnOrderDetailButtonOnThankYouPage();
        cy.waitForStableAndInteractiveDOM();
        changeOrderDetailDynamicPartsToStaticDemodata();
        checkOrderDetailFromOrderPage(transport.czechPost.name, payment.onDelivery.name, orderNote);
    });

    it('[Anon Collect Cash] create order as unlogged user (personal collection, cash) and check it in order detail', function () {
        cy.preselectTransportForTest(transport.personalCollection.uuid, transport.personalCollection.storeOstrava.uuid);
        cy.preselectPaymentForTest(payment.cash.uuid);
        cy.visitAndWaitForStableAndInteractiveDOM(url.order.contactInformation);

        fillEmailInThirdStep(customer1.email);
        fillCustomerInformationInThirdStep(customer1.phone, customer1.firstName, customer1.lastName);
        fillBillingAdressInThirdStep(customer1.billingStreet, customer1.billingCity, customer1.billingPostCode);
        fillInNoteInThirdStep(orderNote);
        loseFocus();
        takeSnapshotAndCompare(this.test?.title, 'filled contact information form', {
            blackout: [
                { tid: TIDs.order_summary_transport_and_payment_image },
                { tid: TIDs.order_summary_cart_item_image },
            ],
        });

        clickOnSendOrderButton();
        cy.waitForStableAndInteractiveDOM();
        changeOrderConfirmationDynamicPartsToStaticDemodata();
        checkOrderConfirmationStatusText(order.confirmation.presonalCollection);

        clickOnOrderDetailButtonOnThankYouPage();
        cy.waitForStableAndInteractiveDOM();
        changeOrderDetailDynamicPartsToStaticDemodata();
        checkOrderDetailFromOrderPage(
            `${transport.personalCollection.name} ${transport.personalCollection.storeOstrava.name}`,
            payment.cash.name,
            orderNote,
        );
    });

    it('[Anon PPL Card] create order as unlogged user (PPL, credit card) and check it in order detail', function () {
        cy.preselectTransportForTest(transport.ppl.uuid);
        cy.preselectPaymentForTest(payment.creditCard.uuid);
        cy.visitAndWaitForStableAndInteractiveDOM(url.order.contactInformation);

        fillEmailInThirdStep(customer1.email);
        fillCustomerInformationInThirdStep(customer1.phone, customer1.firstName, customer1.lastName);
        fillBillingAdressInThirdStep(customer1.billingStreet, customer1.billingCity, customer1.billingPostCode);
        fillInNoteInThirdStep(orderNote);
        loseFocus();
        takeSnapshotAndCompare(this.test?.title, 'filled contact information form', {
            blackout: [
                { tid: TIDs.order_summary_transport_and_payment_image },
                { tid: TIDs.order_summary_cart_item_image },
            ],
        });

        clickOnSendOrderButton();
        cy.waitForStableAndInteractiveDOM();
        changeOrderConfirmationDynamicPartsToStaticDemodata();
        checkOrderConfirmationStatusText(order.confirmation.card);

        clickOnOrderDetailButtonOnThankYouPage();
        cy.waitForStableAndInteractiveDOM();
        changeOrderDetailDynamicPartsToStaticDemodata();
        checkOrderDetailFromOrderPage(transport.ppl.name, payment.creditCard.name, orderNote);
    });

    it('[Anon Promo Code] create order with promo code and check it in order detail', function () {
        cy.addPromoCodeToCartForTest(promoCode);
        cy.preselectTransportForTest(transport.czechPost.uuid);
        cy.preselectPaymentForTest(payment.onDelivery.uuid);
        cy.visitAndWaitForStableAndInteractiveDOM(url.order.contactInformation);

        fillEmailInThirdStep(customer1.email);
        fillCustomerInformationInThirdStep(customer1.phone, customer1.firstName, customer1.lastName);
        fillBillingAdressInThirdStep(customer1.billingStreet, customer1.billingCity, customer1.billingPostCode);
        fillInNoteInThirdStep(orderNote);
        loseFocus();
        takeSnapshotAndCompare(this.test?.title, 'filled contact information form', {
            blackout: [
                { tid: TIDs.order_summary_transport_and_payment_image },
                { tid: TIDs.order_summary_cart_item_image },
            ],
        });

        clickOnSendOrderButton();
        cy.waitForStableAndInteractiveDOM();
        changeOrderConfirmationDynamicPartsToStaticDemodata();
        checkOrderConfirmationStatusText(order.confirmation.czechPost);

        clickOnOrderDetailButtonOnThankYouPage();
        cy.waitForStableAndInteractiveDOM();
        changeOrderDetailDynamicPartsToStaticDemodata();
        checkOrderDetailFromOrderPageWithPromoCode(transport.czechPost.name, payment.onDelivery.name, orderNote);
    });

    it(
        '[Register After Order] register after order completion, and check that the just created order is in customer orders',
        { retries: { runMode: 0 } },
        function () {
            cy.preselectTransportForTest(transport.czechPost.uuid);
            cy.preselectPaymentForTest(payment.onDelivery.uuid);
            cy.visitAndWaitForStableAndInteractiveDOM(url.order.contactInformation);

            fillEmailInThirdStep('after-order-registration@shopsys.com');
            fillCustomerInformationInThirdStep(customer1.phone, customer1.firstName, customer1.lastName);
            fillBillingAdressInThirdStep(customer1.billingStreet, customer1.billingCity, customer1.billingPostCode);
            loseFocus();
            takeSnapshotAndCompare(this.test?.title, 'filled contact information form', {
                blackout: [
                    { tid: TIDs.order_summary_transport_and_payment_image },
                    { tid: TIDs.order_summary_cart_item_image },
                ],
            });

            clickOnSendOrderButton();
            cy.waitForStableAndInteractiveDOM();
            changeOrderConfirmationDynamicPartsToStaticDemodata();
            checkOrderConfirmationStatusText(order.confirmation.czechPost);

            fillRegistrationInfoAfterOrder(password);
            submitRegistrationFormAfterOrder();
            checkAndHideSuccessToast('Your account has been created and you are logged in now');
            cy.waitForStableAndInteractiveDOM();
            checkUrl('/');

            cy.visitAndWaitForStableAndInteractiveDOM(url.customer.orders);
            goToOrderDetailFromOrderList();
            changeOrderDetailDynamicPartsToStaticDemodata(true);
            checkOrderDetailFromOrderPageWithComplaintButton(transport.czechPost.name, payment.onDelivery.name);

            goToEditProfileFromHeader();
            takeSnapshotAndCompare(this.test?.title, 'customer edit page', {
                blackout: [{ tid: TIDs.footer_social_links }],
            });
        },
    );

    it(
        '[Logged Home Cash] create order as logged-in user (transport to home, cash on delivery) and check it in order detail',
        { retries: { runMode: 0 } },
        function () {
            cy.registerAsNewUser(
                generateCustomerRegistrationData('commonCustomer', 'create-order-as-logged-in-user@shopsys.com'),
            );
            cy.addProductToCartForTest().then((cart) => cy.storeCartUuidInLocalStorage(cart.uuid));
            cy.preselectTransportForTest(transport.czechPost.uuid);
            cy.preselectPaymentForTest(payment.onDelivery.uuid);
            cy.visitAndWaitForStableAndInteractiveDOM(url.order.contactInformation);

            fillInNoteInThirdStep(orderNote);
            loseFocus();
            takeSnapshotAndCompare(this.test?.title, 'filled contact information form', {
                blackout: [
                    { tid: TIDs.order_summary_transport_and_payment_image },
                    { tid: TIDs.order_summary_cart_item_image },
                ],
            });

            clickOnSendOrderButton();
            cy.waitForStableAndInteractiveDOM();
            changeOrderConfirmationDynamicPartsToStaticDemodata();
            mouseOverUserMenuButton();
            checkOrderConfirmationStatusText(order.confirmation.czechPost);

            clickOnOrderDetailButtonOnThankYouPage();
            changeOrderDetailDynamicPartsToStaticDemodata();
            checkOrderDetailFromOrderPageWithComplaintButton(
                transport.czechPost.name,
                payment.onDelivery.name,
                orderNote,
            );
        },
    );
});
