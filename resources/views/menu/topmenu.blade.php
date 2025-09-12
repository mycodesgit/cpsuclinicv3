@php
    $current_route=request()->route()->getName();
@endphp

<ul class="navbar-nav">
    <li class="nav-item">
        <a href="{{ route('dash') }}" class="nav-link {{$current_route=='dash'?'active':''}}">
           <i class="fas fa-th-large"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('patients.add') }}" class="nav-link {{ request()->is('patient/*') || request()->is('file*') ? 'active' : '' }}">
           <i class="fas fa-user-injured"></i> Pre-Entrance Exam
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('consultPatientRead') }}" class="nav-link {{ request()->is('patient-visit*') ? 'active' : '' }}">
           <i class="fas fa-user-injured"></i> Patients Visit
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('medicineRead') }}" class="nav-link {{ request()->is('medicine*') ? 'active' : '' }}">
           <i class="fas fa-pills"></i> Medicines
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('reportPatientDataRead') }}" class="nav-link {{ request()->is('reports') || request()->is('reports/*') ? 'active' : '' }}">
        <i class="fas fa-file-pdf"></i> Reports
    </a>
    </li>
    @if(auth()->user()->role == 'Administrator')
    <li class="nav-item">
        <a href="" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
            <i class="fas fa-users-cog"></i> Users
        </a>
    </li>
    @endif
    <li class="nav-item">
        <a href="" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}">
           <i class="fas fa-cog"></i> Settings
        </a>
    </li>
</ul>