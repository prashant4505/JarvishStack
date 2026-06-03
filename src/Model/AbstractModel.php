<?php
namespace App\Model;

use mysqli;

abstract class AbstractModel
{
    protected mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }
}
