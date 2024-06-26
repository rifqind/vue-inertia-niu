<?php

namespace App\Http\Controllers;

use App\Models\Dinas;
use App\Models\MasterWilayah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $number = 1;
        $id_wilayah = MasterWilayah::getDinasWilayah();
        $users = User::orderBy('dinas.nama')
            ->leftJoin('dinas', 'users.id_dinas', '=', 'dinas.id')
            ->leftJoin('master_wilayah as w', 'w.wilayah_fullcode', '=', 'dinas.wilayah_fullcode')
            ->whereIn('dinas.wilayah_fullcode', $id_wilayah)->with('dinas')->get(['users.*', 'dinas.nama as nama_dinas', 'w.label as wilayah_label']);
        foreach ($users as $user) {
            $user->number = $number;
            $number++;
        }

        return Inertia::render('User/Index', [
            'users' => $users,
        ]);
    }


    public function create()
    {
        $id_wilayah = MasterWilayah::getMyWilayahId();
        $dinas = Dinas::orderBy('wilayah_fullcode')->orderBy('nama')
            ->whereIn('wilayah_fullcode', MasterWilayah::getDinasWilayah())
            ->get(['dinas.id as value', 'dinas.nama as label']);
        return Inertia::render('User/Create', [
            'dinas' => $dinas,
        ]);
    }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    public function store(Request $request)
    {
        //
        $request->validate([
            'username' => ['required', 'string', 'lowercase', 'unique:' . User::class],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'noHp' => ['required', 'string', 'max:13', 'min:12']
        ]);
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'noHp' => $request->noHp,
            'id_dinas' => $request->id_dinas,
        ]);
        return redirect()->route('users.index')->with('message', 'Berhasil menambah pengguna baru');
    }


    // /**
    //  * Show the form for editing the specified resource.
    //  */
    public function edit(Request $request)
    {
        //
        $id = $request->id;
        if ($id) {
            $user = User::find($id);
        } else {
            $user = auth()->user();
        }
        $dinas = Dinas::orderBy('wilayah_fullcode')->orderBy('nama')
            ->whereIn('wilayah_fullcode', MasterWilayah::getDinasWilayah())
            ->get(['dinas.id as value', 'dinas.nama as label']);
        return Inertia::render('User/Edit', [
            'dinas' => $dinas,
            'user' => $user,
        ]);
    }

    public function editProfile(Request $request)
    {
        // $id = auth()->user()->id;
        $id = $request->id;
        // $decryptedId = Crypt::decrypt($id);
        $request->validate([
            'username' => ['required', 'string', Rule::unique('users')->ignore($id)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'noHp' => ['required', 'string', 'max:13'],
            'password' => $request->filled('password') ? ['confirmed', Rules\Password::defaults()] : [],
        ]);
        $user = User::where('id', $id)->update([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'noHp' => $request->noHp,
            'id_dinas' => $request->id_dinas,
        ]);
        if ($request->filled('password')) {
            $user = User::where('id', $id)->update([
                'password' => Hash::make($request->password),
            ]);
        }
        return back()->with('message', 'Berhasil edit profile');
    }


    public function roleChange(Request $request)
    {
        $id = $request->id;
        $user = User::where('id', $id)->first();
        if ($user->role == 'produsen') {
            # code...
            $user = User::where('id', $id)->update([
                'role' => 'kominfo'
            ]);
        } else if ($user->role == 'kominfo') {
            $user = User::where('id', $id)->update([
                'role' => 'admin'
            ]);
        } else if ($user->role == 'admin') {
            $user = User::where('id', $id)->update([
                'role' => 'produsen'
            ]);
        }
        $user = User::where('id', $id)->first();
        return redirect()->route('users.index')->with('message', 'Berhasil mengubah role menjadi ' . $user->role);
    }

    public function reset(Request $request)
    {
        $id = $request->id;
        $user = User::where('id', $id)->with('dinas')->first();
        return Inertia::render('User/Reset', [
            'user' => $user,
        ]);
    }
    public function default(Request $request)
    {
        $id = $request->id;
        $defaults = "deFAULTCoDE";
        $user = User::where('id', $id)->update([
            'password' => Hash::make($defaults)
        ]);
        return redirect()->route('users.index')->with('message', 'Berhasil reset password akun tersebut');
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    public function delete(Request $request)
    {
        //
        $id = $request->id;
        // dd($id);
        User::destroy($id);
        // return response()->json('Berhasil Hapus');
        return redirect()->route('users.index')->with('message', 'Berhasil menghapus akun pengguna tersebut');
    }

}
