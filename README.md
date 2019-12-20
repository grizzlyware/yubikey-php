<p align="center"><img src="./assets/images/yubico_logo.png" alt="Yubico logo" /></p>

# YubiKey Validation Library
This library allows you to validate YubiKey OTPs (one time passwords) easily. It's written in pure PHP, requiring only cURL to connect to Yubico's servers.

## Requirements
PHP 5.6+
cURL

## Example

```
require 'vendor/autoload.php';

// These can be obtained from Yubico: https://upgrade.yubico.com/getapikey/
$clientId = 12345; // Replace with your client ID
$clientSecret = 'YOUR_SECRET'; // Replace with your client secret. This can also be null or omitted, but the requests and responses will not be signed

// This will come from your user
$otpToValidate = 'OTP_GENERATED_BY_HARDWARE_YUBIKEY';

// Create the validator instance
$yubiKeyValidator = new \Grizzlyware\YubiKey\Validator($clientId, $clientSecret);

try
{
    // Check the OTP
    $yubiKeyValidator->verifyOtp($otpToValidate);

    // OTP was validated successfully
}
catch(Grizzlyware\YubiKey\Exceptions\Yubico\BadOtpException $e)
{
    // YubiKey failed validation
}
catch(Grizzlyware\YubiKey\Exceptions\Exception $e)
{
    // Other error relating to Yubico validation
}
catch(\Exception $e)
{
    // PHP level exception
}
```

### Validating the key is the same between uses
The first 12 digits of a YubiKey OTP are always the same and unique to that key. That segment can be stored in your app to check against incoming OTPs before validating the OTP with Yubico. You can attach multiple YubiKeys to a user by storing multiple OTP prefixes.

## Security Vulnerabilities
If you discover a security vulnerability within this project, please contact Grizzlyware Ltd directly (contact@grizzlyware.com). All security vulnerabilities will be promptly addressed.


