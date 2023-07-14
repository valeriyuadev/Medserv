<?php

include 'IPdoImplementation.php';

class MysqlPdo extends PDO implements IPdoImplementation
{
    private ?PDOStatement $stmt = null;

    /**
     * @throws PDOException
     */
    public function __construct($username, $password, $database, $host)
    {
        parent::__construct(
            "mysql:host=$host;port=3306;dbname=$database",
            $username,
            $password
        );
    }

    public function getStmt(): null|PDOStatement
    {
        return $this->stmt;
    }

    /**
     * @throws PDOException
     */
    public function dql(string $sql): void
    {
        $this->stmt = $this->query($sql);
    }

    public function dml(string $sql, array $params = []): void
    {
        $this->stmt = $this->prepare($sql);
        $this->stmt->execute($params);
    }

    /**
     * @throws PDOException
     */
    public function dmlTransaction(string $sql, array $params): int
    {
        try {
            $this->beginTransaction();
            $this->dml($sql, $params);
            $lastInsertId = $this->lastInsertId();
            $this->commit();

            return (int)$lastInsertId;
        } catch (PDOException $exception) {
            $this->rollBack();
            throw $exception;
        }
    }
}
 
