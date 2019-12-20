<?php

namespace Grizzlyware\YubiKey\Exceptions\Yubico;
use Grizzlyware\YubiKey\Exceptions\Exception;

class BackendErrorException extends Exception
{
	const RESPONSE_CODE = 'BACKEND_ERROR';
	protected $message = 'Unexpected error in our server. Please contact us if you see this error.';
}

