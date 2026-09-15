<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushDevice;
use App\Models\PushNotification;
use App\Services\FirebasePushService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class PushNotificationController extends Controller
{
    private const ACTIONS = [
        'live',
        'participate',
        'latest',
        'play:somos-radio-89-1',
        'play:somos-radio-102-9',
    ];

    public function index(FirebasePushService $firebase)
    {
        $notifications = PushNotification::query()->latest()->paginate(15);
        $activeDevices = PushDevice::where('is_active', true)->count();
        $configured = $firebase->configured();

        return view('admin.push-notifications.index', compact(
            'notifications',
            'activeDevices',
            'configured'
        ));
    }

    public function store(Request $request, FirebasePushService $firebase)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:500'],
            'action' => ['nullable', 'string', Rule::in(self::ACTIONS)],
        ]);

        $devices = PushDevice::query()->where('is_active', true)->get();

        $notification = PushNotification::create([
            'title' => $data['title'],
            'body' => $data['body'],
            'target' => 'all',
            'action' => $data['action'] ?? null,
            'data' => array_filter([
                'action' => $data['action'] ?? null,
            ]),
            'status' => PushNotification::STATUS_DRAFT,
            'recipients_count' => $devices->count(),
            'created_by' => $request->user()?->id,
        ]);

        if (!$firebase->configured()) {
            $notification->update([
                'status' => PushNotification::STATUS_FAILED,
                'error_text' => 'Firebase no está configurado en el servidor.',
            ]);

            return back()->with('error', 'Firebase aún no está configurado. La notificación quedó registrada, pero no fue enviada.');
        }

        $success = 0;
        $failure = 0;
        $errors = [];

        foreach ($devices as $device) {
            try {
                $firebase->send(
                    $device->token,
                    $notification->title,
                    $notification->body,
                    $notification->data ?? []
                );
                $success++;
            } catch (Throwable $e) {
                $failure++;
                $message = $e->getMessage();
                $errors[] = $message;

                if ($this->isInvalidRegistrationToken($message)) {
                    $device->update(['is_active' => false]);
                }
            }
        }

        $notification->update([
            'status' => $failure === $devices->count() && $devices->isNotEmpty()
                ? PushNotification::STATUS_FAILED
                : PushNotification::STATUS_SENT,
            'success_count' => $success,
            'failure_count' => $failure,
            'error_text' => $errors ? implode("\n", array_slice(array_unique($errors), 0, 5)) : null,
            'sent_at' => now(),
        ]);

        return back()->with(
            $failure > 0 ? 'warning' : 'success',
            "Notificación procesada: {$success} enviadas, {$failure} fallidas."
        );
    }

    private function isInvalidRegistrationToken(string $message): bool
    {
        $message = strtolower($message);

        return str_contains($message, 'notregistered')
            || str_contains($message, 'unregistered')
            || str_contains($message, 'registration-token-not-registered')
            || str_contains($message, 'registration token is not a valid fcm registration token')
            || str_contains($message, 'requested entity was not found');
    }
}
