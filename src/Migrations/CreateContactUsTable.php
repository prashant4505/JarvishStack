<?php
namespace App\Migrations;

use mysqli;

class CreateContactUsTable
{
    public function up(mysqli $conn)
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS contact_us (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $conn->query($sql);
    }
}
