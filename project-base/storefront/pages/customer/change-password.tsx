import { MetaRobots } from 'components/Basic/Head/MetaRobots';
import { CustomerLayout } from 'components/Layout/CustomerLayout';
import { ChangePasswordContent } from 'components/Pages/Customer/ChangePassword/ChangePasswordContent';
import { useDomainConfig } from 'components/providers/DomainConfigProvider';
import { useCurrentCustomerData } from 'connectors/customer/CurrentCustomer';
import { TypeBreadcrumbFragment } from 'graphql/requests/breadcrumbs/fragments/BreadcrumbFragment.generated';
import { GtmPageType } from 'gtm/enums/GtmPageType';
import { useGtmStaticPageViewEvent } from 'gtm/factories/useGtmStaticPageViewEvent';
import { useGtmPageViewEvent } from 'gtm/utils/pageViewEvents/useGtmPageViewEvent';
import useTranslation from 'next-translate/useTranslation';
import { getServerSidePropsWrapper } from 'utils/serverSide/getServerSidePropsWrapper';
import { initServerSideProps } from 'utils/serverSide/initServerSideProps';
import { getInternationalizedStaticUrls } from 'utils/staticUrls/getInternationalizedStaticUrls';

const ChangePasswordPage: FC = () => {
    const { t } = useTranslation();
    const { url } = useDomainConfig();
    const [changePasswordUrl] = getInternationalizedStaticUrls(['/customer', '/customer/change-password'], url);
    const currentCustomerUserData = useCurrentCustomerData();
    const breadcrumbs: TypeBreadcrumbFragment[] = [
        { __typename: 'Link', name: t('Change password'), slug: changePasswordUrl },
    ];

    const gtmStaticPageViewEvent = useGtmStaticPageViewEvent(GtmPageType.other, breadcrumbs);
    useGtmPageViewEvent(gtmStaticPageViewEvent);

    return (
        <>
            <MetaRobots content="noindex" />
            <CustomerLayout
                breadcrumbs={breadcrumbs}
                breadcrumbsType="account"
                pageHeading={t('Change password')}
                title={t('Change password')}
            >
                {currentCustomerUserData !== undefined && (
                    <ChangePasswordContent currentCustomerUser={currentCustomerUserData} />
                )}
            </CustomerLayout>
        </>
    );
};

export const getServerSideProps = getServerSidePropsWrapper(
    ({ redisClient, domainConfig, t }) =>
        async (context) =>
            initServerSideProps({
                context,
                authenticationConfig: { authenticationRequired: true },
                redisClient,
                domainConfig,
                t,
            }),
);

export default ChangePasswordPage;
