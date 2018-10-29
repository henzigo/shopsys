<?php

namespace Shopsys\FrameworkBundle\Component\Paginator;

interface PaginatorInterface
{
    /**
     * @param  $page
     * @param  $pageSize
     */
    public function getResult($page, $pageSize);

    public function getTotalCount();
}
