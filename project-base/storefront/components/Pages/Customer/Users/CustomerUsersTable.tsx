import { EditIcon } from 'components/Basic/Icon/EditIcon';
import { RemoveIcon } from 'components/Basic/Icon/RemoveIcon';
import { Cell, Row, Table } from 'components/Basic/Table/Table';
import { SkeletonCustomerUsersTable } from 'components/Blocks/Skeleton/SkeletonModuleCustomerUsers';
import { Button } from 'components/Forms/Button/Button';
import { TypeSimpleCustomerUserFragment } from 'graphql/requests/customer/fragments/SimpleCustomerUserFragment.generated';
import { useRemoveCustomerUserMutation } from 'graphql/requests/customer/mutations/RemoveCustomerUserMutation.generated';
import { GtmMessageOriginType } from 'gtm/enums/GtmMessageOriginType';
import useTranslation from 'next-translate/useTranslation';
import dynamic from 'next/dynamic';
import { useSessionStore } from 'store/useSessionStore';
import { twJoin } from 'tailwind-merge';
import { useUserPermissions } from 'utils/auth/useUserPermissions';
import { getUserFriendlyErrors } from 'utils/errors/friendlyErrorMessageParser';
import { showErrorMessage } from 'utils/toasts/showErrorMessage';
import { showSuccessMessage } from 'utils/toasts/showSuccessMessage';
import { useCurrentCustomerUsers } from 'utils/user/useCurrentCustomerUsers';

const DeleteCustomerUserPopup = dynamic(
    () =>
        import('components/Blocks/Popup/DeleteCustomerUserPopup').then(
            (component) => component.DeleteCustomerUserPopup,
        ),
    {
        ssr: false,
    },
);

const ManageCustomerUserPopup = dynamic(
    () =>
        import('components/Blocks/Popup/ManageCustomerUserPopup').then(
            (component) => component.ManageCustomerUserPopup,
        ),
    {
        ssr: false,
    },
);

export const CustomerUsersTable: FC = () => {
    const { t } = useTranslation();
    const updatePortalContent = useSessionStore((s) => s.updatePortalContent);
    const [, removeCustomerUser] = useRemoveCustomerUserMutation();
    const { customerUsers, customerUsersIsFetching } = useCurrentCustomerUsers();
    const { currentCustomerUserUuid } = useUserPermissions();

    const deleteItemHandler = async (customerUserUuid: string | undefined) => {
        if (customerUserUuid === undefined) {
            return;
        }

        updatePortalContent(null);
        const deleteCustomerUserResult = await removeCustomerUser({ customerUserUuid });

        if (deleteCustomerUserResult.error !== undefined) {
            const { applicationError } = getUserFriendlyErrors(deleteCustomerUserResult.error, t);

            showErrorMessage(
                applicationError?.message ? applicationError.message : t('There was an error while deleting user'),
                GtmMessageOriginType.other,
            );
            return;
        }

        showSuccessMessage(t('User has been deleted'));
    };

    const openDeleteCustomerUserPopup = (
        e: React.MouseEvent<HTMLButtonElement, MouseEvent>,
        customerUserToBeDeletedUuid: string,
    ) => {
        e.stopPropagation();
        updatePortalContent(
            <DeleteCustomerUserPopup
                deleteCustomerUserHandler={() => deleteItemHandler(customerUserToBeDeletedUuid)}
            />,
        );
    };

    const openManageCustomerUserPopup = (
        e: React.MouseEvent<HTMLButtonElement, MouseEvent>,
        customerUser?: TypeSimpleCustomerUserFragment,
    ) => {
        e.stopPropagation();
        updatePortalContent(
            <ManageCustomerUserPopup customerUser={customerUser} mode={customerUser ? 'edit' : 'add'} />,
        );
    };

    if (customerUsersIsFetching) {
        return (
            <div className="flex w-full flex-col">
                <SkeletonCustomerUsersTable />
            </div>
        );
    }

    return (
        <Table className="w-full border-0 p-0">
            {customerUsers.map((user) => (
                <Row
                    key={user.uuid}
                    className="mb-2 flex flex-col rounded-md border-none bg-tableBackgroundContrast vl:table-row vl:bg-tableBackground vl:odd:bg-tableBackgroundContrast"
                >
                    <Cell className="py-2 text-left text-sm font-bold uppercase leading-5">
                        {user.lastName} {user.firstName} {currentCustomerUserUuid === user.uuid && `(${t('You')})`}
                    </Cell>

                    <Cell
                        className={twJoin(
                            'py-2 text-left text-sm leading-5 vl:table-cell',
                            'max-w-64 overflow-x-auto whitespace-nowrap sm:max-w-full vl:max-w-56',
                            '[&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-backgroundMost [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar]:h-1',
                        )}
                    >
                        {user.email}
                    </Cell>
                    <Cell className="py-2 text-left text-sm leading-5 vl:table-cell">{user.roleGroup.name}</Cell>
                    <Cell align="right" className="flex flex-row-reverse gap-2 py-2 vl:flex-row vl:justify-end">
                        <Button
                            className="flex-1"
                            size="small"
                            variant="inverted"
                            onClick={(e) => openManageCustomerUserPopup(e, user)}
                        >
                            <EditIcon className="size-4" /> <span className="sm:block">{t('Edit')}</span>
                        </Button>
                        <Button
                            className="flex-1"
                            size="small"
                            variant="inverted"
                            onClick={(e) => openDeleteCustomerUserPopup(e, user.uuid)}
                        >
                            <RemoveIcon className="size-4" /> <span className="sm:block">{t('Delete')}</span>
                        </Button>
                    </Cell>
                </Row>
            ))}
        </Table>
    );
};
