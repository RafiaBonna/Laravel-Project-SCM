<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Depo;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles','depo','distributor.depo'])
            ->whereDoesntHave('roles', fn($q)=>$q->where('slug','superadmin'))
            ->orderBy('id','desc')
            ->paginate(10);
        return view('superadmin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('slug','!=','superadmin')->get();
        $depos = Depo::all();
        return view('superadmin.users.create', compact('roles','depos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:8|confirmed',
            'role_id'=>'required|exists:roles,id',
            'status'=>'required|in:active,inactive',
            'depo_location'=>'nullable|string|max:255',
            'distributor_location'=>'nullable|string|max:255',
            'distributor_depo_id'=>'nullable|exists:depos,id'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::find($validated['role_id']);
            $user = User::create([
                'name'=>$validated['name'],
                'email'=>$validated['email'],
                'password'=>Hash::make($validated['password']),
                'status'=>$validated['status']
            ]);
            $user->roles()->attach($role->id);

            $location = $validated['depo_location'] ?? $validated['distributor_location'] ?? 'N/A';

            if($role->slug==='depo'){
                Depo::create([
                    'user_id'=>$user->id,
                    'name'=>$validated['name'].' Depo',
                    'location'=>$location
                ]);
            } elseif($role->slug==='distributor'){
                Distributor::create([
                    'user_id'=>$user->id,
                    'depo_id'=>$validated['distributor_depo_id'],
                    'name'=>$validated['name'].' Distributor',
                    'location'=>$location
                ]);
            }

            DB::commit();
            return redirect()->route('superadmin.users.index')->with('success','User created successfully');
        } catch(\Exception $e){
            DB::rollBack();
            return back()->with('error','Failed: '.$e->getMessage())->withInput();
        }
    }

    public function edit(User $user)
    {
        if($user->hasRole('superadmin')){
            return back()->with('error','Cannot edit Superadmin');
        }
        $roles = Role::where('slug','!=','superadmin')->get();
        $depos = Depo::all();
        $user->load(['roles','depo','distributor']);
        return view('superadmin.users.edit', compact('user','roles','depos'));
    }

    public function update(Request $request, User $user)
    {
        if($user->hasRole('superadmin')){
            return back()->with('error','Cannot update Superadmin');
        }

        $validated = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>['required','email',Rule::unique('users')->ignore($user->id)],
            'password'=>'nullable|string|min:8|confirmed',
            'role_id'=>'required|exists:roles,id',
            'status'=>'required|in:active,inactive',
            'depo_location'=>'nullable|string|max:255',
            'distributor_location'=>'nullable|string|max:255',
            'distributor_depo_id'=>'nullable|exists:depos,id'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::find($validated['role_id']);

            $user->update([
                'name'=>$validated['name'],
                'email'=>$validated['email'],
                'status'=>$validated['status'],
                'password'=>$validated['password']?Hash::make($validated['password']):$user->password
            ]);

            $user->roles()->sync([$role->id]);

            $user->depo()->delete();
            $user->distributor()->delete();

            $location = $validated['depo_location'] ?? $validated['distributor_location'] ?? 'N/A';

            if($role->slug==='depo'){
                Depo::create([
                    'user_id'=>$user->id,
                    'name'=>$user->name.' Depo',
                    'location'=>$location
                ]);
            } elseif($role->slug==='distributor'){
                Distributor::create([
                    'user_id'=>$user->id,
                    'depo_id'=>$validated['distributor_depo_id'],
                    'name'=>$user->name.' Distributor',
                    'location'=>$location
                ]);
            }

            DB::commit();
            return redirect()->route('superadmin.users.index')->with('success','User updated successfully');

        } catch(\Exception $e){
            DB::rollBack();
            return back()->with('error','Failed: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(User $user)
    {
        if($user->hasRole('superadmin')){
            return back()->with('error','Cannot delete Superadmin');
        }

        try {
            $user->delete();
            return redirect()->route('superadmin.users.index')->with('success','User deleted successfully');
        } catch(\Exception $e){
            return back()->with('error','Deletion failed: '.$e->getMessage());
        }
    }
}
