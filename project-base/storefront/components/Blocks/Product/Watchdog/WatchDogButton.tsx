import { WatchdogIcon } from 'components/Basic/Icon/Watchdog';
import { Button } from 'components/Forms/Button/Button';
import { TypeAvailability, TypeAvailabilityStatusEnum } from 'graphql/types';
import useTranslation from 'next-translate/useTranslation';
import dynamic from 'next/dynamic';
import { useSessionStore } from 'store/useSessionStore';
import { twJoin } from 'tailwind-merge';

const WatchdogPopup = dynamic(
    () => import('components/Blocks/Popup/WatchdogPopup').then((component) => component.WatchdogPopup),
    {
        ssr: false,
    },
);

type WatchDogButtonProps = {
    productUuid: string | undefined;
    isInquiryType: boolean;
    availability: TypeAvailability;
    productIsSellingDenied: boolean | undefined;
};

export const WatchDogButton: FC<WatchDogButtonProps> = ({
    productUuid,
    isInquiryType,
    availability,
    productIsSellingDenied,
    className,
}) => {
    const { t } = useTranslation();
    const updatePortalContent = useSessionStore((s) => s.updatePortalContent);

    const showWatchdogButton =
        (productUuid && !isInquiryType && availability.status === TypeAvailabilityStatusEnum.OutOfStock) ||
        productIsSellingDenied;

    if (!showWatchdogButton) {
        return null;
    }

    const openWatchDogPopup = (e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => {
        e.stopPropagation();

        if (productUuid) {
            updatePortalContent(<WatchdogPopup productUuid={productUuid} />);
        }
    };

    return (
        <Button
            className={twJoin('whitespace-nowrap', className)}
            size="large"
            variant="primary"
            onClick={openWatchDogPopup}
        >
            <WatchdogIcon className="size-6" />
            {t('Watch the goods')}
        </Button>
    );
};
