<?php

use PHPUnit\Framework\TestCase;

final class YubiKeyTest extends TestCase
{
	public function testInvalidOtp()
	{
		$this->expectException(Grizzlyware\YubiKey\Exceptions\Yubico\BadOtpException::class);
		$yubiKeyValidator = new \Grizzlyware\YubiKey\Validator('invalid', 'invalid');
		$yubiKeyValidator->verifyOtp('invalid_otp');
	}
}

