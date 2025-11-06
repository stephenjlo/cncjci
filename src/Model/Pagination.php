<?php
namespace App\Model;
class Pagination {
  public function __construct(
    public array $items,
    public int $total,
    public int $page,
    public int $pageSize
  ) {}
}
