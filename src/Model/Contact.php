<?php
namespace App\Model;

use mysqli;

class Contact
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function create(array $data): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO contact_us (email, subject, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $data['email'], $data['subject'], $data['message']);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getAll(int $limit = 20): array
    {
        $result = $this->conn->query("SELECT id, email, subject, created_at FROM contact_us LIMIT $limit");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
