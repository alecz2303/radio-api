<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\ContestEntry;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContestController extends Controller
{
    public function active(Request $request)
    {
        $stationId = null;
        if ($slug = $request->query('station_slug')) {
            $stationId = Station::where('slug', $slug)->value('id');
        }
        $contest = Contest::currentlyActive()
            ->when($stationId, fn($q) => $q->where(fn($x) => $x->whereNull('station_id')->orWhere('station_id',$stationId)))
            ->withCount(['entries as winners_count' => fn($q) => $q->where('is_winner', true)])
            ->latest()->first();
        if (!$contest || ($contest->close_when_full && $contest->winners_count >= $contest->max_winners)) return response()->json(['data' => null]);
        return response()->json(['data' => $this->contestData($contest)]);
    }

    public function pendingPrize(Request $request)
    {
        $data = $request->validate(['device_key' => 'required|string|max:100']);
        $entry = ContestEntry::query()->with('contest')->where('device_key',$data['device_key'])->where('is_winner',true)->whereNull('claimed_at')->latest()->first();
        if (!$entry) return response()->json(['data'=>null]);
        return response()->json(['data'=>$this->entryData($entry, $entry->contest, true)]);
    }

    public function submit(Request $request, Contest $contest)
    {
        $data=$request->validate(['device_key'=>'required|string|max:100','selected_option'=>'required|in:A,B,C','listener_name'=>'nullable|string|max:120','phone'=>'nullable|string|max:40']);
        $result=DB::transaction(function()use($contest,$data){
            $locked=Contest::whereKey($contest->id)->lockForUpdate()->firstOrFail();$now=now();
            $available=$locked->is_active&&(!$locked->starts_at||$locked->starts_at<=$now)&&(!$locked->ends_at||$locked->ends_at>=$now);
            if(!$available)return['error'=>'Esta dinámica ya no está disponible.','status'=>409];
            $existing=ContestEntry::where('contest_id',$locked->id)->where('device_key',$data['device_key'])->first();if($existing)return['entry'=>$existing,'already'=>true];
            $correct=$data['selected_option']===$locked->correct_option;$winnerCount=ContestEntry::where('contest_id',$locked->id)->where('is_winner',true)->count();$winner=$correct&&$winnerCount<$locked->max_winners;
            if($locked->close_when_full&&$winnerCount>=$locked->max_winners)return['error'=>'Los lugares ganadores ya fueron asignados.','status'=>409];
            $entry=ContestEntry::create(['contest_id'=>$locked->id,'device_key'=>$data['device_key'],'listener_name'=>$data['listener_name']??null,'phone'=>$data['phone']??null,'selected_option'=>$data['selected_option'],'is_correct'=>$correct,'is_winner'=>$winner,'claim_code'=>$winner?$this->uniqueClaimCode():null]);
            return['entry'=>$entry,'already'=>false];
        });
        if(isset($result['error']))return response()->json(['message'=>$result['error']],$result['status']);
        return response()->json(['message'=>$result['already']?'Ya participaste en esta dinámica.':'Participación registrada.','data'=>$this->entryData($result['entry'],$contest,$result['already'])]);
    }

    private function entryData(ContestEntry $entry, Contest $contest, bool $already=false): array
    {
        return ['already_participated'=>$already,'is_correct'=>$entry->is_correct,'is_winner'=>$entry->is_winner,'claim_code'=>$entry->claim_code,'claimed_at'=>$entry->claimed_at?->toIso8601String(),'prize'=>$entry->is_winner?$contest->prize:null,'redemption_instructions'=>$entry->is_winner?$contest->redemption_instructions:null];
    }
    private function contestData(Contest $contest):array{return['id'=>$contest->id,'title'=>$contest->title,'question'=>$contest->question,'options'=>['A'=>$contest->option_a,'B'=>$contest->option_b,'C'=>$contest->option_c],'prize'=>$contest->prize,'ends_at'=>$contest->ends_at?->toIso8601String()];}
    private function uniqueClaimCode():string{do{$code='SR-'.strtoupper(Str::random(8));}while(ContestEntry::where('claim_code',$code)->exists());return $code;}
}
