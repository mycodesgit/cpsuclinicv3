@extends('layouts.masterlayout')

@section('body')
    <style>
        .mtop {
            margin-top: -15px;
        }
    </style>

    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <div class="page-header" style="border-bottom: 1px solid #04401f;">
                            <h4>Menu</h4>
                        </div>
                        <div class="mt-3" style="font-size: 13pt;">
                            @include('menu.sidemenu')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <div class="page-header" style="border-bottom: 1px solid #04401f;">
                        <h4>Reports </h4>
                    </div>
                    <form action="{{ route('reportStockMedDatasearchRead') }}" method="GET" id="stockRep">
                        @csrf
                        <div class="form-group" style="padding: 10px">
                            <div class="form-row">
                                <div class="col-md-3">
                                    <label><span class="badge badge-secondary">Select Medicine</span></label>
                                    <select class="form-control form-control-sm" id="medicine-dropdown" name="medicine_id">
                                        <option disabled selected> --Select-- </option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="form-control form-control-sm btn btn-success btn-sm">OK</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>

                    <iframe src="{{ route('reportpdfStockMedDataRead', ['medicine' => request('medicine')]) }}" width="100%" height="500"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection
