<?php

namespace Smtt\RegisterMo;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Smtt\dto\MoRequest;

class InstantRegister implements RegisterMoInterface
{
    /** @var Connection */
    protected $dbConnection;

    /**
     * InstantRegister constructor.
     * @param Connection $dbConnection
     */
    public function __construct(Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }


    /**
     * Register Mo request and save hash to DB
     *
     * @param MoRequest $moRequest
     * @return boolean
     * @throws DBALException
     */
    public function register(MoRequest $moRequest)
    {
        $params = escapeshellarg(json_encode($moRequest));
        $hash = shell_exec("./registermo {$params}");

        if (!$this->isValidHash($hash)) {
            return false;
        }

        $this->dbConnection->insert('mo', array(
            'msisdn'        => $moRequest->msisdn,
            'operatorid'    => $moRequest->operatorid,
            'shortcodeid'   => $moRequest->shortcodeid,
            'text'          => $moRequest->text,
            'auth_token'    => $hash,
            'created_at'    => null,
        ));

        return true;
    }

    private function isValidHash($hash)
    {
    }
}
