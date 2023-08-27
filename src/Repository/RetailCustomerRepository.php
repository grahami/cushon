<?php

namespace App\Repository;

use App\Entity\Interface\EntityInterface;
use App\Entity\RetailCustomer;
use App\Exception\DatabaseException;
use App\Exception\TodoException;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

class RetailCustomerRepository extends AbstractRepository
{
    public function __construct(Connection $connection, private RetailCustomer $retailCustomer)
    {
        $this->connection = $connection;
    }

    public function findById(int $id): EntityInterface
    {
        $sql = '/* RetailCustomerRepository - findById */ ';
        $sql .= 'SELECT * FROM `retail_customers` WHERE `id` = :id';
        $rawData = $this->fetchOne($sql, ['id' => $id]);
        $id = $rawData['id'] ?? -1;
        $firstName = $rawData['first_name'] ?? '';
        $lastName = $rawData['last_name'] ?? '';
        $emailAddress = $rawData['email_address'] ?? '';
        $dob = DateTimeImmutable::createFromFormat('Y-m-d', $rawData['dob'] ?? '')->setTime(0, 0, 0);

        // Creation with only parameters for properties that are required
        return $this->retailCustomer->create($id, $firstName, $lastName, $emailAddress, $dob);
    }

    public function findByParameters(array $params): array
    {
        $result = [];
        $sql = '/* RetailCustomerRepository - findByParameters */ ';
        $sql .= 'SELECT * FROM `retail_customers`';
        $sql = $this->addWhereClause($sql, $params);

        $rowData = $this->fetchAll($sql, $params);
        foreach ($rowData as $row) {
            // duplicated code here - it would be better to split this logic out to another method
            $id = $row['id'] ?? -1;
            $firstName = $row['first_name'] ?? '';
            $lastName = $row['last_name'] ?? '';
            $emailAddress = $row['email_address'] ?? '';
            $dob = DateTimeImmutable::createFromFormat('Y-m-d', $rowData['dob'] ?? '')->setTime(0, 0, 0);

            // Creation with only parameters for properties that are required
            $result[] = $this->retailCustomer->create($id, $firstName, $lastName, $emailAddress, $dob);
        }
        return $result;
    }

    public function create(array $params): EntityInterface
    {
        // TODO: Implement
        throw new TodoException(__CLASS__ . ' - ' . __METHOD__ . ' - Not yet implemented');
    }

    /**
     * @return array<mixed> $params
     * @throws DatabaseException
     */
    public function getFullInvestmentReport(int $id): array
    {
        $result = [];
        $sql = '/* RetailCustomerRepository - getFullInvestmentReport */ ';
        $sql .= 'SELECT rc.id, rc.first_name, rc.last_name, rc.dob, rc.email_address,';
        $sql .= ' isa.name AS isa_name, inv.invested_at, inv.lump_sum';
        $sql .= ' FROM `retail_customers` rc';
        $sql .= ' JOIN `investments` inv ON rc.`id` = inv.`retail_customer_id`';
        $sql .= ' JOIN `isas` isa ON inv.`isa_id` = isa.`id`';
        $sql .= ' WHERE rc.`id` = :id';

        // TODO: Parse the flat rows into an array that represents a heirarchy of the customer, the isa's and
        // the investments
        return $this->fetchAll($sql, ['id' => $id]);
    }

}