<?php

/**
 * mysql Class
 * You are required to complete this unfinished class so that test.php runs as it is meant to
 * You may also add in any error reporting or analytics that you feel would improve this class
 **/

require("./MysqlPdo.php");

class mysql_db
{
    private ?IPdoImplementation $implementationPdo = null;

    public function __construct(
        private readonly string $username,
        private readonly string $password,
        private readonly string $database,
        private readonly string $host
    ) {
        $this->getPdo();
    }

    function __destruct()
    {
        $this->implementationPdo = null;
    }

    /**
     * @throws PDOException
     */
    public function query(string $sql): void
    {
        $this->getPdo()->dql($sql);
    }

    /**
     * @throws PDOException
     */
    public function insert(string $table, array $values): int
    {
        $sql = $this->prepareData('INSERT', $table, $values);

        return $this->getPdo()->dmlTransaction($sql, $values);
    }

    /**
     * @throws PDOException
     */
    public function update(string $table, array $values, string $where): void
    {
        $sql = $this->prepareData('UPDATE', $table, $values, $where);

        $this->getPdo()->dmlTransaction($sql, $values);
    }

    /**
     * @throws PDOException
     */
    public function fetchrow(): mixed
    {
        return $this->getStmt()?->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @throws PDOException
     */
    public function fetchall(): array
    {
        return $this->getStmt()?->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function numrows(): int
    {
        return $this->getStmt()?->rowCount() ?: 0;
    }

    private function getStmt(): null|PDOStatement
    {
        return $this->getPdo()->getStmt();
    }

    /**
     * @throws PDOException
     */
    private function getPdo(): IPdoImplementation
    {
        if (!$this->implementationPdo) {
            $this->implementationPdo = new MysqlPdo($this->username, $this->password, $this->database, $this->host);
        }

        if ($this->implementationPdo) {
            return $this->implementationPdo;
        }

        throw new PDOException('PDO is not available');
    }

    /**
     * @throws PDOException
     */
    private function prepareData(string $operation, string $table, array $values, string $where = ''): string
    {
        $tableName = trim($table);

        if (!strlen($tableName)) {
            throw new InvalidArgumentException('name of table is empty');
        }

        if (!count($values)) {
            throw new InvalidArgumentException('values are empty');
        }

        switch (strtoupper($operation)) {
            case 'INSERT':
                $fields = array_keys($values);
                $columnNames = '(`' . implode('`, `', $fields) . '`)';
                $columnMarks = '(:' . implode(', :', $fields) . ')';
                $sql = "INSERT INTO `$tableName` $columnNames VALUES $columnMarks";
                break;
            case 'UPDATE':
                $columns = [];
                foreach ($values as $k => $v) {
                    $columns[] = "$k = :$k";
                }

                $columns = implode(',', $columns);
                $sqlWhere = trim($where);

                if ($sqlWhere) {
                    $sqlWhere = "WHERE $sqlWhere ";
                }

                $sql = "UPDATE $tableName SET $columns $sqlWhere";
                break;
            default:
                throw new InvalidArgumentException('what?');
        }

        return $sql;
    }
}
 
