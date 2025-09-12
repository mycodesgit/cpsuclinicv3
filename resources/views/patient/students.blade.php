@extends('layouts.masterlayout')

@section('body')

<div class="row">
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <div class="">
                    <div class="page-header" style="border-bottom: 1px solid #04401f;">
                        <h4>Menu</h4>
                    </div>
                    @include('menu.sidemenu')
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Upcoming Students</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover" id="studentdata" style="width: 100%">
                            <thead>
                                <tr>
                                    <th class="text-left">Name</th>
                                    <th class="text-left">StudID</th>
                                    <th class="text-left">Gender</th>
                                    <th class="text-left">Civil Status</th>
                                    <th class="text-left">Course</th>
                                    <th class="text-left">Status</th>
                                    <th class="text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($enrolledstud as $datadatas)
                                    <tr>
                                    <td>{{ $datadatas->lname }}</td>
                                    <td>{{ $datadatas->stud_id }}</td>
                                    <td>{{ $datadatas->gender }}</td>
                                    <td>{{ $datadatas->progCod }}</td>
                                    <td>{{ $datadatas->id }}</td>
                                    </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewStudHisModal" role="dialog" aria-labelledby="viewStudHisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <input type="text" name="id" id="viewStudHisId" hidden>
                <h5 class="modal-title" id="viewStudHisModalLabel">Enrollment History of <span id="studentName"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="ss" class="table table-striped">
                    <thead>
                        <tr>
                            <th>StudentID</th>
                            <th>School Year</th>
                            <th>Semester</th>
                            <th>Course</th>
                            <th>Year Level</th>
                            <th>Section</th>
                        </tr>
                    </thead>
                    <tbody id="enrollmentHistoryTable">
                        <!-- Enrollment history will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
    
<script>
    var studentsReadRoute = "{{ route('patients.fetch') }}";
    var studenhistoryClickReadRoute = "{{ route('fetchStudEnrollmentHistory') }}";
</script>

@endsection