<?php

namespace Grizzlyware\YubiKey\Exceptions\Yubico;
use Grizzlyware\YubiKey\Exceptions\Exception;

class ReplayedRequestException extends Exception
{
	const RESPONSE_CODE = 'REPLAYED_REQUEST';
	protected $message = 'Server has seen the OTP/Nonce combination before';
}