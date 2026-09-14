<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Models\Station;
use Illuminate\Http\Request;

class AdCampaignController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'station_slug' => 'nullable|string|max:100',
            'placement' => 'nullable|in:home,splash',
        ]);

        $stationId = null;
        if (!empty($data['station_slug'])) {
            $stationId = Station::where('slug', $data['station_slug'])->value('id');
        }

        $campaigns = AdCampaign::query()
            ->currentlyActive()
            ->when($stationId, function ($query) use ($stationId) {
                $query->where(function ($query) use ($stationId) {
                    $query->whereNull('station_id')->orWhere('station_id', $stationId);
                });
            })
            ->where('placement', $data['placement'] ?? 'home')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(fn (AdCampaign $campaign) => [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'advertiser' => $campaign->advertiser,
                'image_url' => asset('storage/' . $campaign->image_path),
                'target_url' => $campaign->target_url,
                'placement' => $campaign->placement,
            ]);

        return response()->json(['data' => $campaigns]);
    }

    public function impression(AdCampaign $adCampaign)
    {
        if (!$this->isVisible($adCampaign)) {
            return response()->json(['message' => 'Campaign is not active.'], 404);
        }

        $adCampaign->increment('impressions');

        return response()->json(['ok' => true]);
    }

    public function click(AdCampaign $adCampaign)
    {
        if (!$this->isVisible($adCampaign)) {
            return response()->json(['message' => 'Campaign is not active.'], 404);
        }

        $adCampaign->increment('clicks');

        return response()->json([
            'ok' => true,
            'target_url' => $adCampaign->target_url,
        ]);
    }

    private function isVisible(AdCampaign $campaign): bool
    {
        if (!$campaign->is_active) {
            return false;
        }

        if ($campaign->starts_at && $campaign->starts_at->isFuture()) {
            return false;
        }

        if ($campaign->ends_at && $campaign->ends_at->isPast()) {
            return false;
        }

        return true;
    }
}
