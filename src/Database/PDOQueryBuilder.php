<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use PDO;

class PDOQueryBuilder
{
    protected $table;
    protected $connection;
    protected $conditions;
    protected $values;
    protected $statement;

    public function __construct(DatabaseConnectionInterface $connection)
    {
        $this->connection = $connection->getConnection();
    }

    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }

    public function create(array $data)
    {
        $placeHolder = [];
        foreach ($data as $column => $value) {
            $placeHolder[] = '?';
        }
        $fields = implode(',', array_keys($data));
        $placeHolder = implode(',', $placeHolder);
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeHolder})";
        $this->values = array_values($data);
        $this->execute($sql);
        return (int)$this->connection->lastInsertId();
    }

    public function where(string $column, string $value)
    {
        if (is_null($this->conditions)) {
            $this->conditions .= "{$column}=?";
        } else {
            $this->conditions .= " and {$column}=?";
        }
        $this->values[] = $value;
        return $this;
    }

    public function update(array $data)
    {
        $fields = [];
        foreach ($data as $column => $value) {
            $fields[] .= "{$column}='{$value}'";
        }
        $fields = implode(',', $fields);
        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->conditions};";
        $this->execute($sql);
        return $this->statement->rowCount();
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->conditions}";
        $this->execute($sql);
        return $this->statement->rowCount();
    }

    public function get(array $columns = ['*'])
    {
        $columns = implode(',', $columns);
        $sql = "SELECT {$columns} FROM {$this->table} WHERE {$this->conditions}";
        $this->execute($sql);
        return $this->statement->fetchAll();
    }

    public function first()
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->conditions}";
        $this->execute($sql);
        $result = $this->statement->fetch();
        return empty($result) ? null : $result;
    }

    public function find(int $id)
    {
        return $this->where('id', $id)->first();
    }

    public function truncateAllTable()
    {
        $this->execute("SHOW TABLES");
        foreach ($this->statement->fetchAll(PDO::FETCH_COLUMN) as $table) {
            $this->connection->prepare("TRUNCATE TABLE `{$table}`")->execute();
        }

    }

    private function execute(string $sql)
    {
        $this->statement = $this->connection->prepare($sql);
        $result = $this->statement->execute($this->values);
        $this->values = [];
        return $this;
    }

    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    public function rollback()
    {
        $this->connection->rollback();
    }
}
