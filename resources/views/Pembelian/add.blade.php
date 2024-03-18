@extends('adminlte::page')

@section('title', 'Pembelian')

@section('content_header')
    <h1>Tambah Pembelian</h1>
@stop

@section('content')
    @if (session('msg'))
        <div class="alert alert-{{ session('type') ?? 'info' }}" role="alert">
            {{ session('msg') }}
        </div>
    @endif
    @if (count($errors) > 0)
        <div class="alert-danger" role="alert">
            @foreach ($errors as $e)
                {{ $e }}
            @endforeach
        </div>
    @endif
    <div class="card border border-dark">
        <div class="card-body">
            <form action="{{ route('purchase.add-item') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col">
                        <x-adminlte-select2 name="id" label="Produk" class="required" autocomplete="off" data-placeholder="Pilih Produk...">
                            <option/>
                            @foreach ($produk as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </x-adminlte-select2>
                        <x-adminlte-input name="qty" label="Jumlah Produk" type="number" placeholder="Masukkan Jumlah Barang" value="1" />
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <x-adminlte-button class="btn" type="reset" label="Reset" theme="danger" icon="fas fa-trash" />
                    <x-adminlte-button class="btn" type="submit" label="Tambah" theme="success" icon="fas fa-plus" />
                </div>
            </div>
        </form>
    </div>
    <div class="card border border-dark">
        <div class="card-header bg-dark clearfix">
            <h5 class="mb-0 float-left">
                Daftar Produk
            </h5>
            <div class="form-actions float-right">
                <a href="{{ route('purchase.index') }}" name="Find" class="btn btn-sm btn-primary" title="Back">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <th class="text-center">No.</th>
                        <th class="text-center">Nama Produk</th>
                        <th class="text-center">Harga</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Subtotal</th>
                        <th class="text-center">Aksi</th>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                            $total = 0;
                        @endphp
                        @if (empty($sessiondata))
                            <tr>
                                <td colspan="6" class="text-center">Data Kosong</td>
                            </tr>
                        @else
                            @foreach ($sessiondata as $k => $v)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td>{{ $produk[$k] }}</td>
                                    <td class="text-right">{{ number_format($v[1],2) }}</td>
                                    <td class="text-center">{{ $v[0] }}</td>
                                    <td class="text-right">{{ number_format($v[0]*$v[1],2) }}</td>
                                    <td class="text-center">
                                        <x-adminlte-button class="btn-sm" theme="danger" icon="fas fa-trash" onclick="location.href='{{ route('purchase.delete-item') }}';" />
                                    </td>
                                </tr>
                                @php
                                    $total += ($v[0]*$v[1])
                                @endphp
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-center font-weight-bold">Total</td>
                            <td colspan="2" class="text-center">{{ number_format($total,2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="card border border-dark">
        <div class="card-body">
            <form action="{{ route('purchase.add-process') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col">
                        <x-adminlte-input name="totalHarga" label="Total Pembelian" value="{{ $total }}" readonly />
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <x-adminlte-button class="btn" type="reset" label="Reset" theme="danger" icon="fas fa-trash" />
                    <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-save" />
                </div>
            </div>
        </form>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="">
@stop

@section('js')
    <script></script>
@stop

@section('footer')
    <i class="fas fa-copyright"></i>created by someone that still breating
@stop