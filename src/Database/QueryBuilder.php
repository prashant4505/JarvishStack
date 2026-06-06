<?php
namespace App\Database;

use mysqli;
use RuntimeException;

class QueryBuilder
{
    private mysqli $conn;
    private string $table;
    private array $selects = [];
    private array $conditions = [];
    private array $bindings = [];
    private string $bindTypes = '';
    private ?int $limitVal = null;
    private ?string $orderCol = null;
    private string $orderDir = 'ASC';

    public function __construct(mysqli $conn, string $table)
    {
        $this->conn  = $conn;
        $this->table = $table;
    }

    public function select(string ...$columns): static
    {
        $this->selects = $columns;
        return $this;
    }

    public function where(string $column, mixed $value): static
    {
        $this->conditions[] = "`{$column}` = ?";
        $this->bindings[]   = $value;
        $this->bindTypes   .= $this->inferType($value);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->orderCol = $column;
        $this->orderDir = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        return $this;
    }

    public function limit(int $n): static
    {
        $this->limitVal = $n;
        return $this;
    }

    public function get(): array
    {
        $cols = empty($this->selects)
            ? '*'
            : implode(', ', array_map(fn($c) => "`{$c}`", $this->selects));

        $sql = "SELECT {$cols} FROM `{$this->table}`";

        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->conditions);
        }
        if ($this->orderCol !== null) {
            $sql .= " ORDER BY `{$this->orderCol}` {$this->orderDir}";
        }
        if ($this->limitVal !== null) {
            $sql .= " LIMIT {$this->limitVal}";
        }

        $result = $this->executeQuery($sql, $this->bindTypes, $this->bindings);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function first(): ?array
    {
        $rows = $this->limit(1)->get();
        return $rows[0] ?? null;
    }

    public function insert(array $data): bool
    {
        $cols   = array_keys($data);
        $values = array_values($data);
        $types  = implode('', array_map([$this, 'inferType'], $values));

        $colList     = implode(', ', array_map(fn($c) => "`{$c}`", $cols));
        $placeholder = implode(', ', array_fill(0, count($values), '?'));

        $sql  = "INSERT INTO `{$this->table}` ({$colList}) VALUES ({$placeholder})";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param($types, ...$values);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function insertOrIgnore(array $data): bool
    {
        $cols   = array_keys($data);
        $values = array_values($data);
        $types  = implode('', array_map([$this, 'inferType'], $values));

        $colList     = implode(', ', array_map(fn($c) => "`{$c}`", $cols));
        $placeholder = implode(', ', array_fill(0, count($values), '?'));

        $sql  = "INSERT IGNORE INTO `{$this->table}` ({$colList}) VALUES ({$placeholder})";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param($types, ...$values);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function update(array $data): bool
    {
        if (empty($this->conditions)) {
            throw new RuntimeException('UPDATE without WHERE is not allowed.');
        }

        $setCols   = array_keys($data);
        $setValues = array_values($data);
        $setTypes  = implode('', array_map([$this, 'inferType'], $setValues));

        $setClauses = implode(', ', array_map(fn($c) => "`{$c}` = ?", $setCols));
        $sql        = "UPDATE `{$this->table}` SET {$setClauses} WHERE " . implode(' AND ', $this->conditions);
        $allValues  = array_merge($setValues, $this->bindings);
        $allTypes   = $setTypes . $this->bindTypes;

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param($allTypes, ...$allValues);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function delete(): bool
    {
        if (empty($this->conditions)) {
            throw new RuntimeException('DELETE without WHERE is not allowed.');
        }

        $sql  = "DELETE FROM `{$this->table}` WHERE " . implode(' AND ', $this->conditions);
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param($this->bindTypes, ...$this->bindings);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function tableExists(mysqli $conn, string $table): bool
    {
        $stmt = $conn->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?'
        );
        $stmt->bind_param('s', $table);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_row();
        $stmt->close();
        return (int) ($row[0] ?? 0) > 0;
    }

    private function executeQuery(string $sql, string $types, array $bindings): ?\mysqli_result
    {
        if (empty($bindings)) {
            $result = $this->conn->query($sql);
            return $result instanceof \mysqli_result ? $result : null;
        }

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param($types, ...$bindings);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ?: null;
    }

    private function inferType(mixed $value): string
    {
        return match (true) {
            is_int($value)   => 'i',
            is_float($value) => 'd',
            default          => 's',
        };
    }
}
