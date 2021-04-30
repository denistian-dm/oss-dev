<?php

namespace App\Http\Controllers;

use App\Models\_Case;
use App\Models\CaseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CaseDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $case_details = CaseDetail::all();

        return response()->json([
            'success' => true,
            'data' => $case_details 
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
            'case_id' => ['required'],
            'status' => ['required'],
            'deskripsi' => ['required']
        ])->validate();

        $case = _Case::find($request->case_id);
        $case->case_status_id = $request->status;
        $case->save();

        $case_detail = CaseDetail::create([
            'deskripsi' => $request->deskripsi,
            'case_id' => $request->case_id,
            'user_id' => Auth::user()->id,
            'case_status_id' => $request->status
        ]);

        // Uploading files
        if ($request->file('files')) {
            $files = $request->file('files');
            foreach ($files as $file) {
                $fileName = $file->getClientOriginalName();
                $folder = 'uploads/'.$case->reference;
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
        // 
    }

    public function showByIdCase($id)
    {
        $case_detail = CaseDetail::where('case_id', $id)
                        ->with('user:id,name')
                        ->with('case_status:id,name')
                        ->with('attachment')
                        ->get();

        $check_attachment = CaseDetail::where('case_id', $id)
                                ->whereHas('attachment', function ($query) {
                                    $query->whereIn('ext', ['jpg', 'jpeg', 'png', 'gif']);
                                })->with('attachment')->get();


        if ($check_attachment->count() > 0) {
            $hasAttachment = true;

            foreach ($check_attachment as $ca) {
                foreach ($ca->attachment as $attch) {
                    $path[] = collect([
                        'name' => $attch->name,
                        'lokasi' => $attch->lokasi,
                    ]);
                }    
            }
            return response()->json([
                'success' => true,
                'data' => $case_detail,
                'has_attachment' => $hasAttachment,
                'path' => $path
            ], 200);

        } else {
            $hasAttachment = false;
            return response()->json([
                'success' => true,
                'data' => $case_detail,
                'has_attachment' => $hasAttachment,
            ], 200);
        }
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
