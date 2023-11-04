<?php

use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\summaryController;
use App\Models\Applicant;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MailController;
use App\Http\Controllers\Mail1Controller;
use App\Http\Controllers\DownloadPdfController;
use App\Http\Controllers\LoanApplicantController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\receiptController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TermsController;
use App\Livewire\Transaction;
use App\Livewire\BikeList;
use Livewire\Livewire;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Route::get('send-mail', [MailController::class, 'index']);
Route::get('/', function () {
    return view('welcome');
});
// Routes
Route::get('/customer/loan/{bike_id}', function  ($bike_id) {
    $bikeId = request('bike_id'); // Get the bike ID from the query parameter
    return view('layouts.customer.loan', compact('bike_id'));
})->name('customer.loan');

Route::post('/create', function () {
    // Validate the form data

    request()->validate([
        'first' => 'required|string|max:255',
        'middle' => 'nullable|string|max:255',
        'last' => 'required|string|max:255',
        'age' => 'required|integer',
        'gender' => 'required|string|max:255',
        'civil' => 'required|string|max:255',
        'religion' => 'required|string|max:255',
        'occupation' => 'required|string|max:255',
        'spouse' => 'nullable|string|max:255',
        'contact_spouse' => 'nullable|string|max:255',
        'occupation_spouse' => 'nullable|string|max:255',
        'unit' => 'required|string|max:255',
        'barangay' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'province' => 'required|string|max:255',
        'term' => 'required|integer',
        'installment' => 'required|integer',
        'file' => 'required',
        'clearance' => 'required',
        'picture' => 'required',

    ]);
    // Create a new Applicant instance and save it to the database
    Applicant::create([
        'first' => request('first'),
        'middle' => request('middle'),
        'last' => request('last'),
        'age' => request('age'),
        'gender' => request('gender'),
        'civil' => request('civil'),
        'religion' => request('religion'),
        'occupation' => request('occupation'),
        'spouse' => request('spouse'),
        'contact_spouse' => request('contact_spouse'),
        'occupation_spouse' => request('occupation_spouse'),
        'unit' => request('unit'),
        'barangay' => request('barangay'),
        'city' => request('city'),
        'province' => request('province'),
        'term' => request('term'),
        'installment' => request('installment'),
        'valid_id' => request('file'),
        'bike_id' => request('bike_id'),
        'branch_id' => request('branch_id'),
        'user_id' => request('user_id'),
        'barangay_clearance' => request('clearance'),
        'picture' => request('picture'),
        'contact' => request('contact'),
    ]);

    // Redirect to a success page or any other page you prefer
    return redirect('/customer/home'); // Adjust the route name accordingly
});
Route::get('/livewire/newcart', function () {
    return redirect('/customer/home');
})->name('livewire.newcart');


// Route::post('applicants', [LoanApplicantController::class, 'store'])->name('applicants.store');
Route::get('/{record}/send1', [MailController::class, 'index'])->name('repossession.index');
Route::get('/{record}/send', [Mail1Controller::class, 'index'])->name('repossession1.index');
Route::get('/{record}/pdf/download', [DownloadPdfController::class, 'download'])->name('billing.pdf.download');
Route::get('/customer/{bike:name}', [BikeList::class, 'show'])->name('customer.show');
Route::get('/layouts/customer/show/{bike_id}', function ($bike_id) {
    $bike = App\Models\Bike::findOrFail($bike_id);
    return view('layouts.customer.show', compact('bike'));
})->name('customer.show');
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
Route::get('/livewire/show/{bike_id}', function ($bike_id) {
    $bike = App\Models\Bike::findOrFail($bike_id);
    return view('livewire.show', compact('bike'));
})->name('bike.show');
Route::get('/{record}/generate-pdf', [PDFController::class, 'generatePDF'])->name('generate-pdf');
Route::get('/{record}/invoice', [receiptController::class, 'generateINVOICE'])->name('invoice');
// Route::get('/transactions', Transaction::class)->name('livewire.transaction');
// Route::get('pay', [PaymentController::class, 'pay'])->name('payment');
Route::get('pay/{record}', [PaymentController::class, 'pay'])->name('payment');
// Route::get('success', [PaymentController::class, 'success']);
// Route::get('error', [PaymentController::class, 'error']);
Route::get('success', [PaymentController::class, 'success'])->name('success');
Route::get('error', [PaymentController::class, 'error'])->name('error');
Route::get('/terms', [TermsController::class, 'show'])->name('terms.show');
Route::get('/privacy', [PrivacyController::class, 'show'])->name('terms.privacy');
Route::get('/reports', function(){
    return 'reports';
})->name('terms.reports');
Route::get('/report-pdf', [ReportController::class, 'reportPDF'])->name('report-pdf');

// Generate and download the PDF report
Route::get('/report-pdf-form', [ReportController::class, 'reportPDForm'])->name('report-pdf-form');
Route::get('/summary', [summaryController::class, 'summary'])->name('summary');
Route::get('/summary-form', [summaryController::class, 'summary_form'])->name('summary-form');
