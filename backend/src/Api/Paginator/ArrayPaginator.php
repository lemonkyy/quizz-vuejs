<?php

namespace App\Api\Paginator;

use ApiPlatform\State\Pagination\PaginatorInterface;
use ArrayIterator;
use IteratorAggregate;

class ArrayPaginator implements PaginatorInterface, IteratorAggregate
{
    private array $items;
    private int $currentPage;
    private int $itemsPerPage;
    private int $totalItems;

    public function __construct(array $items, int $currentPage, int $itemsPerPage, int $totalItems)
    {
        $this->items = $items;
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
        $this->totalItems = $totalItems;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function getTotalItems(): float
    {
        return $this->totalItems;
    }

    public function getItemsPerPage(): float
    {
        return $this->itemsPerPage;
    }

    public function getCurrentPage(): float
    {
        return $this->currentPage;
    }

    public function getLastPage(): float
    {
        return ceil($this->totalItems / $this->itemsPerPage);
    }

    public function getFirstItemNumber(): float
    {
        return (($this->currentPage - 1) * $this->itemsPerPage) + 1;
    }

    public function getLastItemNumber(): float
    {
        return min($this->currentPage * $this->itemsPerPage, $this->totalItems);
    }
}
