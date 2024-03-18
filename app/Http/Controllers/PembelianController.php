<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\PembelianDataTable;
use Carbon\Carbon;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Facades\Session;

class PembelianController extends Controller
{
    public function index(PembelianDataTable $table)
    {
        Session::forget('data-pembelian');
        return $table->render('Pembelian.index');
    }

    public function create()
    {
        $produk = Produk::get()->pluck('namaProduk', 'id');
        $sessiondata = Session::get('data-pembelian');
        return view('Pembelian.add', compact('sessiondata', 'produk'));
    }

    public function processCreate(Request $request)
    {
        $item = Session::get('data-pembelian');
        try {
            DB::beginTransaction();

            $sales = Pembelian::create([
                'tglPembelian' => Carbon::now()->format('Y-m-d'),
                'totalHarga' => $request->totalHarga,
            ]);

            foreach ($item as $k => $v) {
                $itm = Produk::find($k);
                $itm->stok = ($itm->stok+$v[0]);
                $itm->save();

                $sales->detail()->create([
                    'produk_id' => $k,
                    'jmlProduk' => $v[0],
                    'subtotal' => ($v[0]*$itm->harga)
                ]);
            }

            DB::commit();
            return redirect()->route('purchase.index')->with(['msg' => 'Berhasil Menambahkan Data Pembelian', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            report($e);
            return redirect()->route('purchase.add')->with(['msg' => 'Gagal Menambahkan Data Pembelian', 'type' => 'danger']);
        }
    }

    public function addPurchaseItem(Request $request)
    {
        $data = collect(Session::get('data-pembelian'));
        $prd = Produk::find($request->id);
        $data = $data->put($request->id, [($request->qty??1), $prd->harga]);
        Session::put('data-pembelian', $data->toArray());
        return redirect()->route('purchase.add');
    }

    public function deletePurchaseItem($id)
    {
        $data = collect(Session::get('data-pembelian'));
        $data = $data->forget($id);
        Session::put('data-pembelian', $data->toArray());
        return redirect()->route('purchase.add');
    }

    public function detailPurchase($id)
    {
        $sessiondata = Session::get('data-pembelian');
        $produk = Produk::get()->pluck('namaProduk', 'id');
        $pembelian = Pembelian::with('detail.produk')->find($id);
        return view('Pembelian.detail', compact('sessiondata', 'produk', 'pembelian'));
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $pjl = Pembelian::with('detail')->find($id);

            foreach ($pjl->detail as $value) {
                $itm = Produk::find($value->produk_id);
                $itm->stok = ($itm->stok-$value->jmlProduk);
                $itm->save();
            }

            $pjl->delete();

            DB::commit();
            return redirect()->route('purchase.index')->with(['msg' => 'Berhasil Menghapus Data Pembelian', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('purchase.index')->with(['msg' => 'Gagal Menghapus Data Pembelian', 'type' => 'danger']);
        }
    }
    
    public function filter(Request $request) {
          $filter = Session::get('filter');
          $filter['start_date'] = $request->start_date;
          $filter['end_date'] = $request->end_date;
          Session::put('filter',$filter);
    }
}
