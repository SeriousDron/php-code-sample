<?php

namespace Smtt\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Smtt\dto\MoDates;
use Smtt\Exception\QueryException;
use Smtt\Exception\UnexpectedValueException;
use Smtt\Traits\Logger;

class Statistics
{
    use Logger;

    /** @var Connection */
    protected $connection;

    /**
     * Statistics constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get Mo count created in last 15 minutes
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getMoCountLast15m()
    {
        try {
            $stmt = $this->connection->query('SELECT count(*) as `count` FROM `mo` WHERE `created_at` > NOW() - INTERVAL 15 MINUTE');
            $data = $stmt->fetch();
        } catch (DBALException $e) {
            $this->logger->error('Error fetching mo count', ['exception' => $e]);
            throw new QueryException('Error fetching mo count', 500, $e);
        }
        return $data['count'];
    }

    /**
     * Get min and max dates for last N mo
     * @param int $lastMoCount
     * @return MoDates
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getDates4LastMo($lastMoCount = 10000)
    {
        $lastMoCount = (int)$lastMoCount;
        if ($lastMoCount < 1) {
            throw new UnexpectedValueException('Invalid mo count requested');
        }
        try {
            $stmt = $this->connection->prepare('
              SELECT min(`created_at`) as `minDate`, max(`created_at`) as `maxDate`
              FROM (
                SELECT *
                FROM `mo`
                ORDER BY `id` DESC
                LIMIT ? )
              `last`');
            $stmt->bindValue(1, $lastMoCount, \PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(\PDO::FETCH_ASSOC);

            $result = new MoDates();
            $result->minDate = new \DateTime($data['minDate']);
            $result->maxDate = new \DateTime($data['maxDate']);
            return $result;
        } catch (DBALException $e) {
            $this->logger->error('Error querying mo time span', ['exception' => $e]);
            throw new QueryException('Error querying mo time span', 500 ,$e);
        }
    }
}
