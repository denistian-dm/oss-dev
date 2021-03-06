<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;
use Laravel\Jetstream\Jetstream;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$this->authorizationCheck()) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $users = User::with('level:id,name')
                    ->with('division:id,code,name')
                    ->get();
        
        $data = [
            'success' => true,
            'data' => $users
        ];

        return response()->json($data, 200);
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

    private function authorizationCheck()
    {
        $me = Auth::user()->tokens->first()->name;

        if ($me == 'Admin') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$this->authorizationCheck()) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', new Password, 'confirmed'],
            'username' => ['required', 'max:25', 'unique:users'],
            'division' => ['required'],
            'level' => ['required'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'username' => $request->username,
                    'division_id' => $request->division,
                    'level_id' => $request->level,
                ]);

        $this->createTeam($user);

        $tokenName = $user->level->name;
        $user->createToken($tokenName);

        $data = User::where('id', $user->id)
                    ->with('level:id,name')
                    ->with('division:id,code,name')
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);        
        
    }

    protected function createTeam(User $user)
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
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
        if (!$this->authorizationCheck()) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $user = User::findOrFail($id);

        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'max:25', Rule::unique('users')->ignore($user->id)],
            'division' => ['required'],
            'level' => ['required'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'division_id' => $request->division,
            'level_id' => $request->level
        ]);

        $updated_user = User::with('level:id,name')->with('division:id,code,name')->findOrFail($id);
        $updated_user->tokens->first()->update([
            'name' => $updated_user->level->name
        ]);

        $data = [
            'success' => true,
            'data' => $updated_user,
        ];

        return response()->json($data, 200);
    }

    public function reset($id)
    {
        if (!$this->authorizationCheck()) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $new_pwd = Hash::make('polokoplo');

        $user = User::findOrFail($id);
        $user->password = $new_pwd;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Success updating password'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$this->authorizationCheck()) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'sucess' => true,
            'message' => 'Data has been deleted!'
        ], 200);
    }
}
