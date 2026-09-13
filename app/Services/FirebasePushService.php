<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class FirebasePushService
{
    public function configured(): bool
    {
        return filled(config('services.firebase.project_id'))
            && filled(config('services.firebase.client_email'))
            && filled(config('services.firebase.private_key'));
    }

    public function send(string $token, string $title, string $body, array $data = []): array
    {
        if (!$this->configured()) {
            throw new RuntimeException('Firebase no está configurado en el servidor.');
        }

        $accessToken = $this->accessToken();
        $projectId = config('services.firebase.project_id');

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

    protected function accessToken(): string
    {
        $now = time();
        $header = $this->base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claim = $this->base64Url(json_encode([
            'iss' => config('services.firebase.client_email'),
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $unsigned = $header.'.'.$claim;
        $privateKey = str_replace('\\n', "\n", (string) config('services.firebase.private_key'));
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
