import { CompareIcon } from 'components/Basic/Icon/CompareIcon';
import { ComplaintsIcon } from 'components/Basic/Icon/ComplaintsIcon';
import { EditIcon } from 'components/Basic/Icon/EditIcon';
import { HeartIcon } from 'components/Basic/Icon/HeartIcon';
import { LockCheckIcon } from 'components/Basic/Icon/LockCheck';
import { SearchListIcon } from 'components/Basic/Icon/SearchListIcon';
import { UserIcon } from 'components/Basic/Icon/UserIcon';
import { useDomainConfig } from 'components/providers/DomainConfigProvider';
import useTranslation from 'next-translate/useTranslation';
import { PageType } from 'store/slices/createPageLoadingStateSlice';
import { useUserPermissions } from 'utils/auth/useUserPermissions';
import { useComparison } from 'utils/productLists/comparison/useComparison';
import { useWishlist } from 'utils/productLists/wishlist/useWishlist';
import { getInternationalizedStaticUrls } from 'utils/staticUrls/getInternationalizedStaticUrls';

type UserMenuItemType = {
    link: string;
    text: string;
    count?: number;
    iconComponent?: React.ElementType;
    type?: PageType;
};

export const useUserMenuItems = (): UserMenuItemType[] => {
    const { t } = useTranslation();
    const { url } = useDomainConfig();
    const { comparison } = useComparison();
    const { wishlist } = useWishlist();
    const { canManageUsers } = useUserPermissions();
    const [
        customerOrdersUrl,
        customerComplaintsUrl,
        customerUsersUrl,
        customerEditProfileUrl,
        customerChangePasswordUrl,
        productComparisonUrl,
        wishlistUrl,
    ] = getInternationalizedStaticUrls(
        [
            '/customer/orders',
            '/customer/complaints',
            '/customer/users',
            '/customer/edit-profile',
            '/customer/change-password',
            '/product-comparison',
            '/wishlist',
        ],
        url,
    );

    const userMenuItems: UserMenuItemType[] = [
        {
            text: t('Orders'),
            link: customerOrdersUrl,
            type: 'orderList',
            iconComponent: SearchListIcon,
        },
        {
            text: t('Complaints'),
            link: customerComplaintsUrl,
            type: 'complaintList',
            iconComponent: ComplaintsIcon,
        },
        {
            text: t('Edit profile'),
            link: customerEditProfileUrl,
            type: 'editProfile',
            iconComponent: EditIcon,
        },
        {
            text: t('Change password'),
            link: customerChangePasswordUrl,
            type: 'changePassword',
            iconComponent: LockCheckIcon,
        },
        {
            text: t('Wishlist'),
            link: wishlistUrl,
            count: wishlist?.products.length,
            type: 'wishlist',
            iconComponent: HeartIcon,
        },
        {
            text: t('Comparison'),
            link: productComparisonUrl,
            count: comparison?.products.length,
            type: 'comparison',
            iconComponent: CompareIcon,
        },
    ];

    if (canManageUsers) {
        userMenuItems.splice(2, 0, {
            text: t('Customer users'),
            link: customerUsersUrl,
            type: 'customer-users',
            iconComponent: UserIcon,
        });
    }

    return userMenuItems;
};
