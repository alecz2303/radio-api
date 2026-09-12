<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SongRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SongRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $channelId = $request->integer('channel_id');

        $query = SongRequest::query()
            ->with(['station', 'channel'])
            ->latest();

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($channelId) {
            $query->where('channel_id', $channelId);
        }

        $songRequests = $query->paginate(20)->withQueryString();
        $channels = \App\Models\Channel::query()
            ->whereHas('station', fn ($q) => $q->where('slug', 'somos-radio'))
            ->orderBy('order')
            ->get();

        $counts = [
            'new' => SongRequest::where('status', SongRequest::STATUS_NEW)->count(),
            'seen' => SongRequest::where('status', SongRequest::STATUS_SEEN)->count(),
            'attended' => SongRequest::where('status', SongRequest::STATUS_ATTENDED)->count(),
            'discarded' => SongRequest::where('status', SongRequest::STATUS_DISCARDED)->count(),
        ];

        return view('admin.song-requests.index', compact(
            'songRequests',
            'channels',
            'counts',
            'status',
            'channelId'
        ));
    }

    public function update(Request $request, SongRequest $songRequest)
    {
        $data = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    SongRequest::STATUS_NEW,
                    SongRequest::STATUS_SEEN,
                    SongRequest::STATUS_ATTENDED,
                    SongRequest::STATUS_DISCARDED,
                ]),
            ],
        ]);

        $songRequest->update($data);

        return back()->with('success', 'Estado de la solicitud actualizado.');
    }
}
