<?php
namespace App\Model;

class User extends AbstractModel
{
    public function getAll(int $limit = 20): array
    {
        $stmt = $this->conn->prepare("SELECT name, email FROM users LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function findByName(string $name): array
    {
        $stmt = $this->conn->prepare("SELECT name, email FROM users WHERE name = ? LIMIT 20");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
