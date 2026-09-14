<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\Contest;use App\Models\ContestEntry;use App\Models\Station;use Illuminate\Http\Request;
class ContestController extends Controller {
 public function index(){ $contests=Contest::with('station')->withCount(['entries','entries as correct_count'=>fn($q)=>$q->where('is_correct',true),'entries as winners_count'=>fn($q)=>$q->where('is_winner',true),'entries as claimed_count'=>fn($q)=>$q->whereNotNull('claimed_at')])->latest()->paginate(10);$stations=Station::where('is_active',true)->orderBy('name')->get();return view('admin.contests.index',compact('contests','stations'));}
 public function store(Request $r){$d=$this->validated($r);$d['is_active']=$r->boolean('is_active');$d['close_when_full']=$r->boolean('close_when_full');Contest::create($d);return back()->with('success','Dinámica creada correctamente.');}
 public function update(Request $r,Contest $contest){$d=$this->validated($r);$d['is_active']=$r->boolean('is_active');$d['close_when_full']=$r->boolean('close_when_full');$contest->update($d);return back()->with('success','Dinámica actualizada.');}
 public function toggle(Contest $contest){$contest->update(['is_active'=>!$contest->is_active]);return back()->with('success',$contest->is_active?'Dinámica activada.':'Dinámica desactivada.');}
 public function destroy(Contest $contest){$contest->delete();return back()->with('success','Dinámica eliminada.');}
 public function entries(Contest $contest){$entries=$contest->entries()->latest()->paginate(50);return view('admin.contests.entries',compact('contest','entries'));}
 public function claim(ContestEntry $entry){abort_unless($entry->is_winner,422);if(!$entry->claimed_at)$entry->update(['claimed_at'=>now()]);return back()->with('success','Premio marcado como entregado.');}
 private function validated(Request $r):array{return $r->validate(['station_id'=>'nullable|exists:stations,id','title'=>'required|string|max:140','question'=>'required|string|max:1000','option_a'=>'required|string|max:255','option_b'=>'required|string|max:255','option_c'=>'required|string|max:255','correct_option'=>'required|in:A,B,C','max_winners'=>'required|integer|min:1|max:10000','prize'=>'nullable|string|max:255','redemption_instructions'=>'nullable|string|max:1000','starts_at'=>'nullable|date','ends_at'=>'nullable|date|after_or_equal:starts_at','is_active'=>'nullable|boolean','close_when_full'=>'nullable|boolean']);}
}
