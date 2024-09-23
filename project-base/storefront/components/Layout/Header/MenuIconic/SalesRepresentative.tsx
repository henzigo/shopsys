import { MailIcon } from 'components/Basic/Icon/MailIcon';
import { PhoneIcon } from 'components/Basic/Icon/PhoneIcon';
import { Image } from 'components/Basic/Image/Image';
import { useCurrentCustomerData } from 'connectors/customer/CurrentCustomer';
import { TypeSalesRepresentative } from 'graphql/types';
import useTranslation from 'next-translate/useTranslation';

export const SalesRepresentative: FC = () => {
    const { t } = useTranslation();
    const currentCustomerData = useCurrentCustomerData();
    const salesRepresentative = currentCustomerData?.salesRepresentative;
    if (!salesRepresentative) {
        return null;
    }

    const { telephone, email } = salesRepresentative;
    const fullName = getFullName(salesRepresentative.firstName, salesRepresentative.lastName);

    if (!getShowSalesRepresentative(salesRepresentative)) {
        return null;
    }

    return (
        <div className="flex w-full items-start gap-4 pt-4">
            {salesRepresentative.image && (
                <Image
                    alt={t('Need advice?')}
                    className="h-12 w-12 rounded-full object-cover"
                    height={100}
                    src={salesRepresentative.image.url}
                    width={100}
                />
            )}
            <div className="dark:text-white w-full font-medium">
                {fullName && (
                    <>
                        <p className="font-secondary text-lg font-semibold leading-7">{fullName}</p>
                        <p className="font-secondary text-xs font-semibold uppercase tracking-wider text-textSubtle">
                            {t('Your sales representative')}
                        </p>
                    </>
                )}
                {telephone && (
                    <div className="my-2 flex items-center gap-2">
                        <PhoneIcon className="h-6 w-6 flex-shrink-0 p-0.5" />
                        <a className="text-sm text-text no-underline" href={`tel:${telephone}`}>
                            {formatPhoneNumber(telephone)}
                        </a>
                    </div>
                )}
                {email && (
                    <div className="mt-1 flex w-full max-w-80 items-center gap-2 overflow-auto lg:max-w-full">
                        <MailIcon className="h-6 w-6 flex-shrink-0" />
                        <a className="max-w-44 text-sm text-text no-underline lg:max-w-96" href={`mailto:${email}`}>
                            {email}
                        </a>
                    </div>
                )}
            </div>
        </div>
    );
};

const getFullName = (firstName?: string | null, lastName?: string | null): string | null | undefined => {
    if (!firstName || !lastName) {
        return firstName ?? lastName;
    }
    return `${firstName} ${lastName}`;
};

const formatPhoneNumber = (phoneNumber: string): string | null | undefined => {
    if (phoneNumber && phoneNumber.length === 9) {
        return phoneNumber.replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3');
    }
    return phoneNumber ? phoneNumber : undefined;
};

const getShowSalesRepresentative = (salesRepresentative: TypeSalesRepresentative | null | undefined): boolean => {
    return (
        !!salesRepresentative &&
        !!(
            salesRepresentative.firstName ||
            salesRepresentative.lastName ||
            salesRepresentative.email ||
            salesRepresentative.telephone
        )
    );
};
