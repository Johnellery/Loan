
<!DOCTYPE html>
<html>
<head>
    <title>Bisikleta Bike Shop - Reports</title>
    <!-- Include Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
<body>
    <div class="min-h-screen flex items-center justify-center">
        <div class="form-container">
            {{-- <form method="get" action="{{ route('report-pdf') }}">
                @csrf
                <label for="date" class="form-label">Select Date:</label>
                <input type="date" id="date" name="date" required class="form-input">
                <button type="submit" class="form-button">Generate PDF</button>
            </form> --}}

            <form method="get" action="{{ route('report-pdf') }}">
                @csrf
                <label>Select Date:</label>
                <select id="date-select" name="date" required class="form-input">
                    <option value="today">Today</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                    <option value="custom">Custom Date</option>
                </select>

                <label id="from-label" style="display: none">Date from:</label>
                <input type="date" id="custom-date-from" name="custom_date_from" class="form-input" style="display: none">

                <label id="until-label" style="display: none">Date until:</label>
                <input type="date" id="custom-date-until" name="custom_date_until" class="form-input" style="display: none">

                <button type="submit" class="form-button">Generate PDF</button>
            </form>


        </div>
    </div>
</body>
</html>
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
