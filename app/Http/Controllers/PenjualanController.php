<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\PenjualanDataTable;
use Carbon\Carbon;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Facades\Session;

class PenjualanController extends Controller
{
    public function index(PenjualanDataTable $table)
    {
        Session::forget('data-penjualan');
        return $table->render('Penjualan.index');
    }

    public function create()
    {
        $produk = Produk::get()->pluck('namaProduk', 'id');
        $pelanggan = Pelanggan::get()->pluck('namaPelanggan', 'id');
        $sessiondata = Session::get('data-penjualan');
        return view('Penjualan.add', compact('sessiondata', 'produk', 'pelanggan'));
    }

    public function processCreate(Request $request)
    {
        $item = Session::get('data-penjualan');
        try {
            DB::beginTransaction();

            $sales = Penjualan::create([
                'tglPenjualan' => Carbon::now()->format('Y-m-d'),
                'totalHarga' => $request->totalHarga,
                'pelanggan_id' => $request->pelanggan_id
            ]);

            foreach ($item as $k => $v) {
                $itm = Produk::find($k);
                $itm->stok = ($itm->stok-$v[0]);
                $itm->save();

                $sales->detail()->create([
                    'produk_id' => $k,
                    'jmlProduk' => $v[0],
                    'subtotal' => ($v[0]*$itm->harga)
                ]);
            }

            DB::commit();
            return redirect()->route('sales.index')->with(['msg' => 'Berhasil Menambahkan Data Penjualan', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            report($e);
            return redirect()->route('sales.add')->with(['msg' => 'Gagal Menambahkan Data Penjualan', 'type' => 'danger']);
        }
    }

    public function addSalesItem(Request $request)
    {
        $data = collect(Session::get('data-penjualan'));
        $prd = Produk::find($request->id);
        $data = $data->put($request->id, [($request->qty??1), $prd->harga]);
        Session::put('data-penjualan', $data->toArray());
        return redirect()->route('sales.add');
    }

    public function deleteSalesItem($id)
    {
        $data = collect(Session::get('data-penjualan'));
        $data = $data->forget($id);
        Session::put('data-penjualan', $data->toArray());
        return redirect()->route('sales.add');
    }

    public function detailSales($id)
    {
        $sessiondata = Session::get('data-penjualan');
        $produk = Produk::get()->pluck('namaProduk', 'id');
        $pelanggan = Pelanggan::get()->pluck('namaPelanggan', 'id');
        $penjualan = Penjualan::with('detail.produk', 'pelanggan')->find($id);
        return view('Penjualan.detail', compact('sessiondata', 'produk', 'pelanggan', 'penjualan'));
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $pjl = Penjualan::with('detail')->find($id);

            foreach ($pjl->detail as $value) {
                $itm = Produk::find($value->produk_id);
                $itm->stok = ($itm->stok+$value->jmlProduk);
                $itm->save();
            }

            $pjl->delete();

            DB::commit();
            return redirect()->route('sales.index')->with(['msg' => 'Berhasil Menghapus Data Penjualan', 'type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('sales.index')->with(['msg' => 'Gagal Menghapus Data Penjualan', 'type' => 'danger']);
        }
    }

    public function printSales($id)
    {
        $penjualan = Penjualan::with('detail.produk', 'pelanggan')->find($id);
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Set margins
        $pdf::SetHeaderMargin(5);
        $pdf::SetFooterMargin(10);

        // Add a page
        $pdf::AddPage();

        // Set header and footer fonts
        $pdf::setHeaderFont(Array('helvetica', '', 10));
        $pdf::setFooterFont(Array('helvetica', '', 8));

        // Output nama toko dan alamat toko
        $pdf::Cell(0, 10, 'Nama Toko', 0, 1, 'C');
        $pdf::Cell(0, 10, 'Alamat Toko', 0, 1, 'C');

        // Content
        $content = '
            <hr>
            <h3>Struk Pembelian</h3>
            <p><strong>Tanggal Penjualan:</strong> ' . $penjualan->tglPenjualan . '</p>
            <p><strong>Nama Pelanggan:</strong> ' . ($penjualan->pelanggan ? $penjualan->pelanggan->namaPelanggan : '-') . '</p>
            <table border="0" cellpadding="5">
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>';

        // Loop through sale details
        foreach ($penjualan->detail as $value) {
            $content .= '
                <tr>
                    <td>' . $value->produk->namaProduk . '</td>
                    <td>' . 'Rp. ' .number_format($value->produk->harga,2) . '</td>
                    <td>' . $value->jmlProduk . '</td>
                    <td>' . 'Rp. ' . number_format($value->subtotal,2) . '</td>
                </tr>';
        }

        // Total sale amount
        $content .= '
                <tr>
                    <td colspan="3" align="right"><strong>Total Pembelian Barang:</strong></td>
                    <td>' . 'Rp. ' . number_format($penjualan->totalHarga,2) . '</td>
                </tr>
            </table>';

        // Write the content
        $pdf::writeHTML($content, true, false, true, false, '');

        // Close and output PDF document
        $pdf::Output('struk_pembelian.pdf', 'I');
    }

}
