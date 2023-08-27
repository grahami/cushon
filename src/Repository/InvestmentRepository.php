<?php

namespace App\Repository;

use App\Entity\Interface\EntityInterface;
use App\Entity\Investment;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

class InvestmentRepository extends AbstractRepository
{
    public function __construct(Connection $connection, private Investment $investment)
    {
        $this->connection = $connection;
    }

    public function findById(int $id): EntityInterface
    {
        $sql = '/* InvestmentRepository - findById */ ';
        $sql .= 'SELECT * FROM `investments` WHERE `id` = :id';
        $rawData = $this->fetchOne($sql, ['id' => $id]);
        $id = $rawData['id'] ?? -1;
        $retailCustomerId = $rawData['retail_customer_id'] ?? '';
        $isaId = $rawData['isa_id'] ?? '';
        $investedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $rawData['invested_at'] ?? '');
        if ($investedAt === false) {
            $investedAt = null;
        }
        $lumpSum = $rawData['lump_sum'] ?? 0;
        $monthlySum = $rawData['monthly_sum'] ?? 0;

        return $this->investment->create($id, $retailCustomerId, $isaId, $investedAt, $lumpSum, $monthlySum);
    }

    public function findByParameters(array $params): array
    {
        $result = [];
        $sql = '/* InvestmentRepository - findByParameters */ ';
        $sql .= 'SELECT * FROM `investments`';
        $sql = $this->addWhereClause($sql, $params);

        $rowData = $this->fetchAll($sql, $params);
        foreach ($rowData as $row) {
            // duplicated code here - it would be better to split this logic out to another method
            $id = $row['id'] ?? -1;
            $retailCustomerId = $row['retail_customer_id'] ?? '';
            $isaId = $row['isa_id'] ?? '';
            $investedAt = DateTimeImmutable::createFromFormat('Y-m-d H-i-s', $row['invested_at'] ?? '');
            if ($investedAt === false) {
                $investedAt = null;
            }
            $lumpSum = $row['lump_sum'] ?? 0;
            $monthlySum = $row['monthly_sum'] ?? 0;

            $result[] = $this->investment->create($id, $retailCustomerId, $isaId, $investedAt, $lumpSum, $monthlySum);
        }
        return $result;
    }

    public function create(array $params): EntityInterface
    {
        $sql = '/* InvestmentRepository - create */ ';
        $sql .= 'INSERT INTO `investments` (`retail_customer_id`, `isa_id`, `invested_at`, `lump_sum`, `monthly_sum`)';
        $sql .= ' VALUES (:retail_customer_id, :isa_id, :invested_at, :lump_sum, :monthly_sum)';

        // TODO: Do validation that $params has all required fields and no extras

        // TODO: Move modifiers to a separate method for re-use
        $params['invested_at'] = $params['invested_at']->format('Y-m-d H:i:s');

        return $this->insert($sql, $params);
    }
}