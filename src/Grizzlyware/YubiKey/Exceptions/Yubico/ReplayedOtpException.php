<?php

namespace Grizzlyware\YubiKey\Exceptions\Yubico;
use Grizzlyware\YubiKey\Exceptions\Exception;

class ReplayedOtpException extends Exception
{
	const RESPONSE_CODE = 'REPLAYED_OTP';
	protected $message = 'The OTP has already been seen by the service.';
}