<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentsPatientController;
use App\Http\Controllers\PatientVisitController;
use App\Http\Controllers\PatientVisitReferralController;
use App\Http\Controllers\PatientvisitToothExtractController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ReportsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware'=>['guest']],function(){
    Route::get('/', function () {
        return view('login');
    });

    Route::get('/login',[LoginController::class,'loginView'])->name('getLogin');
    Route::post('/login/auth',[LoginController::class,'loginAuthenticate'])->name('postLogin');
});

Route::group(['middleware'=>['login_auth']],function(){
    Route::get('/dashboard',[DashboardController::class,'dash'])->name('dash');
    Route::get('/logout',[DashboardController::class,'logout'])->name('logout');

    Route::prefix('/patient')->group(function () {
        Route::get('/add',[StudentsPatientController::class,'patientAdd'])->name('patients.add');
        Route::get('/students',[StudentsPatientController::class,'studentsRead'])->name('patients.students');
        Route::get('/students/list',[StudentsPatientController::class,'studentsShow'])->name('patients.fetch');
        Route::get('/student/show/moreinfo/{id}', [StudentsPatientController::class,'studentsMoreInfo'])->name('studentsMoreInfo');
        Route::post('/student/show/moreinfo/update', [StudentsPatientController::class, 'studentsUpdate'])->name('studentsUpdate');
        Route::post('/student/show/medical/history/update', [StudentsPatientController::class, 'studentsHistory'])->name('studentsHistory');
        Route::post('students/delete/{id}', [StudentsPatientController::class, 'studentsDelete'])->name('patients.Delete');

        Route::get('student/list/show/search/file/{id}', [FileController::class, 'fileRead'])->name('fileRead');
        Route::post('student/list/show/search/file/file-create/{id}', [FileController::class, 'fileCreate'])->name('fileCreate');
        Route::delete('student/list/show/search/file/file/deleteFile/{id}', [FileController::class, 'deleteFile'])->name('deleteFile');
    });

    Route::prefix('patient-visit')->group( function(){
        Route::get('/patientListOption', [PatientVisitController::class, 'patientListOption'])->name('patientListOption');

        Route::get('/view/consult', [PatientVisitController::class, 'consultPatientRead'])->name('consultPatientRead');
        Route::get('/view/consult/list/{id}', [PatientVisitController::class, 'consultPatientVisitSearch'])->name('consultPatientVisitSearch');
        Route::get('/view/consult/list/getajax/{id}', [PatientVisitController::class, 'getconsultPatientVisitSearch'])->name('getconsultPatientVisitSearch');
        Route::post('/view/consult/list/listadd', [PatientVisitController::class, 'addPatient'])->name('addPatient');

        Route::get('/view/refer', [PatientVisitReferralController::class, 'patientReferRead'])->name('patientReferRead');
        Route::get('/view/refer/list/{id}', [PatientVisitReferralController::class, 'referPatientVisitSearch'])->name('referPatientVisitSearch');
        Route::post('/view/refer/list/add', [PatientVisitReferralController::class, 'referralCreate'])->name('referralCreate');
        Route::get('/view/refer/list/viewlistajax/{id}', [PatientVisitReferralController::class, 'getreferralRead'])->name('getreferralRead');
        Route::post('/view/refer/list/update', [PatientVisitReferralController::class, 'referralUpdate'])->name('referralUpdate');
        Route::post('/view/refer/list/delete/{id}', [PatientVisitReferralController::class, 'referralDelete'])->name('referralDelete');
        Route::get('/view/referral/list/pdf/{id}', [PatientVisitReferralController::class, 'referralPDF'])->name('referralPDF');

        Route::get('/view/toothextraction', [PatientvisitToothExtractController::class, 'toothExtractRead'])->name('toothExtractRead');
        Route::get('/view/toothextraction/list/{id}', [PatientvisitToothExtractController::class, 'toothExtractSearch'])->name('toothExtractSearch');
    });

    Route::prefix('/medicines')->group(function(){
        Route::get('/list', [MedicineController::class, 'medicineRead'])->name('medicineRead');
        Route::get('/list/ajax', [MedicineController::class, 'getmedicineRead'])->name('getmedicineRead');
        Route::post('/medicine/add', [MedicineController::class, 'medicineCreate'])->name('medicineCreate');
        Route::post('/medicineUpdate', [MedicineController::class, 'medicineUpdate'])->name('medicineUpdate');
        Route::post('/medicineDelete/{id}', [MedicineController::class, 'medicineDelete'])->name('medicineDelete');
    });

    Route::prefix('/reports')->group(function (){
        Route::get('/', [ReportsController::class,'reportPatientDataRead'])->name('reportPatientDataRead');
        Route::get('/view/{id}', [ReportsController::class,'reportPatientDataShow'])->name('reportPatientDataShow');
        Route::get('/refused', [ReportsController::class,'refusedReport'])->name('refusedReport');
        Route::get('/waiver', [ReportsController::class,'waiverReport'])->name('waiverReport');
        Route::get('/pehe-report/{id}', [ReportsController::class,'peheReport'])->name('peheReport');
    });

});
