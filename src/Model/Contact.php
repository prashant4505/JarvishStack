<?php
namespace App\Model;

class Contact extends AbstractModel
{
    protected string $table = 'contact_us';

    public function create(array $data): bool
    {
        return $this->query()->insert([
            'email'   => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
        ]);
    }

    public function getAll(int $limit = 20): array
    {
        return $this->query()
            ->select('id', 'email', 'subject', 'created_at')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }
}
