<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use App\Models\ClinicDB\Patients;
use App\Models\ClinicDB\Medicine;

use App\Models\ClinicDB\Patientvisit;
use App\Models\ClinicDB\File;
use App\Models\ClinicDB\Complaint;

class PatientVisitController extends Controller
{
    public function patientListOption(Request $request)
    {
        $pageSize = 1000;
        $page = $request->input('page', 1);

        $patients = Patients::select('id', 'fname', 'lname', 'mname')
                            ->paginate($pageSize, ['*'], 'page', $page);

        $encryptedItems = collect($patients->items())->map(function ($patient) {
            return [
                'id' => Crypt::encryptString($patient->id), 
                'fname' => $patient->fname,
                'lname' => $patient->lname,
                'mname' => $patient->mname,
            ];
        });

        return response()->json([
            'data' => $encryptedItems,
            'pagination' => [
                'more' => $patients->hasMorePages(),
            ],
        ]);
    }

    public function consultPatientRead(Request $request)
    {
        $date = date('Y-m-d');
        date_default_timezone_set('Asia/Manila');
        
        return view('patientvisit.listconsult', compact('date'));   
    }

    public function consultPatientVisitSearch(Request $request, $id)
    {
        $date = date('Y-m-d');
        date_default_timezone_set('Asia/Manila');

        $decryptedId = Crypt::decryptString($id);

        $files = File::where('patient_id', $decryptedId)->get();  
        $patientSearch = Patients::select('id', 'fname', 'lname', 'mname'   )->where('id', $decryptedId)->first();
        $patientVisit = Patientvisit::where('stid', $decryptedId)->get();
        
        $patients = Patients::select('id', 'fname', 'lname', 'mname')->get();
        $medicines = Medicine::all();
        $complaints =  Complaint::all();
        $meddata = [];
        $quantity=[];

        return view('patientvisit.listconsult', compact('patients','patientSearch','patientVisit', 'medicines', 'complaints', 'quantity','files','date'));
    }

    public function getconsultPatientVisitSearch($id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid ID'], 400);
        }

        $meddatas = Medicine::all();
        $meddata = [];
        foreach ($meddatas as $data) {
            $meddata[$data->id] = $data->medicine;
        }

        $patientVisit = Patientvisit::leftJoin('complaint', 'patientvisits.chief_complaint', '=', 'complaint.id')
                        ->where('stid', $decryptedId)
                        ->select(
                            'patientvisits.id as vid',
                            'patientvisits.date',
                            'patientvisits.time',
                            'patientvisits.medicine',
                            'patientvisits.qty',
                            'patientvisits.chief_complaint',
                            'complaint.complaint as complaintname',
                            'complaint.id as complaint_ids',
                            'patientvisits.treatment'
                        )
                        ->get();

            $data = $patientVisit->map(function ($visit) use ($meddata) {
            $medicineValues = explode(',', $visit->medicine ?? '');
            $quantities = explode(',', $visit->qty ?? '');

            $medQty = '';
            foreach ($medicineValues as $index => $medicineId) {
                if (isset($meddata[$medicineId]) && isset($quantities[$index])) {
                    $medQty .= e($meddata[$medicineId]) . ' - ' . e($quantities[$index]) . '<br>';
                }
            }

            return [
                'vid'        => $visit->vid,
                'date'       => $visit->date,
                'time'       => $visit->time,
                'medicines'  => $medQty,
                'medicine_ids'   => $visit->medicine, // e.g. "3,3"
                'qtys'           => $visit->qty, 
                'complaintname'  => $visit->complaintname,
                'complaint_ids'  => $visit->chief_complaint,
                'treatment'  => $visit->treatment,
            ];
        });

        return response()->json([
            'data' => $data
        ]);
    }

    public function addPatient(Request $request)
    {
        $patient = new Patientvisit();
        $patient->stid = $request->input('stid');
        $patient->date = $request->input('date');
        $patient->time = $request->input('time');

        $patient->chief_complaint = $request->input('chief_complaint');
        $patient->treatment = $request->input('treatment');
        $patient->certificate = $request->input('certificate');

        $input1 = $request->input('qty', []);  
        $input2 = $request->input('medicine', []);
        $input3 = $request->input('chief_complaint', []);

        $maxCount = max(count($input1), count($input2), count($input3));
        
        $input1 = array_pad($input1, $maxCount, '');  
        $input2 = array_pad($input2, $maxCount, '');
        $input3 = array_pad($input3, $maxCount, '');
        
        $input1 = array_map(function($value) {
            return $value === null ? '' : $value;
        }, $input1);
        
        $input2 = array_map(function($value) {
            return $value === null ? '' : $value;
        }, $input2);
        
        $input3 = array_map(function($value) {
            return $value === null ? '' : $value;
        }, $input3);
        
        $complaint = implode(',', $input3);
        
        if (substr($complaint, -1) === ',') {
            $complaint = rtrim($complaint, ',');
        }
        $quantity = implode(',', $input1);
        $medicine = implode(',', $input2);
        
        $patient->medicine = $medicine;
        
        $medicinesDetails = [];
        $medicines = explode(',', $medicine);
        $quantities = explode(',', $quantity);
        
        $quantityvisit = explode(',', $patient->qty);
        
        foreach ($medicines as $index => $med) {
            $medicine2 = Medicine::select('qty', 'id', 'medicine')->where('id', $med)->first();
            
            if ($medicine2) {
                $visitQuantity = isset($quantityvisit[$index]) ? $quantityvisit[$index] : 0;
                $newQuantity = ((int)$medicine2->qty + (int)$visitQuantity) - (int)$quantities[$index];
        
                if ($newQuantity >= 0) {
                    $medicine2->update(['qty' => $newQuantity]);
        
                    $medicinesDetails[] = [
                        'id' => $medicine2->id,
                        'medicine' => $medicine2->medicine,
                        'quantity' => $newQuantity
                    ];
                }
            }
        }
        
        $patient->chief_complaint = $complaint;
        
        $patient->qty = $quantity;
        
        $patient->save();

        return response()->json(['success' => true, 'message' => 'Added Successfully']);
    }
}
