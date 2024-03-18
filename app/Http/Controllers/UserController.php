<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\DataTables\UserDataTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(UserDataTable $table)
    {
        if (Auth::user()->level == 'admin') {
            return $table->render('User.index');
        } else {
            return redirect()->route('user.edit', Auth::id());
        }
    }

    public function create()
    {
        $level = [
            'kasir' => 'Kasir',
            'admin' => 'Admin'
        ];
        return view('User.add', compact('level'));
    }

    public function update($id)
    {
        $level = [
            'kasir' => 'Kasir',
            'admin' => 'Admin'
        ];

        if (is_null($id)) {
            $id = Auth::id();
        } elseif (!Auth::user()->level == 'kasir') {
            $id = Auth::id();
        }

        $data = User::find($id);
        return view('User.edit', compact('data', 'level'));
    }

    public function processCreate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'password' => 'required',
            'level' => 'required|in:admin,kasir'
        ], [
            'name.required' => 'Kolom Nama Harus Terisi',
            'username.required' => 'Kolom Username Harus Terisi',
            'password.required' => 'Kolom Password Harus Terisi',
            'level.required' => 'Kolom Level Harus Dipilih',
            'level.in' => 'Kolom Level User Harus Admin atau Kasir',
        ]);

        try {
            DB::beginTransaction();
            User::create($request->all());
            DB::commit();
            // dd($request->all());
            return redirect()->route('user.index')->with(['msg' => 'Berhasil Menambahkan Data User', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            report($e);
            return redirect()->route('user.add')->with(['msg' => 'Gagal Menambahkan Data User', 'type' => 'danger']);
        }
    }

    public function processUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'level' => 'required|in:admin,kasir'
        ], [
            'name.required' => 'Kolom Nama Harus Terisi',
            'username.required' => 'Kolom Username Harus Terisi',
            'level.required' => 'Kolom Level Harus Dipilih',
            'level.in' => 'Kolom Level User Harus Admin atau Kasir',
        ]);

        try {
            DB::beginTransaction();

            $id = $request->id;
            if (!Auth::user()->level == 'kasir' && $request->id!=Auth::id()) {
                $id = Auth::id();
            }

            $user = User::find($id);
            if (is_null($request->password)) {
                $user->update($request->except('password', 'id'));
            } else {
                $user->update($request->except('id'));
            }

            DB::commit();
            return redirect()->route('user.index')->with(['msg' => 'Berhasil Mengubah Data User', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('user.edit')->with(['msg' => 'Gagal Mengubah Data User', 'type' => 'danger']);
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            User::find($id)->delete();
            DB::commit();
            return redirect()->route('user.index')->with(['msg' => 'Berhasil Menghapus Data User', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('user.index')->with(['msg' => 'Gagal Menghapus Data User', 'type' => 'danger']);
        }
    }
}
