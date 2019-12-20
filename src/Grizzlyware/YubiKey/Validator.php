<?php

namespace Grizzlyware\YubiKey;

use Grizzlyware\YubiKey\Exceptions\Exception;
use Grizzlyware\YubiKey\Exceptions\InvalidResponseException;
use Grizzlyware\YubiKey\Exceptions\InvalidResponseHashException;
use Grizzlyware\YubiKey\Exceptions\InvalidResponseTimestampException;
use Grizzlyware\YubiKey\Exceptions\TransportException;
use Grizzlyware\YubiKey\Exceptions\Yubico\BackendErrorException;
use Grizzlyware\YubiKey\Exceptions\Yubico\BadOtpException;
use Grizzlyware\YubiKey\Exceptions\Yubico\BadSignatureException;
use Grizzlyware\YubiKey\Exceptions\Yubico\MissingParameterException;
use Grizzlyware\YubiKey\Exceptions\Yubico\NoSuchClientException;
use Grizzlyware\YubiKey\Exceptions\Yubico\NotEnoughAnswersException;
use Grizzlyware\YubiKey\Exceptions\Yubico\OperationNotAllowedException;
use Grizzlyware\YubiKey\Exceptions\Yubico\ReplayedOtpException;
use Grizzlyware\YubiKey\Exceptions\Yubico\ReplayedRequestException;

class Validator
{
	private $clientId;
	private $secretKey;
	private $tolerance = 15;

	const VERIFY_API_1_URL = 'https://api.yubico.com/wsapi/2.0/verify';
	const RESPONSE_OK = 'OK';

	public function __construct($clientId, $secretKey = null)
	{
		$this->clientId = $clientId;
		if($secretKey) $this->secretKey = base64_decode($secretKey);
	}

	protected static function randomString($length = 16)
	{
		// Thank you to Taylor Otwell / Laravel for this function
		$string = '';

		while (($len = strlen($string)) < $length) {
			$size = $length - $len;

			$bytes = random_bytes($size);

			$string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
		}

		return $string;
	}

	protected static function generateNonce()
	{
		return substr(md5(microtime()), 0, 16) . self::randomString(16);
	}

	protected function getVerificationUrl()
	{
		return self::VERIFY_API_1_URL;
	}

	protected static function orderQueryString($query)
	{
		ksort($query);
		return $query;
	}

	protected function signPayload($query)
	{
		return base64_encode(hash_hmac('sha1', urldecode(http_build_query(self::orderQueryString($query))), $this->secretKey, true));
	}

	public function verifyOtp($otp)
	{
		$query = [
			'id' => $this->clientId,
			'otp' => $otp,
			'nonce' => self::generateNonce()
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);

		if($this->secretKey)
		{
			curl_setopt($ch, CURLOPT_URL, $this->getVerificationUrl() . '?' . http_build_query($query) . '&h=' . urlencode($this->signPayload($query)));
		}
		else
		{
			curl_setopt($ch, CURLOPT_URL, $this->getVerificationUrl() . '?' . http_build_query($query));
		}

		$response = curl_exec($ch);

		if (!$response) throw new TransportException('cURL Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
		if(curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) throw new TransportException('Non 200 response code received from Yubico verifiation server');
		curl_close($ch);

		$formattedResponse = [];

		foreach(explode("\n",$response) as $responseLine)
		{
			$lineParts = explode('=', $responseLine, 2);
			if(count($lineParts) < 2) continue;

			$lineParts[0] = trim($lineParts[0]);
			$lineParts[1] = trim($lineParts[1]);

			if($lineParts[0]) $formattedResponse[$lineParts[0]] = $lineParts[1];
		}

		// Well?
		switch($formattedResponse['status'])
		{
			case BackendErrorException::RESPONSE_CODE:
				throw new BackendErrorException();

			case BadOtpException::RESPONSE_CODE:
				throw new BadOtpException();

			case BadSignatureException::RESPONSE_CODE:
				throw new BadSignatureException();

			case MissingParameterException::RESPONSE_CODE:
				throw new MissingParameterException();

			case NoSuchClientException::RESPONSE_CODE:
				throw new NoSuchClientException();

			case NotEnoughAnswersException::RESPONSE_CODE:
				throw new NotEnoughAnswersException();

			case OperationNotAllowedException::RESPONSE_CODE:
				throw new OperationNotAllowedException();

			case ReplayedOtpException::RESPONSE_CODE:
				throw new ReplayedOtpException();

			case ReplayedRequestException::RESPONSE_CODE:
				throw new ReplayedRequestException();
		}

		// Check the nonce and OTP
		if(!isset($formattedResponse['nonce'])) throw new InvalidResponseException('Response does not contain a nonce');
		if(!isset($formattedResponse['otp'])) throw new InvalidResponseException('Response does not contain a OTP');
		if($query['nonce'] != $formattedResponse['nonce']) throw new InvalidResponseException('Responses nonce does not match requests');
		if($query['otp'] != $formattedResponse['otp']) throw new InvalidResponseException('Responses OTP does not match requests');

		// Extract the response hash for verification?
		if($this->secretKey)
		{
			$responseToValidate = $formattedResponse;
			unset($responseToValidate['h']);
			$expectedHash = $this->signPayload($responseToValidate);
			if($formattedResponse['h'] != $expectedHash) throw new InvalidResponseHashException('Invalid hash response from Yubico');
		}

		// Verify the time
		if(!isset($formattedResponse['t'])) throw new InvalidResponseTimestampException('Response timestamp not set');
		$currentTimestamp = time();
		$responseTime = \DateTime::createFromFormat('Y-m-d\TH:i:s+', $formattedResponse['t'], new \DateTimeZone('UTC'));
		if($responseTime->getTimestamp() > $currentTimestamp + $this->tolerance) throw new InvalidResponseTimestampException('Response timestamp is out of bounds (ahead)');
		if($responseTime->getTimestamp() < $currentTimestamp - $this->tolerance) throw new InvalidResponseTimestampException('Response timestamp is out of bounds (behind)');

		// Well?
		switch($formattedResponse['status'])
		{
			case self::RESPONSE_OK:
				return true;

			default:
				throw new Exception('Unexpected response from Yubico: ' . $formattedResponse['status']);
		}
	}

	public function setTolerance($tolerance)
	{
		$this->tolerance = $tolerance;
	}
}


