<?php
namespace App\Services\Security;
use Exception;

/**
 * |--------------------------------------------------------------------------
 * | RSA Security Service
 * |--------------------------------------------------------------------------
 * |
 * | Handles RSA decryption using the private key.
 * |
 * | Frontend:
 * |     JSEncrypt
 * |
 * | Backend:
 * |     OpenSSL
 * |
 * | Developer : Snehal Vasava
 * |--------------------------------------------------------------------------
 */
class RSAService{
    /**
     * |--------------------------------------------------------------------------
     * | Private Key Path
     * |--------------------------------------------------------------------------
     */
    private static function privateKeyPath(): string{
        return storage_path('keys/private.pem');
    }

    /**
     * |--------------------------------------------------------------------------
     * | Get Private Key
     * |--------------------------------------------------------------------------
     */
    private static function getPrivateKey(): string{
        $path = self::privateKeyPath();
        if (!file_exists($path)) {
            throw new Exception(
                'RSA private key not found.'
            );
        }
        return file_get_contents($path);
    }

    /**
     * |--------------------------------------------------------------------------
     * | Decrypt RSA Data
     * |--------------------------------------------------------------------------
     *
     * @param string $encryptedData
     *
     * @return string
     *
     * |--------------------------------------------------------------------------
     */
    public static function decrypt(string $encryptedData): string {
        if (empty($encryptedData)) {
            throw new Exception('Encrypted data is empty.');
        }

        $privateKey = openssl_pkey_get_private(
            self::getPrivateKey()
        );

        if (!$privateKey) {
            throw new Exception('Unable to load private key.');
        }

        $decrypted = '';
        $success = openssl_private_decrypt(
            base64_decode($encryptedData),
            $decrypted,
            $privateKey,
            OPENSSL_PKCS1_PADDING
        );

        if (!$success) {
            throw new Exception(
                'RSA decryption failed.'
            );
        }

        return trim($decrypted);
    }

    /**
     * |--------------------------------------------------------------------------
     * | Multiple Field Decryption
     * |--------------------------------------------------------------------------
     *
     * Example:
     *
     * RSAService::decryptArray([
     *     'username' => $request->username,
     *     'password' => $request->password
     * ]);
     *
     * |--------------------------------------------------------------------------
     */
    public static function decryptArray(array $data): array {
        $result = [];
        foreach ($data as $key => $value) {
            $result[$key] = self::decrypt($value);
        }
        return $result;
    }
}
