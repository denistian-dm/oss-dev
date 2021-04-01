<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $members = Member::with('client:id,name')
                            ->get();

        return response()->json([
            'success' => true,
            'data' => $members
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
            'id_member' => ['required', 'string', 'unique:members'],
            'name' => ['required', 'string'],
            'email' => ['email', 'max:50'],
            'phone1' => ['string', 'max:15'],
            'phone2' => ['string', 'max:15'],
            'client_id' => ['required']
        ])->validate();

        $member = Member::create([
            'id_member' => $request->id_member,
            'name' => $request->name,
            'email' => $request->email,
            'phone1' => $request->phone1,
            'phone2' => $request->phone2,
            'client_id' => $request->client_id
        ]);

        return response()->json([
            'success' => true,
            'data' => $member
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
        $member = Member::findOrFail($id);

        Validator::make($request->all(), [
            'id_member' => ['required', 'string', Rule::unique('members')->ignore($member->id)],
            'name' => ['required', 'string'],
            'email' => ['email', 'max:50'],
            'phone1' => ['string', 'max:15'],
            'phone2' => ['string', 'max:15'],
            'client_id' => ['required']
        ])->validate();

        $member->update([
            'id_member' => $request->id_member,
            'name' => $request->name,
            'email' => $request->email,
            'phone1' => $request->phone1,
            'phone2' => $request->phone2,
            'client_id' => $request->client_id
        ]);

        return response()->json([
            'success' => true,
            'data' => $member
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
        $member = Member::findOrFail($id);
        $member->delete();

        return response()->json([
            'sucess' => true,
            'message' => 'Data has been deleted!'
        ], 200);
    }
}
