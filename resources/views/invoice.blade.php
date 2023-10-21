<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .invoice {
            border: 1px solid #ccc;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .label {
            display: inline-block;
            width: 150px;
            font-size: 14px;
            font-weight: bold;
        }

        .value {
            display: inline-block;
            font-size: 14px;
            width: calc(100% - 170px); /* Adjust the width as needed for alignment */
        }

        .line {
            margin-bottom: 10px; /* Add some vertical spacing between lines */
        }

        hr {
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        /* Align right for the specific line */
        .line.right-aligned {
            text-align: right;
        }

        /* Center align for label within the line */
        .center-label {
            display: flex;
            left: calc(50% - 40%); /* Move content to the left within the container */
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="invoice">

        <h2>{{ $title }}</h2>
        <hr>
        <div class="line">
            <span class="label">Invoice No.</span>
            <span class="value">{{ $loan->transaction_number }}</span>
        </div>
        <div class= "line">
            <span class="label">Invoice Date</span>
            <span class="value">{{ $loan->applicant->created_at }}</span>
        </div>
        <div class="line">
            <span class="label">Name</span>
            <span class="value">{{ $loan->applicant->customer_name }}</span>
        </div>
        <div class="line">
            <span class="label">Address</span>
            <span class="value">{{ $loan->applicant->unit }}, {{ $loan->applicant->barangay }}, {{ $loan->applicant->city }}, {{ $loan->applicant->province }}</span>
        </div>

        <hr>

        <table>
            <tr>
                <th>Item</th>
                <th>Payment Type</th>
                <th>Amount</th>
            </tr>
            <tr>
                <td>{{ $loan->applicant->bike->name }}</td>
                <td>{{ $loan->payment_type }}</td>
                <td>{{ $loan->amount }}</td>
            </tr>
        </table>
        <hr>

        <div class="line right-aligned">
            <br>
            <span class="value">{{ $loan->cashier }}</span>
            <br>
            <div class="center-label">
                <span class="label">Cashier/Authorized Representative</span>
            </div>
        </div>
    </div>
</body>
</html>

