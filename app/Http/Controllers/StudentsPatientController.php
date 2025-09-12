<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\ClinicDB\Patients;
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

class StudentsPatientController extends Controller
{
    public function patientAdd() 
    {
        $regions = Region::all();
        $col = College::whereIn('id', [2, 3, 4, 5, 6, 7, 8])->get();
        $prog = EnPrograms::orderBy('progAcronym', 'ASC')->get();
        $patients = Patients::all();
        
        //$offices = Office::all();
        return view('patient.addpatient', compact('patients', 'regions', 'col', 'prog'));
    }

    public function studentsRead() 
    {
        $sy = ConfigureCurrent::select('id', 'schlyear')
            ->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('settings_conf')
                    ->groupBy('schlyear');
            })
            ->orderBy('id', 'DESC')
            ->get();

        return view('patient.students', compact('sy'));
    }

    public function studentsShow(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $data = Patients::whereYear('patients.created_at', $currentYear)
            ->select('patients.*')
            ->orderBy('patients.created_at', 'ASC')
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => Crypt::encryptString($patient->id),
                    'fname' => $patient->fname,
                    'lname' => $patient->lname,
                    'mname' => $patient->mname,
                    'stdntid' => $patient->stdntid,
                    'sex' => $patient->sex,
                    'studCourse' => $patient->studCourse,
                    'c_status' => $patient->c_status,
                    'pexam_remarks' => $patient->pexam_remarks,
                ];
            });
        
        return response()->json(['data' => $data]);
    }

    public function studentsMoreInfo($id)
    {
        $decryptedId = Crypt::decryptString($id);
        
        $patients = Patients::where('id', $decryptedId)->first();
        $regions = Region::all();
        $hprovinces = Province::where('region_id', $patients->home_region)->get();
        $hcities = City::where('city_id', $patients->home_city)->get();
        $hbarangays = Barangay::find($patients->home_brgy);

        $gprovinces = Province::where('region_id', $patients->guardian_region)->get();
        $gcities = City::where('city_id', $patients->guardian_city)->get();
        $gbarangays = Barangay::find($patients->guardian_brgy);

        $campus = Auth::guard('web')->user()->campus;

        $campusArray = array_map('trim', explode(',', $campus));

        $col = College::whereIn('id', [2, 3, 4, 5, 6, 7, 8])
                ->get();

        // $offices = Office::all();
   
        if (is_null($id)) {
            return redirect()->back()->with('error', 'ID cannot be null.');
        }

        return view('patient.studentsmoreinfo', compact('patients', 'regions', 'hprovinces', 'hcities', 'hbarangays', 'gbarangays', 'gprovinces', 'gcities', 'gbarangays', 'col'));
    }

    public function studentsUpdate(Request $request)
    {
        $patient = Patients::findOrFail($request->id);
        $column = $request->column;
        if ($column == 'birthdate') {
            $birthdate = Carbon::parse($request->value);
            $age = $birthdate->age;
            $patient->update([
                $column => $request->value,
                'age' => $age
            ]);
        } else {
            $patient->update([
                $column => $request->value
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function studentsHistory(Request $request)
    {
        $patient = Patients::find($request->id);
        $column = $request->column;
        $value = $request->value;
        $array = $request->data_array; 

        $arrayVal = $patient->$column;
        $arrayVal = explode(",", $arrayVal);
        $currentValue = isset($arrayVal[$array]) ? $arrayVal[$array] : null;
        $newvalue = $currentValue === $value ? '' : $value;
        $arrayVal[$array] = $newvalue;
        $newarrayVal = implode(",", $arrayVal);
        $patient->$column = $newarrayVal;
        $patient->save();
        
    
        return response()->json(['success' => true]);
    }

    public function studentsDelete($id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $studpatient = Patients::findOrFail($decryptedId);
            $studpatient->delete();

            return response()->json(['success' => true, 'message' => 'Deleted Successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Delete Failed: ' . $e->getMessage()], 500);
        }
    }

    public function fetchStudEnrollmentHistory(Request $request)
    {
        $stdntid = $request->input('stdntid');
        $campus = Auth::guard('web')->user()->campus;

        $campusArray = array_map('trim', explode(',', $campus));

        $enrollmentHistory = StudEnrolmentHistory::join('coasv2_db_schedule.programs', 'program_en_history.progCod', '=', 'coasv2_db_schedule.programs.progCod')
            ->where('program_en_history.studentID', $stdntid)
            ->where(function ($q) use ($campusArray) {
                foreach ($campusArray as $campus) {
                    $q->orWhere('program_en_history.campus', 'LIKE', "%$campus%");
                }
            })
            ->select('program_en_history.*', 'coasv2_db_schedule.programs.progAcronym')
            ->orderBy('schlyear', 'ASC')
            ->get();

        return response()->json(['data' => $enrollmentHistory]);
    }
}
