<?php

namespace App\Http\Controllers;

use App\Models\Juklak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JuklakController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $juklaks = Juklak::with('juklak_category:id,name')->get();

        return response()->json([
            'success' => true,
            'data' => $juklaks
        ], 200);
    }

    public function findByCategory($category_id)
    {
        $juklaks = Juklak::with('juklak_category:id,name')
                        ->where('juklak_category_id', $category_id)
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $juklaks
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
            'name' => ['required'],
            'juklak_category_id' => ['required']
        ])->validate();

        $juklak = Juklak::create([
            'name' => $request->name,
            'contoh' => $request->contoh,
            'jawaban' => $request->jawaban,
            'follow_up' => $request->follow_up,
            'indikator' => $request->indikator,
            'juklak_category_id' => $request->juklak_category_id
        ]);

        return response()->json([
            'success' => true,
            'data' => $juklak
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
        $juklak = Juklak::findOrFail($id);

        $data = [
            'success' => true,
            'data' => $juklak
        ];

        return response()->json($data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $juklak = Juklak::findOrFail($id);

        $data = [
            'success' => true,
            'data' => $juklak
        ];

        return response()->json($data, 200);
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
        $juklak = Juklak::findOrFail($id);

        Validator::make($request->all(), [
            'name' => ['required'],
            'juklak_category_id' => ['required']
        ])->validate();

        $juklak->update([
            'name' => $request->name,
            'contoh' => $request->contoh,
            'jawaban' => $request->jawaban,
            'follow_up' => $request->follow_up,
            'indikator' => $request->indikator,
            'juklak_category_id' => $request->juklak_category_id
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $juklak
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $juklak = Juklak::findOrFail($id);
        $juklak->delete();

        return response()->json([
            'sucess' => true,
            'message' => 'Data has been deleted!'
        ], 200);
    }
}
