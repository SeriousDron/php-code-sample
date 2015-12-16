<?php

namespace Smtt\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Smtt\dto\MoRequest;
use Smtt\dto\RegisterResult;
use Smtt\Traits\Logger;

class InstantRegister implements RegisterMoInterface
{
    use Logger;

    /** @var CommandRunner */
    protected $runner;

    /** @var Connection */
    protected $dbConnection;

    /**
     * InstantRegister constructor.
     * @param Connection $dbConnection
     */
    public function __construct(CommandRunner $runner, Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
        $this->runner = $runner;
        $this->runner->setCommand('./bin/registermo');
    }

    /**
     * Register Mo request through command runned. Check result and save hash to DB
     *
     * @param MoRequest $moRequest
     * @return RegisterResult
     * @throws DBALException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function register(MoRequest $moRequest)
    {
        $hash = $this->runner->run(json_encode($moRequest));

        if (!$this->isValidHash($hash)) {
            return RegisterResult::fail('Invalid register mo result');
        }
        try {
            $this->dbConnection->insert(
                'mo',
                array(
                    'msisdn' => $moRequest->msisdn,
                    'operatorid' => $moRequest->operatorid,
                    'shortcodeid' => $moRequest->shortcodeid,
                    'text' => $moRequest->text,
                    'auth_token' => $hash,
                ),
                array(
                    'operatorid' => \PDO::PARAM_INT,
                    'shortcodeid' => \PDO::PARAM_INT,
                )
            );
        } catch (DBALException $e) {
            $this->logger->error('DBAL exception occured', ['exception' => $e]);
            return RegisterResult::fail('DBAL exception occured');
        }
        return RegisterResult::success();
    }

    /**
     * Check processing result is ok
     *
     * @param string $hash
     * @return bool
     */
    protected function isValidHash($hash)
    {
        if (strlen($hash) != 24 || preg_match('#^[a-z0-9_\-]{24}$#iu', $hash) != 1) {
            return false;
        }
        return true;
    }
}
