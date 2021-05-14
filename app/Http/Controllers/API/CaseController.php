<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\_Case;
use App\Models\CaseDetail;
use App\Models\Juklak;
use App\Models\JuklakCategory;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tz = geoip()->getLocation('111.68.29.30')->timezone;
        $from = Carbon::now()->timezone($tz)->startOfMonth()->setTimezone('UTC');
        $to = Carbon::now()->timezone($tz)->endOfMonth()->setTimezone('UTC');

        $cases = _Case::with('member:id,name,id_member')
                        ->with('user:id,name,profile_photo_path')
                        ->with('juklak:id,name')
                        ->with('category:id,name')
                        ->with('case_status:id,name')
                        ->whereBetween('created_at', [$from, $to])
                        ->latest()
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $cases
        ], 200);
    }

    public function new_case()
    {
        $cases = _Case::with('member:id,name,id_member')
                        ->with('user:id,name,profile_photo_path')
                        ->with('juklak:id,name')
                        ->with('category:id,name')
                        ->with('case_status:id,name')
                        ->latest()
                        ->take(5)
                        ->get();

        $pending = _Case::where('case_status_id', 1)->get();
        $urgent = _Case::where('case_status_id', 3)->get();

        return response()->json([
            'success' => true,
            'pending' => $pending->count(),
            'urgent' => $urgent->count(),
            'data' => $cases
        ], 200);
    }

    public function filter(Request $request)
    {
        $cases = new _Case;
        
        if ($request->dateStart != null || $request->dateEnd != null) {
            Validator::make($request->all(), [
                'dateStart' => ['required'],
                'dateEnd' => ['required']
            ])->validate();

            $from = explode(' ', $request->dateStart);
            $from = Carbon::createFromFormat('M d Y', $from[1].' '.$from[2].' '.$from[3], $from[5])->startOfDay()->setTimezone('UTC');

            $to = explode(' ', $request->dateEnd);
            $to = Carbon::createFromFormat('M d Y', $to[1].' '.$to[2].' '.$to[3], $to[5])->endOfDay()->setTimezone('UTC');
            $cases = $cases->whereBetween('created_at', [$from, $to]);
        }

        if ($request->reference != null) {
            $cases = $cases->where('reference', $request->reference);
        }

        if ($request->caseStatus != null) {
            $cases = $cases->where('case_status_id', $request->caseStatus);
        }

        if ($request->client != null) {
            $client = Member::all()->where('client_id', $request->client)->pluck('id');
            $cases = $cases->whereIn('member_id', $client);
        }

        if ($request->caseCategory != null) {
            $cases = $cases->where('category_id', $request->caseCategory);
        }

        if ($request->juklakCategory != null) {
            if ($request->juklak != null) {
                $cases = $cases->where('juklak_id', $request->juklak);
            } else {
                $juklak = Juklak::all()->where('juklak_category_id', $request->juklakCategory)->pluck('id');
                $cases = $cases->whereIn('juklak_id', $juklak);
            }
        }

        $cases = $cases->with('member:id,name,id_member')->with('user:id,name,profile_photo_path')->with('juklak:id,name')->with('category:id,name')->with('case_status:id,name');
        $cases = $cases->get();

        return response()->json([
            'success' => true,
            'data' => $cases
        ], 200);
    }

    public function statistik()
    {   
        $jukcat = JuklakCategory::select('id', 'name')->withCount('cases')->orderBy('cases_count', 'desc')->take(5)->get();

        $topCases = _Case::with('juklak:id,name,juklak_category_id')
                        ->groupBy('juklak_id')
                        ->select('juklak_id', DB::raw('count(*) as total'))
                        ->orderBy('total', 'desc')
                        ->take(5)
                        ->get();

        $ppi     = _Case::with('juklak:id,name,juklak_category_id')
                        ->where('case_status_id', 4)
                        ->groupBy('juklak_id')
                        ->select('juklak_id', DB::raw('count(*) as total'))
                        ->orderBy('total', 'desc')
                        ->take(5)
                        ->get();

        return response()->json([
            'success' => true,
            'top_categories' => $jukcat,
            'top_cases' => $topCases,
            'ppi' => $ppi
        ], 200);
    }

    public function filter_statistik (Request $request) {
        Validator::make($request->all(), [
            'dateStart' => ['required'],
            'dateEnd' => ['required']
        ])->validate();

        $from = explode(' ', $request->dateStart);
        $from = Carbon::createFromFormat('M d Y', $from[1].' '.$from[2].' '.$from[3], $from[5])->setTimezone('UTC');

        $to = explode(' ', $request->dateEnd);
        $to = Carbon::createFromFormat('M d Y', $to[1].' '.$to[2].' '.$to[3], $to[5])->endOfDay()->setTimezone('UTC');

        $jukcat = JuklakCategory::select('id', 'name')
                        ->withCount(['cases' => function ($query) use($from, $to) {
                            $query->whereBetween('cases.created_at', [$from, $to]);
                        }])->orderBy('cases_count', 'desc')
                        ->take(5)
                        ->get();

        $topCases = _Case::with('juklak:id,name,juklak_category_id')
                        ->groupBy('juklak_id')
                        ->select('juklak_id', DB::raw('count(*) as total'))
                        ->whereBetween('created_at', [$from, $to])
                        ->orderBy('total', 'desc')
                        ->take(5)
                        ->get();

        $ppi     = _Case::with('juklak:id,name,juklak_category_id')
                        ->where('case_status_id', 4)
                        ->groupBy('juklak_id')
                        ->select('juklak_id', DB::raw('count(*) as total'))
                        ->whereBetween('created_at', [$from, $to])
                        ->orderBy('total', 'desc')
                        ->take(5)
                        ->get();

        return response()->json([
            'success' => true,
            'top_categories' => $jukcat,
            'top_cases' => $topCases,
            'ppi' => $ppi
        ], 200);
    }

    public function chart(Request $request) {
        
        $filterDate = $this->filteringDate($request->dateStart, $request->dateEnd);
        $labels = $filterDate['labels'];
        $range = $filterDate['range'];

        $backgroundColor = [
            '#FECACA',
            '#FDE68A',
            '#A7F3D0',
            '#BFDBFE',
            '#818CF8',
            '#DDD6FE',
            '#FBCFE8',
            '#B45309',
            '#047857'
        ];

        $jukcat = JuklakCategory::all();
        $i = 0;

        foreach ($jukcat as $jc) {
            for ($index=0; $index < count($range); $index++) { 
                $getData        = JuklakCategory::select('id', 'name')
                                            ->withCount(['cases' => function ($query) use($range, $index) {
                                                $query->whereBetween('cases.created_at', $range[$index]);
                                            }])->where('id', $jc->id)->first();
                $data[$index]   = $getData->cases_count;
            }

            $datasets[] = [
                "type" => "bar",
                "label" => $jc->name,
                "backgroundColor" => $backgroundColor[$i],
                "data" => $data
            ];

            $i++;
        }

        // $tempFrom = $from;
        for ($i=0; $i < count($range); $i++) {
            $getData = _Case::whereBetween('created_at', $range[$i])->get();
            $lineData[] = $getData->count();
            // $tempFrom->addDay(1);
        }

        return response()->json([
            'periods' => $labels,
            'datasets' => $datasets,
            'line' => [
                'labels' => $labels,
                'datasets' => [
                    'label' => 'Total',
                    'data' => $lineData,
                    'fill' => false,
                    'borderColor' => $backgroundColor[0]
                ]
            ]
        ], 200);
    }

    private function filteringDate($from, $to) {
        $from = explode(' ', $from);
        $from = Carbon::createFromFormat('M d Y', $from[1].' '.$from[2].' '.$from[3], $from[5])->startOfDay()->setTimezone('UTC');

        $to = explode(' ', $to);
        $to = Carbon::createFromFormat('M d Y', $to[1].' '.$to[2].' '.$to[3], $to[5])->endOfDay()->setTimezone('UTC');

        $tz = geoip()->getLocation('111.68.29.30')->timezone;
        $tempFrom = $from;
        
        $dayInterval    = $from->diffInDays($to);
        $weekInterval   = $from->diffInWeeks($to);
        $monthInterval  = $from->diffInMonths($to);
        $yearInterval   = $from->diffInYears($to);

        if ($yearInterval > 0) 
        {
            // get yeaerly datas
            for ($i=0; $i <= $yearInterval; $i++) { 
                $tempStart  = $tempFrom->format('Y-m-d H:i:s');

                if ($tempFrom->diffInYears($to) > 0) 
                { $tempEnd = $tempFrom->setTimezone($tz)->endOfYear()->setTimezone('UTC')->format('Y-m-d H:i:s'); } 
                else
                { $tempEnd = $to->format('Y-m-d H:i:s');}

                $range[$i]  = [$tempStart, $tempEnd];
                $labels[$i] = strtoupper(Carbon::parse($tempStart)->setTimezone($tz)->format('dM').'-'. Carbon::parse($tempEnd)->setTimezone($tz)->format('dM Y'));
                $tempFrom = $tempFrom->addDay(1)->setTimezone($tz)->startOfDay()->setTimezone('UTC');
            }
        } 
        else 
        {
            if ($monthInterval >= 2) 
            {
                // get monthly datas
                for ($i=0; $i <= $monthInterval; $i++) { 
                    $tempStart  = $tempFrom->format('Y-m-d H:i:s');

                    if ($tempFrom->diffInMonths($to) > 0) 
                    { $tempEnd = $tempFrom->setTimezone($tz)->endOfMonth()->setTimezone('UTC')->format('Y-m-d H:i:s'); } 
                    else 
                    { $tempEnd = $to->format('Y-m-d H:i:s'); }

                    $range[$i]  = [$tempStart, $tempEnd];
                    $labels[$i] = strtoupper(Carbon::parse($tempStart)->setTimezone($tz)->format('d').'-'. Carbon::parse($tempEnd)->setTimezone($tz)->format('d M'));
                    $tempFrom = $tempFrom->addDay(1)->setTimezone($tz)->startOfDay()->setTimezone('UTC');
                }
            } 
            else 
            {
                if ($weekInterval >= 2) 
                {
                    // get weekly datas
                    for ($i=0; $i <= $weekInterval+1; $i++) { 
                        $tempStart  = $tempFrom->format('Y-m-d H:i:s');

                        if ($tempFrom->diffInWeeks($to) > 0)
                        { $tempEnd    = $tempFrom->setTimezone($tz)->endOfWeek()->setTimezone('UTC')->format('Y-m-d H:i:s');} 
                        else 
                        { $tempEnd    = $to->format('Y-m-d H:i:s'); }

                        $range[$i]  = [$tempStart, $tempEnd];
                        $labels[$i] = strtoupper(Carbon::parse($tempStart)->setTimezone($tz)->format('dM').' - '. Carbon::parse($tempEnd)->setTimezone($tz)->format('dM'));

                        $tempFrom = $tempFrom->setTimezone($tz)->addDay(1)->startOfDay()->setTimezone('UTC');
                    }
                } 
                else 
                {
                    // get daily datas
                    for ($i=0; $i <= $dayInterval; $i++) {
                        $labels[$i] = $tempFrom->setTimezone($tz)->format('d M y');
                        $range[$i] = [
                            $tempFrom->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s'), 
                            $tempFrom->setTimezone($tz)->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s') 
                        ];

                        $tempFrom->setTimezone($tz)->addDay(1);
                    }
                }
            }
        }

        $data = [
            "labels" => $labels,
            "range" => $range
        ];

        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'ref_code' => ['required', 'string'],
            'member_id' => ['required'],
            'category_id' => ['required'],
            'juklak_id' => ['required'],
            'deskripsi' => ['required']
        ])->validate();
        
        $juklak = Juklak::find($request->juklak_id);

        $reference_temp = $request->ref_code.Carbon::now()->isoFormat("YYMM").'-'.$juklak->juklak_category_id;

        $find_reference = _Case::where('reference', 'like', $reference_temp.'%')->latest()->first();

        if ($find_reference) {
            $reference_latest = explode('-', $find_reference->reference);
            $reference_order = ($reference_latest[1].$reference_latest[2]) + 1;
            $reference = $reference_temp.'-'.substr($reference_order, 1) ;
        } else {
            $reference = $reference_temp.'-001';
        }

        $case = _Case::create([
            'reference' => $reference,
            'member_id' => $request->member_id,
            'user_id' => Auth::user()->id,
            'category_id' => $request->category_id,
            'juklak_id' => $request->juklak_id,
            'case_status_id' => 1
        ]);

        $case_detail = CaseDetail::create([
            'deskripsi' => $request->deskripsi,
            'case_id' => $case->id,
            'user_id' => Auth::user()->id,
            'case_status_id' => 1
        ]);

        // Uploading files
        if ($request->file('files')) {
            $files = $request->file('files');
            foreach ($files as $file) {
                $fileName = $file->getClientOriginalName();
                $folder = 'uploads/'.$reference;
                $path = $file->storeAs($folder, $fileName, 'public');
                
                $case_detail->attachment()->create([
                    'name' => $fileName,
                    'ext' => $file->extension(),
                    'lokasi' => $path
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'case' => $case,
            'case_detail' => $case_detail
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $case = _Case::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $case
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
