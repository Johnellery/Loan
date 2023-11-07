
<!DOCTYPE html>
<html>
<head>
    <title>Bisikleta Bike Shop - Reports</title>
    <!-- Include Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
    <div class="min-h-screen flex items-center justify-center">
        <div class="form-container">
            <img src="{{ asset('storage/dashboardpage/logo.png') }}" alt="Bisikleta" class="logo">
            <h1>Bisileta Bike Shop</h1>
            <form method="get" action="{{ route('report-pdf') }}">
                @csrf
                <label>Select Date:</label>
                <select id="date-select" name="date" required class="form-input">
                    <option disabled selected class="text-gray-500">Choose a Date</option>
                    <option value="today">Today</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                    <option value="custom">Custom Date</option>
                </select>

                <label id="from-label" style="display: none">Date from:</label>
                <input type="date" id="custom-date-from" name="custom_date_from" class="form-input" style="display: none">

                <label id="until-label" style="display: none">Date until:</label>
                <input type="date" id="custom-date-until" name="custom_date_until" class="form-input" style="display: none">

                <button type="submit" class="form-button" style="margin-left: 10px;">Generate PDF</button>
                <button type="button" class="warning-button"  onclick="window.location.href='/admin/applicants'">Cancel</button>
            </form>


        </div>
    </div>
</body>
</html>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script>
    const dateSelect = document.getElementById("date-select");
    const customDateFrom = document.getElementById("custom-date-from");
    const customDateUntil = document.getElementById("custom-date-until");
    const fromLabel = document.getElementById("from-label");
    const untilLabel = document.getElementById("until-label");

    dateSelect.addEventListener("change", function () {
        if (dateSelect.value === "custom") {
            customDateFrom.style.display = "block"; // Show the "Date from" input
            customDateUntil.style.display = "block"; // Show the "Date until" input
            fromLabel.style.display = "block"; // Show the "Date from" label
            untilLabel.style.display = "block"; // Show the "Date until" label
        } else {
            customDateFrom.style.display = "none"; // Hide the "Date from" input
            customDateUntil.style.display = "none"; // Hide the "Date until" input
            fromLabel.style.display = "none"; // Hide the "Date from" label
            untilLabel.style.display = "none"; // Hide the "Date until" label
        }
    });
</script>
{{--

use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
$user = Auth::user();
$admin = $user->role->name;
$branch = Branch::all();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bisikleta Bike Shop - Reports</title>
    <!-- Include Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        /* Add custom CSS styles here */
        body {
            background-color: #f7f7f7;
        }

        .form-container {
            max-width: 600px;
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
            float: right;
        }

        h1 {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }

        .form-button:hover {
            background-color: #0056b3;
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
    <div class="min-h-screen flex items-center justify-center">
        <div class="form-container">
            <img src="{{ asset('storage/dashboardpage/logo.png') }}" alt="Bisikleta" class="logo">
            <h1>Bisileta Bike Shop</h1>
            <form id="dateRangeForm" method="post" action="{{ route('report-pdf') }}">

                @csrf
                @if ($admin === 'Admin')
                <label>Branch</label>
                <select id="branch" required name="branch[]" class="form-control selectpicker">
                    <option disabled selected class="text-gray-500">Choose a Branch</option>
                    @foreach($branch as $branch)
                        <option value="{{ $branch->id }}" class="text-gray-900">{{ $branch->name }}</option>
                    @endforeach
                </select>

                @endif
                <label>Date</label>

                <select id="date-select" name="date" required class="form-input form-control selectpicker">
                    <option disabled selected class="text-gray-500">Choose a Date</option>
                    <option value="today">Today</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                    <option value="custom">Custom Date</option>
                </select>

                <label id="from-label" style="display: none">Date from:</label>
                <input type="hidden" name="date" id="dateRangeInput" required id="custom-date-from"  class="form-input" style="display: none">

                <label id="until-label" style="display: none">Date until:</label>
                <input type="hidden" name="date" id="dateRangeInput" required id="custom-date-until"  class="form-input" style="display: none">
                <button type="submit" class="form-button" style="margin-left: 10px;">Generate PDF</button>
                <button type="button" class="warning-button"  onclick="window.location.href='/admin/applicants'">Cancel</button>

            </form>
        </div>
    </div>
</body>
</html>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script>
  const dateSelect = document.getElementById("date-select");
const dateRangeInput = document.getElementById("dateRangeInput");

dateSelect.addEventListener("change", function () {
    dateRangeInput.value = dateSelect.value;

    if (dateSelect.value === "custom") {
        const customDateFrom = document.getElementById("custom-date-from");
        const customDateUntil = document.getElementById("custom-date-until");

        dateRangeInput.value = `${customDateFrom.value}-${customDateUntil.value}`;
    }

    document.getElementById("dateRangeForm").submit();
});

</script> --}}
