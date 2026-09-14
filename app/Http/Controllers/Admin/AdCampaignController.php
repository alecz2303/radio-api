<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdCampaignController extends Controller
{
    public function index()
    {
        $campaigns = AdCampaign::query()
            ->with('station')
            ->latest()
            ->paginate(12);

        $stations = Station::query()->where('is_active', true)->orderBy('name')->get();

        $stats = [
            'active' => AdCampaign::currentlyActive()->count(),
            'impressions' => AdCampaign::sum('impressions'),
            'clicks' => AdCampaign::sum('clicks'),
        ];

        return view('admin.ads.index', compact('campaigns', 'stations', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $this->validateCampaign($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['image_path'] = $request->file('image')->store('ads', 'public');

        unset($data['image']);

        AdCampaign::create($data);

        return redirect()->route('admin.ads.index')->with('success', 'Campaña publicitaria creada.');
    }

    public function update(Request $request, AdCampaign $adCampaign)
    {
        $data = $this->validateCampaign($request, false);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($adCampaign->image_path) {
                Storage::disk('public')->delete($adCampaign->image_path);
            }

            $data['image_path'] = $request->file('image')->store('ads', 'public');
        }

        unset($data['image']);

        $adCampaign->update($data);

        return redirect()->route('admin.ads.index')->with('success', 'Campaña actualizada.');
    }

    public function destroy(AdCampaign $adCampaign)
    {
        if ($adCampaign->image_path) {
            Storage::disk('public')->delete($adCampaign->image_path);
        }

        $adCampaign->delete();

        return redirect()->route('admin.ads.index')->with('success', 'Campaña eliminada.');
    }

    private function validateCampaign(Request $request, bool $imageRequired = true): array
    {
        return $request->validate([
            'name' => 'required|string|max:120',
            'advertiser' => 'nullable|string|max:120',
            'station_id' => 'nullable|exists:stations,id',
            'image' => ($imageRequired ? 'required' : 'nullable') . '|image|mimes:jpg,jpeg,png,webp|max:4096',
            'target_url' => 'nullable|url|max:500',
            'placement' => 'required|in:home',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',
        ]);
    }
}
