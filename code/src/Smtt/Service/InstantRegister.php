<?php

namespace Smtt\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Smtt\dto\MoRequest;

class InstantRegister implements RegisterMoInterface
{
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
     * @return boolean
     * @throws DBALException
     */
    public function register(MoRequest $moRequest)
    {
        $hash = $this->runner->run(json_encode($moRequest));

        if (!$this->isValidHash($hash)) {
            return false;
        }

        $this->dbConnection->insert('mo', array(
            'msisdn' => $moRequest->msisdn,
            'operatorid' => $moRequest->operatorid,
            'shortcodeid' => $moRequest->shortcodeid,
            'text' => $moRequest->text,
            'auth_token' => $hash,
            'created_at' => null,
        ));

        return true;
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
