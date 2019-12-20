<?php

namespace Grizzlyware\YubiKey\Exceptions\Yubico;
use Grizzlyware\YubiKey\Exceptions\Exception;

class BadOtpException extends Exception
{
	const RESPONSE_CODE = 'BAD_OTP';
	protected $message = 'The OTP is invalid format.';
}