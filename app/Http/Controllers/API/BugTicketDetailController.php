<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BugTicket;
use App\Models\BugTicketDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BugTicketDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'status' => ['required'],
            'comment' => ['required'],
            'bug_ticket_id' => ['required'],
        ])->validate();

        $bug_ticket = BugTicket::find($request->bug_ticket_id);
        $bug_ticket->bug_ticket_status_id = $request->status;
        $bug_ticket->save();

        $detail = BugTicketDetail::create([
            'comment' => $request->comment,
            'user_id' => Auth::user()->id,
            'bug_ticket_id' => $request->bug_ticket_id,
            'bug_ticket_status_id' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'data' => $detail
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
