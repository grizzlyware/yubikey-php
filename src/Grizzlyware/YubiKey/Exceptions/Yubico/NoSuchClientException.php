<?php

namespace Grizzlyware\YubiKey\Exceptions\Yubico;
use Grizzlyware\YubiKey\Exceptions\Exception;

class NoSuchClientException extends Exception
{
	const RESPONSE_CODE = 'NO_SUCH_CLIENT';
	protected $message = 'The request id does not exist.';
}