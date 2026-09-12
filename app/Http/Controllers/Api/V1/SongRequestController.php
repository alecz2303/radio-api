<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\SongRequest;
use App\Models\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SongRequestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'station_slug' => ['required', 'string', 'max:120'],
            'channel_slug' => ['required', 'string', 'max:120'],
            'listener_name' => ['required', 'string', 'max:100'],
            'song' => ['required', 'string', 'max:150'],
            'artist' => ['required', 'string', 'max:150'],
            'dedication' => ['nullable', 'string', 'max:1000'],
        ]);

        $station = Station::query()
            ->where('slug', $data['station_slug'])
            ->where('is_active', true)
            ->firstOrFail();

        $channel = Channel::query()
            ->where('station_id', $station->id)
            ->where('slug', $data['channel_slug'])
            ->where('is_active', true)
            ->firstOrFail();

        $songRequest = SongRequest::create([
            'station_id' => $station->id,
            'channel_id' => $channel->id,
            'listener_name' => trim($data['listener_name']),
            'song' => trim($data['song']),
            'artist' => trim($data['artist']),
            'dedication' => isset($data['dedication']) && trim($data['dedication']) !== ''
                ? trim($data['dedication'])
                : null,
            'status' => SongRequest::STATUS_NEW,
        ]);

        return response()->json([
            'message' => 'Solicitud recibida correctamente.',
            'data' => [
                'id' => $songRequest->id,
                'status' => $songRequest->status,
                'station' => $station->name,
                'channel' => $channel->name,
                'created_at' => $songRequest->created_at?->toIso8601String(),
            ],
        ], 201);
    }
}
