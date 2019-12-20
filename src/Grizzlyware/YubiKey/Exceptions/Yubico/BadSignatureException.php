<?php

namespace Grizzlyware\YubiKey\Exceptions\Yubico;
use Grizzlyware\YubiKey\Exceptions\Exception;

class BadSignatureException extends Exception
{
	const RESPONSE_CODE = 'BAD_SIGNATURE';
	protected $message = 'The HMAC signature verification failed.';
}