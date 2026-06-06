<?php
namespace App\Model;

class User extends AbstractModel
{
    protected string $table = 'users';

    public function getAll(int $limit = 20): array
    {
        return $this->query()
            ->select('name', 'email')
            ->limit($limit)
            ->get();
    }

    public function findByName(string $name): array
    {
        return $this->query()
            ->select('name', 'email')
            ->where('name', $name)
            ->limit(20)
            ->get();
    }
}
