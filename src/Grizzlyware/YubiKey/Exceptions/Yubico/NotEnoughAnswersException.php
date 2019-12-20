<?php

namespace Grizzlyware\YubiKey\Exceptions\Yubico;
use Grizzlyware\YubiKey\Exceptions\Exception;

class NotEnoughAnswersException extends Exception
{
	const RESPONSE_CODE = 'NOT_ENOUGH_ANSWERS';
	protected $message = 'Server could not get requested number of syncs during before timeout';
}