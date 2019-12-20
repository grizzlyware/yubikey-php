<?php

namespace Grizzlyware\YubiKey\Exceptions\Yubico;
use Grizzlyware\YubiKey\Exceptions\Exception;

class MissingParameterException extends Exception
{
	const RESPONSE_CODE = 'MISSING_PARAMETER';
	protected $message = 'The request lacks a parameter.';
}