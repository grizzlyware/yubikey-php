<?php

namespace Grizzlyware\YubiKey\Exceptions\Yubico;
use Grizzlyware\YubiKey\Exceptions\Exception;

class OperationNotAllowedException extends Exception
{
	const RESPONSE_CODE = 'OPERATION_NOT_ALLOWED';
	protected $message = 'The request id is not allowed to verify OTPs.';
}

