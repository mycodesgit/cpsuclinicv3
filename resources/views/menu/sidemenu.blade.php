@php  
    $curr_route = request()->route()->getName();

    $patientAddActive = in_array($curr_route, ['patients.add']) ? 'active' : '';
    $patientStudentActive = in_array($curr_route, ['patients.students', 'studentsMoreInfo', 'fileRead']) ? 'active' : '';

    $consultPatientActive = in_array($curr_route, ['consultPatientRead', 'consultPatientVisitSearch']) ? 'active' : '';
    $referPatientActive = in_array($curr_route, ['patientReferRead', 'referPatientVisitSearch']) ? 'active' : '';
    $toothPatientActive = in_array($curr_route, ['toothExtractRead', 'toothExtractSearch']) ? 'active' : '';

    $patientDataReportActive = in_array($curr_route, ['reportPatientDataRead', 'reportPatientDataShow']) ? 'active' : '';
    $medicineDataReportActive = in_array($curr_route, ['reportMedicineDataRead', 'reportsearch_MedicineDataRead']) ? 'active' : '';
    $medicineStockDataReportActive = in_array($curr_route, ['reportStockMedDataRead']) ? 'active' : '';
@endphp


@if(request()->is('patient') || request()->is('patient/*'))
    <div class="mt-3" style="font-size: 13pt;">
        <div class="nav flex-column nav-pills nav-stacked nav-tabs-right h-100" aria-orientation="vertical">
            <a class="nav-link {{ $patientAddActive }}" href="{{ route('patients.add') }}">Add Patient</a>
            <a class="nav-link {{ $patientStudentActive }}" href="{{ route('patients.students') }}">Students</a>
            <a class="nav-link" href="">Employees</a>
        </div>
    </div>
@endif

@if(request()->is('patient-visit') || request()->is('patient-visit/*'))
    <div class="mt-3" style="font-size: 13pt;">
        <div class="nav flex-column nav-pills nav-stacked nav-tabs-right h-100" aria-orientation="vertical">
            <a href="{{ route('consultPatientRead') }}" class="nav-link {{ $consultPatientActive }}">Consultation</a>  
            <a href="{{ route('patientReferRead') }}" class="nav-link {{ $referPatientActive }}">Referral</a>       
            <a href="{{ route('toothExtractRead') }}" class="nav-link {{ $toothPatientActive }}" >Tooth Extraction</a>                                
        </div>
    </div>
@endif

@if(request()->is('medicines') || request()->is('medicines/*'))

@endif

@if(request()->is('reports') || request()->is('reports/*'))
    <div class="mt-3" style="font-size: 13pt;">
        <div class="nav flex-column nav-pills nav-stacked nav-tabs-right h-100" aria-orientation="vertical">
            <a class="nav-link {{ $patientDataReportActive }}" href="{{ route('reportPatientDataRead') }}">Patient Data Report</a>
            <a class="nav-link {{ $medicineDataReportActive }}" href="{{ route('reportMedicineDataRead') }}">Medicines Report</a>                                
            <a class="nav-link {{ $medicineStockDataReportActive }}" href="{{ route('reportStockMedDataRead') }}">Stock Report</a>                                
            <a class="nav-link" href="">Accomplishment Report</a>                                
        </div>
    </div>
@endif
