<?php

use App\Models\Applicant;

$application = Applicant::where('status', 'approved')
                        ->where('ci_status', 'approved')
                        ->get();

?>
<!DOCTYPE html>
<html>

<head>
    <title>Bisikleta Bike Shop - Loan Summary</title>
    <!-- Include Tailwind CSS via CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include select2 CSS and JS files -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <style>
        /* Add custom CSS styles here */
        body {
            background-color: #f7f7f7;
        }

        .form-container {
            max-width: 400px;
            margin: 0 auto;
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
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="form-container p-4 rounded-lg shadow-md bg-white">
        <form method="get" action="{{ route('summary') }}">
            @csrf
            <select id="applicant" name="applicant[]" class="block w-full p-3 rounded-md border border-gray-300 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6" multiple>
                <option disabled selected>Choose Customers</option>
                @foreach($application as $applicant)
                    <option value="{{ $applicant->id }}">{{ $applicant->customer_name }}</option>
                @endforeach
            </select>


            <button type="submit"
                class="mt-3 p-3 bg-blue-500 text-white rounded-md cursor-pointer hover:bg-blue-700">Generate Loan Summary</button>
        </form>
    </div>
</body>

</html>
<script>
    $(document).ready(function() {
        $('#applicant').select2();
    });
</script>
