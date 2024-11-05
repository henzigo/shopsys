import { SkeletonModuleCustomer } from './SkeletonModuleCustomer';
import Skeleton from 'react-loading-skeleton';

export const SkeletonModuleAddButton: FC = () => <Skeleton className="mb-4 h-8 w-[124px] rounded-lg" />;

export const SkeletonCustomerUsersTable: FC = () => (
    <>
        <Skeleton className="mb-0.5 h-12 w-full" />
        <Skeleton className="mb-0.5 h-12 w-full" />
        <Skeleton className="mb-0.5 h-12 w-full" />
    </>
);

export const SkeletonModuleCustomerUsers: FC = () => (
    <SkeletonModuleCustomer>
        <div className="flex w-full flex-col">
            <Skeleton className="mb-4 h-10 w-40" />
            <Skeleton className="mb-4 h-8 w-36" />

            <SkeletonCustomerUsersTable />
        </div>
    </SkeletonModuleCustomer>
);
