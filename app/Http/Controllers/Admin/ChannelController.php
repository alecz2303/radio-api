<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Station;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index()
    {
        $channels = Channel::with('station')->orderBy('order')->paginate(10);
        return view('admin.channels.index', compact('channels'));
    }

    public function create()
    {
        $defaultStationId = request()->query('station_id');

        $stations = Station::pluck('name', 'id');
        return view('admin.channels.form', compact('stations', 'defaultStationId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'station_id' => 'required|exists:stations,id',
            'name'       => 'required|string|max:100',
            'slug'       => 'required|string|unique:channels,slug',
            'stream_url' => 'required|url',
            'backup_url' => 'nullable|url',
            'order'      => 'nullable|integer|min:1',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        Channel::create($data);

        return redirect()->route('admin.channels.index')
            ->with('success', 'Canal creado correctamente.');
    }

    public function edit(Channel $channel)
    {
        $stations = Station::pluck('name', 'id');
        return view('admin.channels.form', compact('channel', 'stations'));
    }

    public function update(Request $request, Channel $channel)
    {
        $data = $request->validate([
            'station_id' => 'required|exists:stations,id',
            'name'       => 'required|string|max:100',
            'slug'       => 'required|string|unique:channels,slug,' . $channel->id,
            'stream_url' => 'required|url',
            'backup_url' => 'nullable|url',
            'order'      => 'nullable|integer|min:1',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $channel->update($data);

        return redirect()->route('admin.channels.index')
            ->with('success', 'Canal actualizado correctamente.');
    }

    public function destroy(Channel $channel)
    {
        $channel->delete();
        return redirect()->route('admin.channels.index')
            ->with('success', 'Canal eliminado correctamente.');
    }
}
