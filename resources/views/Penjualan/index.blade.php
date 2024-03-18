@extends('adminlte::page')

@section('title', 'Penjualan')

@section('content_header')
    <h1>Daftar Penjualan</h1>
@stop

@section('content')
    @if (Auth::user()->level == "admin")
        <div id="accordion">
            <form action="{{ route('sales.filter') }}" method="post">
                @csrf
                <div class="card border border-dark">
                    <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <h5 class="mb-0">
                            Filter
                        </h5>
                    </div>
                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input">
                                        <section class="control-label">Tanggal Mulai
                                            <span class="required text-danger">
                                                *
                                            </span>
                                        </section>
                                        <x-adminlte-input type="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="{{ $filter['start_date'] ?? date('Y-m-d') }}" style="width: 15rem;" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-md-line-input">
                                        <section class="control-label">Tanggal Akhir
                                            <span class="required text-danger">
                                                *
                                            </span>
                                        </section>
                                        <x-adminlte-input type="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="{{ $filter['end_date'] ?? date('Y-m-d') }}" style="width: 15rem;" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <div class="form-actions float-right">
                                <a href="{{ route('sales.filter-reset') }}" class="btn btn-danger" type="reset" name="Reset"><i class="fa fa-times"></i> Batal</a>
                                <x-adminlte-button class="btn" type="submit" label="Cari" theme="primary" icon="fas fa-search" title="Search Data" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif
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
        <div class="card-header bg-dark clearfix">
            <h5 class="mb-0 float-left">
                Daftar
            </h5>
            <div class="form-actions float-right">
                @if(Auth::user()->level=="kasir")

                <a href="{{ route('sales.add') }}" name="Find" class="btn btn-sm btn-primary" title="Add Data">
                    <i class="fa fa-plus"></i> Tambah Data
                </a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                {{ $dataTable->table() }}
                @push('js')
                    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
                @endpush
            </div>
        </div>
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