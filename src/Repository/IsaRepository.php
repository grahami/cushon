<?php

namespace App\Repository;

use App\Entity\Isa;
use App\Entity\Interface\EntityInterface;
use App\Exception\TodoException;
use Doctrine\DBAL\Connection;

class IsaRepository extends  AbstractRepository
{
    public function __construct(Connection $connection, private Isa $isa) {
        $this->connection = $connection;
    }

    public function findById(int $id): EntityInterface
    {
        $sql = '/* IsaRepository - findById */ ';
        $sql .= 'SELECT * FROM `isas` WHERE `id` = :id';

        $rawData = $this->fetchOne($sql, ['id' => $id]);
        $id = $rawData['id'] ?? -1;
        $name = $rawData['name'] ?? '';
        $type = $rawData['type'] ?? '';
        $riskDetails = $rawData['risk_details'] ?? '';
        $chargeDetails = $rawData['charge_details'] ?? '';

        return $this->isa->create($id, $name, $type, $riskDetails, $chargeDetails);
    }

    public function findByParameters(array $params): array
    {
        $result = [];
        $sql = '/* IsaRepository - findByParameters */ ';
        $sql .= 'SELECT * FROM `isas`';
        $sql = $this->addWhereClause($sql, $params);

        $rowData = $this->fetchAll($sql, $params);
        foreach ($rowData as $row) {
            // duplicated code here - it would be better to split this logic out to another method
            $id = $row['id'] ?? -1;
            $name = $row['name'] ?? '';
            $type = $row['type'] ?? '';
            $riskDetails = $row['risk_details'] ?? '';
            $chargeDetails = $row['charge_details'] ?? '';
            $result[] = $this->isa->create($id, $name, $type, $riskDetails, $chargeDetails);
        }
        return $result;
    }

    public function create(array $params): EntityInterface
    {
        // TODO: Implement
        throw new TodoException(__CLASS__ . ' - ' . __METHOD__ . ' - Not yet implemented');
    }

}