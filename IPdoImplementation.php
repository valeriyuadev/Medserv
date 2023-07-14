<?php

interface IPdoImplementation
{
    public function getStmt(): null|PDOStatement;
    public function dql(string $sql): void;
    public function dml(string $sql, array $params = []): void;
    public function dmlTransaction(string $sql, array $params): int;
}