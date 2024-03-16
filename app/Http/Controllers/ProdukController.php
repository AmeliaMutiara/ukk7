<?php

namespace App\Http\Controllers;

use App\DataTables\ProdukDataTable;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProdukController extends Controller
{
    public function index(ProdukDataTable $table)
    {
        Session::forget('data-produk');
        return $table->render('Produk.index');
    }

    public function create()
    {
        $sessiondata = Session::get('data-produk');
        return view('Produk.add', compact('sessiondata'));
    }

    public function update($id)
    {
        $data = Produk::find($id);
        $sessiondata = Session::get('data-produk');
        return view('Produk.edit', compact('data', 'sessiondata'));
    }

    public function processCreate(Request $request)
    {
        $request->validate([
            'namaProduk' => 'required',
            'harga' => 'required',
            'stok' => 'required'
        ], [
            'namaProduk.required' => 'Kolom Nama Produk Harus Terisi',
            'harga.required' => 'Kolom Harga Produk Harus Terisi',
            'stok.required' => 'Kolom Stok Produk Harus Terisi',
        ]);

        try {
            DB::beginTransaction();
            Produk::create($request->all());
            DB::commit();
            return redirect()->route('product.index')->with(['msg' => 'Berhasil Menambahkan Data Produk', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('product.add')->with(['msg' => 'Gagal Menambahkan Data Produk', 'type' => 'danger']);
        }
    }

    public function processUpdate(Request $request)
    {
        $request->validate([
            'namaProduk' => 'required',
            'harga' => 'required',
            'stok' => 'required'
        ], [
            'namaProduk.required' => 'Kolom Nama Produk Harus Terisi',
            'harga.required' => 'Kolom Harga Produk Harus Terisi',
            'stok.required' => 'Kolom Stok Produk Harus Terisi',
        ]);

        try {
            DB::beginTransaction();
            Produk::find($request->id)->update($request->except('id'));
            DB::commit();
            return redirect()->route('product.index')->with(['msg' => 'Berhasil Mengubah Data Produk', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('product.edit')->with(['msg' => 'Gagal Mengubah Data Produk', 'type' => 'danger']);
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            Produk::find($id)->delete();
            DB::commit();
            return redirect()->route('product.index')->with(['msg' => 'Berhasil Menghapus Data Produk', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('product.index')->with(['msg' => 'Gagal Menghapus Data Produk', 'type' => 'danger']);
        }
    }
}
