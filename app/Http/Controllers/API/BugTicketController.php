<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BugTicket;
use App\Models\BugTicketDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class BugTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bug_tickets = BugTicket::select(['id', 'juklak_id', 'bug_ticket_status_id', 'created_at'])
                            ->with('bug_ticket_status:id,name')
                            ->with('juklak:id,name')
                            ->latest()
                            ->get();

        return response()->json([
            'success' => true,
            'data' => $bug_tickets
        ], 200);
    }

    public function filter(Request $request)
    {
        Validator::make($request->all(), [
            'dateStart' => ['required'],
            'dateEnd' => ['required']
        ])->validate();

        $from = explode(' ', $request->dateStart);
        $from = Carbon::createFromFormat('M d Y', $from[1].' '.$from[2].' '.$from[3], $from[5])->startOfDay()->setTimezone('UTC');

        $to = explode(' ', $request->dateEnd);
        $to = Carbon::createFromFormat('M d Y', $to[1].' '.$to[2].' '.$to[3], $to[5])->endOfDay()->setTimezone('UTC');

        $bug_tickets = BugTicket::select(['id', 'juklak_id', 'bug_ticket_status_id', 'created_at'])
                            ->with('bug_ticket_status:id,name')
                            ->with('juklak:id,name')
                            ->whereBetween('created_at', [$from, $to])
                            ->latest()
                            ->get();

        return response()->json([
            'success' => true,
            'data' => $bug_tickets
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bug_tickets = BugTicket::with('cases:id,reference')->with('juklak:id,name')->get()->find($id);
        $bug_ticket_details = BugTicketDetail::with('user:id,name,profile_photo_path')->where('bug_ticket_id', $id)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'bug_ticket' => $bug_tickets,
                'details' => $bug_ticket_details
            ]
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
