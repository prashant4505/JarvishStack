<?php
namespace App\Model;

use mysqli;

class User
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function getAll(int $limit = 20): array
    {
        $result = $this->conn->query("SELECT name, email FROM users LIMIT $limit");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function findByName(string $name): array
    {
        $stmt = $this->conn->prepare("SELECT name, email FROM users WHERE name = ? LIMIT 20");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
