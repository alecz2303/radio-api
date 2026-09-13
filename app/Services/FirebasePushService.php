<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class FirebasePushService
{
    private ?array $credentials = null;

    public function configured(): bool
    {
        try {
            $credentials = $this->credentials();
        } catch (RuntimeException) {
            return false;
        }

        return filled($credentials['project_id'] ?? null)
            && filled($credentials['client_email'] ?? null)
            && filled($credentials['private_key'] ?? null);
    }

    public function send(string $token, string $title, string $body, array $data = []): array
    {
        $credentials = $this->credentials();

        if (!filled($credentials['project_id'] ?? null)
            || !filled($credentials['client_email'] ?? null)
            || !filled($credentials['private_key'] ?? null)) {
            throw new RuntimeException('Firebase no está configurado en el servidor.');
        }

        $accessToken = $this->accessToken($credentials);
        $projectId = $credentials['project_id'];

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => collect($data)->map(fn ($value) => (string) $value)->all(),
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'channel_id' => 'somos_radio_updates',
                            'sound' => 'default',
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException($response->json('error.message') ?: 'Firebase rechazó la notificación.');
        }

        return $response->json();
    }

    protected function credentials(): array
    {
        if ($this->credentials !== null) {
            return $this->credentials;
        }

        $path = config('services.firebase.credentials_path');

        if (filled($path)) {
            if (!is_file($path) || !is_readable($path)) {
                throw new RuntimeException('No fue posible leer el archivo de credenciales de Firebase.');
            }

            $decoded = json_decode((string) file_get_contents($path), true);

            if (!is_array($decoded)) {
                throw new RuntimeException('El archivo de credenciales de Firebase no es válido.');
            }

            return $this->credentials = [
                'project_id' => $decoded['project_id'] ?? null,
                'client_email' => $decoded['client_email'] ?? null,
                'private_key' => $decoded['private_key'] ?? null,
            ];
        }

        return $this->credentials = [
            'project_id' => config('services.firebase.project_id'),
            'client_email' => config('services.firebase.client_email'),
            'private_key' => config('services.firebase.private_key'),
        ];
    }

    protected function accessToken(array $credentials): string
    {
        $now = time();
        $header = $this->base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claim = $this->base64Url(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $unsigned = $header.'.'.$claim;
        $privateKey = str_replace('\\n', "\n", (string) $credentials['private_key']);
        $signature = '';

        if (!openssl_sign($unsigned, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('No fue posible firmar la credencial de Firebase.');
        }

        $jwt = $unsigned.'.'.$this->base64Url($signature);
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if ($response->failed() || !$response->json('access_token')) {
            throw new RuntimeException($response->json('error_description') ?: 'No fue posible autenticar con Firebase.');
        }

        return $response->json('access_token');
    }

    protected function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
