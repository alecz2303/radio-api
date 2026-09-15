<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PushDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PushDeviceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:512'],
            'device_key' => ['nullable', 'string', 'max:191'],
            'platform' => ['nullable', 'string', 'max:30'],
            'app_version' => ['nullable', 'string', 'max:50'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $device = DB::transaction(function () use ($data) {
            $attributes = [
                'platform' => $data['platform'] ?? 'android',
                'app_version' => $data['app_version'] ?? null,
                'device_name' => $data['device_name'] ?? null,
                'is_active' => true,
                'last_seen_at' => now(),
            ];

            $deviceKey = trim((string) ($data['device_key'] ?? ''));

            if ($deviceKey !== '') {
                $byKey = PushDevice::query()->where('device_key', $deviceKey)->lockForUpdate()->first();
                $byToken = PushDevice::query()->where('token', $data['token'])->lockForUpdate()->first();

                if ($byKey) {
                    if ($byToken && $byToken->id !== $byKey->id) {
                        $byToken->delete();
                    }

                    $byKey->update(array_merge($attributes, [
                        'token' => $data['token'],
                        'device_key' => $deviceKey,
                    ]));

                    return $byKey->fresh();
                }

                if ($byToken) {
                    $byToken->update(array_merge($attributes, [
                        'device_key' => $deviceKey,
                    ]));

                    return $byToken->fresh();
                }

                return PushDevice::create(array_merge($attributes, [
                    'token' => $data['token'],
                    'device_key' => $deviceKey,
                ]));
            }

            return PushDevice::updateOrCreate(
                ['token' => $data['token']],
                $attributes
            );
        });

        return response()->json([
            'message' => 'Dispositivo registrado correctamente.',
            'data' => [
                'id' => $device->id,
                'active' => $device->is_active,
            ],
        ]);
    }
}
