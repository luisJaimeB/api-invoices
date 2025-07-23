<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AuthService
{
    /**
     * Validate the authentication credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(array $auth): bool
    {
        $login = $auth['login'] ?? null;
        $tranKey = $auth['tranKey'] ?? null;
        $nonce = $auth['nonce'] ?? null;
        $seed = $auth['seed'] ?? null;

        Log::info('AuthService: Validando credenciales de autenticación.', [
            'received' => $auth
        ]);

        $expectedLogin = config('services.api_auth.login');
        $secretKey = config('services.api_auth.secret');

        if (!$login || !$tranKey || !$nonce || !$seed) {
            Log::warning('AuthService: Faltan campos en la autenticación.', [
                'received' => $auth
            ]);
            return false;
        }

        if ($login !== $expectedLogin) {
            Log::warning('AuthService: Login incorrecto.', [
                'login_received' => $login,
                'login_expected' => $expectedLogin,
            ]);
            return false;
        }

        $decodedNonce = base64_decode($nonce);
        $rawString = $decodedNonce . $seed . $secretKey;
        $hash = hash('sha256', $rawString, true);
        $expectedTranKey = base64_encode($hash);

        Log::info('AuthService: Debug datos', [
            'nonce_base64' => $nonce,
            'nonce_decoded' => $decodedNonce,
            'seed' => $seed,
            'secretKey' => $secretKey,
            'rawString' => $rawString,
            'hash' => $hash,
            'expectedTranKey' => $expectedTranKey,
        ]);

        Log::info('AuthService: Comparando tranKey recibido vs esperado.', [
            'tranKey_received' => $tranKey,
            'tranKey_expected' => $expectedTranKey,
        ]);

        if ($tranKey !== $expectedTranKey) {
            Log::warning('AuthService: tranKey incorrecto.', [
                'tranKey_received' => $tranKey,
                'tranKey_expected' => $expectedTranKey,
            ]);
            return false;
        }

        return true;
    }
}
