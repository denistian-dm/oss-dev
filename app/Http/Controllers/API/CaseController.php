<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\_Case;
use App\Models\CaseDetail;
use App\Models\Juklak;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cases = _Case::with('member:id,name,id_member')
                        ->with('user:id,name')
                        ->with('juklak:id,name')
                        ->with('category:id,name')
                        ->with('case_status:id,name')
                        ->get();

        return response()->json([
            'success' => true,
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
            $from = Carbon::createFromFormat('M d Y H:i:s', $from[1].' '.$from[2].' '.$from[3].' '.$from[4])->timezone($from[5]);

            $to = explode(' ', $request->dateEnd);
            $to = Carbon::createFromFormat('M d Y H:i:s', $to[1].' '.$to[2].' '.$to[3].' 23:59:59')->timezone($to[5]);
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

        $cases = $cases->with('member:id,name,id_member')->with('user:id,name')->with('juklak:id,name')->with('category:id,name')->with('case_status:id,name');
        $cases = $cases->get();

        return response()->json([
            'success' => true,
            'data' => $cases
        ], 200);
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
