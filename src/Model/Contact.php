<?php
namespace App\Model;

class Contact extends AbstractModel
{
    public function create(array $data): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO contact_us (email, subject, message) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $data['email'], $data['subject'], $data['message']);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getAll(int $limit = 20): array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, email, subject, created_at FROM contact_us ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
