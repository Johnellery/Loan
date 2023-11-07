<?php

use App\Models\Applicant;
use Illuminate\Support\Facades\Auth;

$query = Applicant::query(); // Use query() to start a new query.
$user = Auth::user();
$application = $query->where(function ($query) use ($user) {
    if ($user->role->name === 'Admin') {
        $query->where('status', 'approved')
              ->where('ci_status', 'approved');
    } elseif ($user->role->name === 'Staff' || $user->role->name === 'Collector') {
        $query->where('branch_id', $user->branch_id)
              ->where('ci_status', 'approved')
              ->where(function ($query) {
                  $query->where('status', 'approved');
              });
    }
})->get();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Bisikleta Bike Shop - Loan Summary</title>
    <!-- Include Tailwind CSS via CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include select2 CSS and JS files -->
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script> --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        /* Add custom CSS styles here */
        body {
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .form-label {
            font-weight: 600;
        }

        .form-input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
        }

        .form-button {
            background-color: #007bff;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            float: right; /* Move the button to the right */
        }

        .form-button:hover {
            background-color: #0056b3;
        }

        h1 {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }

        .warning-button {
            background-color: #ff6347; /* Warning color (Tomato) */
            color: #fff;
            padding: 12px 20px;
            border: none;
            float: right;
            border-radius: 4px;
            cursor: pointer;
        }

        .logo {
            width: 100px;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>

<body>
    <div class="form-container p-4 rounded-lg shadow-md bg-white">
        <form method="get" action="{{ route('summary') }}">
            @csrf
            <div class="form-group">
                <img src="{{ asset('storage/dashboardpage/logo.png') }}" alt="Bisikleta" class="logo">
                <h1>Bisileta Bike Shop</h1>
                <label for="applicant">Loan Summary</label>
                <select id="applicant" data-live-search="true" name="applicant[]" class="form-control selectpicker" multiple required>
                    @foreach($application as $applicant)
                        <option value="{{ $applicant->id }}">{{ $applicant->customer_name }}</option>
                    @endforeach
                </select>

            </div>
            <button type="button" class="btn btn-danger" onclick="window.location.href='/admin/applicants'">Cancel</button>
            <button type="submit" style="margin-left: 10px;" class="btn btn-primary">Generate Loan Summary</button>
        </form>
    </div>
</body>

</html>

{{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script> --}}
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function () {
        $('#applicant').select2();

        // Add an event listener to the form submission
        $('form').submit(function () {
            // Get the selected options count
            var selectedCount = $('#applicant').val().length;

            // Check if at least one customer is selected
            if (selectedCount === 0) {
                // Display an error message
                alert('Please select at least one customer.');
                return false; // Prevent form submission
            }
        });
    });
</script>
