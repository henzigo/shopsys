import { Radiobutton } from 'components/Forms/Radiobutton/Radiobutton';
import { TransportAndPaymentListItem } from 'components/Pages/Order/TransportAndPayment/TransportAndPaymentSelect/TransportAndPaymentListItem';
import { TransportAndPaymentSelectItemLabel } from 'components/Pages/Order/TransportAndPayment/TransportAndPaymentSelect/TransportAndPaymentSelectItemLabel';
import { TypeSimplePaymentFragment } from 'graphql/requests/payments/fragments/SimplePaymentFragment.generated';
import { Dispatch, SetStateAction } from 'react';

type PaymentsInOrderSelectItemProps = {
    payment: TypeSimplePaymentFragment;
    selectedPaymentForChange: TypeSimplePaymentFragment | undefined;
    setSelectedPaymentForChange: Dispatch<SetStateAction<TypeSimplePaymentFragment | undefined>>;
    selectedPaymentSwiftForChange?: string | null;
    setSelectedPaymentSwiftForChange?: Dispatch<SetStateAction<string | undefined | null>>;
};

export const PaymentsInOrderSelectItem: FC<PaymentsInOrderSelectItemProps> = ({
    payment,
    selectedPaymentForChange,
    setSelectedPaymentForChange,
    setSelectedPaymentSwiftForChange,
}) => {
    const isPaymentSelected = selectedPaymentForChange?.uuid === payment.uuid;

    return (
        <TransportAndPaymentListItem
            key={payment.uuid}
            className="order-none flex w-auto flex-col"
            isActive={isPaymentSelected}
        >
            <Radiobutton
                checked={isPaymentSelected}
                id={payment.uuid}
                labelWrapperClassName="gap-5"
                name="payment"
                value={payment.uuid}
                label={
                    <TransportAndPaymentSelectItemLabel
                        description={payment.description}
                        image={payment.mainImage}
                        isSelected={isPaymentSelected}
                        name={payment.name}
                        price={payment.price}
                    />
                }
                onChange={() => {
                    setSelectedPaymentForChange(payment);
                    setSelectedPaymentSwiftForChange?.(undefined);
                }}
            />
        </TransportAndPaymentListItem>
    );
};
