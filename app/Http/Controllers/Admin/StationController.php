<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index()
    {
        $stations = Station::latest()->paginate(10);
        return view('admin.stations.index', compact('stations'));
    }

    public function create()
    {
        return view('admin.stations.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|unique:stations,slug',
            'logo_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        Station::create($data);

        return redirect()->route('admin.stations.index')->with('success', 'Estación creada');
    }

    public function edit(Station $station)
    {
        return view('admin.stations.form', compact('station'));
    }

    public function update(Request $request, Station $station)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|unique:stations,slug,' . $station->id,
            'logo_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $station->update($data);

        return redirect()->route('admin.stations.index')->with('success', 'Estación actualizada');
    }

    public function destroy(Station $station)
    {
        $station->delete();
        return redirect()->route('admin.stations.index')->with('success', 'Estación eliminada');
    }

    public function channels(Station $station)
    {
        $channels = $station->channels()->orderBy('order')->paginate(10);
        return view('admin.channels.index', compact('channels', 'station'));
    }
}
