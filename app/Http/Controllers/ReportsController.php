<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

use App\Models\ClinicDB\Patients;
use App\Models\ClinicDB\Patientvisit;
use App\Models\ClinicDB\PatientReferral;
use App\Models\ClinicDB\Complaint;
use App\Models\ClinicDB\Medicine;
use App\Models\ClinicDB\File;
use App\Models\ClinicDB\Course;
use App\Models\ClinicDB\Office;

use App\Models\EnrollmentDB\StudEnrolmentHistory;
use App\Models\EnrollmentDB\Student;

use App\Models\ScheduleDB\College;
use App\Models\ScheduleDB\EnPrograms;

use App\Models\SettingDB\ConfigureCurrent;
use App\Models\SettingDB\Region;
use App\Models\SettingDB\Province;
use App\Models\SettingDB\City;
use App\Models\SettingDB\Barangay;

class ReportsController extends Controller
{
    public function reportPatientDataRead()
    {
        return view('reports.list');
    }

    public function reportPatientDataShow($id)
    {
        $decryptedId = Crypt::decryptString($id);

        $patientSearch = Patients::select('id', 'fname', 'lname', 'mname'   )->where('id', $decryptedId)->get();
        $patientVisit = Patientvisit::leftJoin('patients', 'patientvisits.stid', '=', 'patients.id')
                ->select(
                    'patientvisits.*',
                    'patients.fname',
                    'patients.mname',
                    'patients.lname',
                    'patientvisits.created_at as createdpvisit')
                ->where('stid', $decryptedId)
                ->get();
        $complaints = Complaint::all()->keyBy('id');

        $meddatas = Medicine::all();
        $meddata = [];
        $quantity=[];
        foreach ($meddatas as $data) {
            $meddata[$data->id] = $data->medicine;
            $quantity=[$data->id] =$data->qty;
        }

        $files = File::where('patient_id', $decryptedId)->get();
        $referral = PatientReferral::where('stid', $decryptedId)->get();

        return view('reports.list', compact('patientSearch', 'patientVisit', 'complaints', 'meddata','quantity', 'files',  'referral', 'id'));
    }

    public function peheReport($id)
    {
        $patients = Patients::where('patients.id', $id)
        ->select('patients.*', 'patients.created_at as createdas')
        ->first();

        $hregion = !empty($patients->home_region) ? Region::find($patients->home_region) : null;
        $hprovince  = !empty($patients->home_province) ? Province::where('province_id', $patients->home_province)->first() : null;
        $hcity = !empty($patients->home_city) ? City::where('city_id', $patients->home_city)->first() : null;
        $hbarangay = !empty($patients->home_brgy) ? Barangay::find($patients->home_brgy) : null;
        $gregion = !empty($patients->guardian_region) ? Region::find($patients->guardian_region) : null;
        $gprovince = !empty($patients->guardian_province) ?  Province::where('province_id', $patients->guardian_province)->first() : null;
        $gcity = !empty($patients->guardian_city) ? City::where('city_id', $patients->guardian_city)->first() : null;
        $gbarangay = !empty($patients->guardian_brgy) ? Barangay::find($patients->guardian_brgy) : null;

                    
        $pdf = PDF::loadView('reports.listmhistorypdf', compact('patients', 'hregion', 'hprovince', 'hcity', 'hbarangay', 'gregion', 'gprovince', 'gcity', 'gbarangay', 'id'))->setPaper('Legal', 'portrait');
        return $pdf->stream();
    }

    public function reportMedicineDataRead()
    {
        return view('reports.listmedrep');
    }
}
