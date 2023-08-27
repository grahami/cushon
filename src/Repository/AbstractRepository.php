<?php

namespace App\Repository;

use App\Entity\Interface\EntityInterface;
use App\Exception\DatabaseException;
use App\Exception\EntityNotFoundException;
use App\Repository\Interface\RepositoryInterface;
use App\Service\EntityService;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

abstract class AbstractRepository implements RepositoryInterface
{
    protected Connection $connection;

    abstract public function findById(int $id): EntityInterface;

    /**
     * @param array<mixed> $params
     * @return array<mixed>
     */
    abstract public function findByParameters(array $params): array;

    /**
     * @param array<mixed> $params
     */
    abstract public function create(array $params): EntityInterface;

    /**
     * @param array<mixed> $params
     * @return array<mixed>
     */
    protected function fetchAll(string $sql, array $params): array
    {
        try {
            return $this->connection->fetchAllAssociative($sql, $params);
        } catch (Exception $exception) {
            // Log details of exception for debugging
            throw new DatabaseException($exception->getMessage());
        }
    }

    /**
     * @param array<mixed> $params
     * @return array<mixed>
     * @throws DatabaseException
     * @throws EntityNotFoundException
     */
    protected function fetchOne(string $sql, array $params): array
    {
        $result = [];
        try {
            $result = $this->connection->fetchAssociative($sql, $params);
            if (!empty($result)) {
                return $result;
            } else {
                throw new EntityNotFoundException('Entity not found');
            }
        } catch (Exception $exception) {
            // Log details of exception for debugging
            throw new DatabaseException($exception->getMessage());
        }
    }

    /**
     * @param array<mixed> $params
     * @throws DatabaseException
     */
    protected function insert(string $sql, array $params): EntityInterface
    {
        try {
            $result = $this->connection->executeStatement($sql, $params);
            $lastInsertId = $this->connection->lastInsertId();
            return $this->findById($lastInsertId);
        } catch (Exception $exception) {
            // Log details of exception for debugging
            throw new DatabaseException($exception->getMessage());
        }
    }

    /**
     * @param array<mixed> $params
     */
    protected function addWhereClause(string $sql, array $params): string
    {
        if (!empty($params)) {
            $sql = $sql . ' WHERE';
            foreach($params as $fieldName => $fieldValue) {
                $sql .= ' `' . $fieldName . '` = :' . $fieldName;
            }
        }
        return $sql;
    }

}