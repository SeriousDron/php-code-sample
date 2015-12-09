<?php

namespace Smtt;

use Smtt\dto\MoRequest;
use Smtt\Exception\NotEnoughParametersException;
use Smtt\Exception\UnexpectedValueException;
use Symfony\Component\HttpFoundation\Request;

class RequestProcessor
{
    /**
     * Check request validity and create dto
     *
     * @param Request $request
     * @return MoRequest
     * @throws UnexpectedValueException
     * @throws NotEnoughParametersException
     */
    public function process(Request $request)
    {
        $this->checkFieldsExists($request);
        $result = new MoRequest();
        $result->msisdn = $this->stringify($request, 'msisdn');
        $result->operatorid = $this->numeric($request, 'operatorid');
        $result->shortcodeid = $this->numeric($request, 'shortcodeid');
        $result->text = $this->stringify($request, 'text');

        return $result;
    }

    /**
     * Check all necessary field are set
     *
     * @param Request $request
     * @throws NotEnoughParametersException
     */
    protected function checkFieldsExists(Request $request)
    {
        $moRequest = new MoRequest();
        foreach ($moRequest as $field => $value) {
            if ($request->get($field, false) === false) {
                throw new NotEnoughParametersException("Required parameter {$field} is missing");
            }
        }
    }

    /**
     * Convert field value to string and validate it
     *
     * @param Request $request
     * @param string $key
     * @return string
     * @internal param mixed $value
     */
    protected function stringify(Request $request, $key)
    {
        $result = (string)$request->get($key);
        if (strlen($result) == 0) {
            throw new UnexpectedValueException("Empty string is not valid valued of {$key}");
        }
        return $result;
    }

    /**
     * Convert field value to unsigned int and validate it
     *
     * @param Request $request
     * @param string $key
     * @return int
     * @internal param mixed $value
     */
    protected function numeric(Request $request, $key)
    {
        $result = $request->get($key);
        if (!is_numeric($result) || (int)$result != $result || (int)$result < 0) {
            throw new UnexpectedValueException("{$key} should be an unsigned integer");
        }
        return (int)$result;
    }
}
