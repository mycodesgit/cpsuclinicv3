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
                    <form action="{{ route('reportsearch_MedicineDataRead') }}" method="GET">
                        @csrf
                        <div class="form-group" style="padding: 10px">
                            <div class="form-row">
                                <div class="col-md-3">
                                    <label><span class="badge badge-secondary">Select Month</span></label>
                                    <select class="form-control form-control-sm" name="month" id="monthSelect">
                                        <option disabled selected> --Select-- </option>
                                        @foreach(range(1,12) as $m)
                                            <option value="{{ sprintf('%02d', $m) }}" {{ request()->get('month') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{-- <script>
                                        document.getElementById('monthSelect').addEventListener('change', function() {
                                            alert('Selected month: ' + this.value);
                                        });
                                    </script> --}}
                                </div>

                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="form-control form-control-sm btn btn-success btn-sm">OK</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <iframe src="{{ route('reportsearchpdf_MedicineDataRead', ['month' => request('month')]) }}" width="100%" height="500"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection
