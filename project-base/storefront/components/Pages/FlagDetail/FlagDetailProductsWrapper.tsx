import { CategoryDetailContentMessage } from '../CategoryDetail/CategoryDetailContentMessage';
import { DEFAULT_PAGE_SIZE, Pagination } from 'components/Blocks/Pagination/Pagination';
import { getEndCursor } from 'components/Blocks/Product/Filter/helpers/getEndCursor';
import { ProductsList } from 'components/Blocks/Product/ProductsList/ProductsList';
import { FlagDetailFragmentApi, useFlagProductsQueryApi } from 'graphql/generated';
import { getFilterOptions } from 'helpers/filterOptions/getFilterOptions';
import { mapParametersFilter } from 'helpers/filterOptions/mapParametersFilter';
import { parseFilterOptionsFromQuery } from 'helpers/filterOptions/parseFilterOptionsFromQuery';
import { getMappedProducts } from 'helpers/mappers/products';
import { getProductListSort } from 'helpers/sorting/getProductListSort';
import { parseProductListSortFromQuery } from 'helpers/sorting/parseProductListSortFromQuery';
import { useQueryError } from 'hooks/graphQl/useQueryError';
import { useGtmPaginatedProductListViewEvent } from 'hooks/gtm/productList/useGtmPaginatedProductListViewEvent';
import { useQueryParams } from 'hooks/useQueryParams';
import { useRouter } from 'next/router';
import { RefObject } from 'react';
import { GtmMessageOriginType, GtmProductListNameType } from 'types/gtm/enums';

type FlagDetailProductsWrapperProps = {
    flag: FlagDetailFragmentApi;
    containerWrapRef: RefObject<HTMLDivElement>;
};

export const FlagDetailProductsWrapper: FC<FlagDetailProductsWrapperProps> = ({ flag, containerWrapRef }) => {
    const { query } = useRouter();
    const { currentPage } = useQueryParams();
    const orderingMode = getProductListSort(parseProductListSortFromQuery(query.sort));
    const parametersFilter = getFilterOptions(parseFilterOptionsFromQuery(query.filter));

    const [{ data: flagProductsData, fetching }] = useQueryError(
        useFlagProductsQueryApi({
            variables: {
                endCursor: getEndCursor(currentPage),
                filter: mapParametersFilter(parametersFilter),
                orderingMode,
                uuid: flag.uuid,
                pageSize: DEFAULT_PAGE_SIZE,
            },
        }),
    );

    const flagListedProducts = getMappedProducts(flagProductsData?.flag?.products.edges);

    useGtmPaginatedProductListViewEvent(flagListedProducts, GtmProductListNameType.flag_detail);

    return (
        <>
            {flagListedProducts && flagListedProducts.length !== 0 ? (
                <>
                    <ProductsList
                        gtmProductListName={GtmProductListNameType.flag_detail}
                        fetching={fetching}
                        products={flagListedProducts}
                        gtmMessageOrigin={GtmMessageOriginType.other}
                    />
                    <Pagination totalCount={flag.products.totalCount} containerWrapRef={containerWrapRef} />
                </>
            ) : (
                <CategoryDetailContentMessage />
            )}
        </>
    );
};