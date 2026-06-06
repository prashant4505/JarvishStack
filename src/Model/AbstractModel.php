<?php
namespace App\Model;

use App\Database\QueryBuilder;
use mysqli;

abstract class AbstractModel
{
    protected mysqli $conn;
    protected string $table = '';

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    protected function query(): QueryBuilder
    {
        return new QueryBuilder($this->conn, $this->table);
    }
}
