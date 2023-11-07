<!DOCTYPE html>
<html>
<head>
    <title>Loan Summary</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        .label {
            display: inline-block;
            width: 150px; /* Adjust the width as needed for alignment */
            font-size: 13px;
        }

        .value {
            display: inline-block;
            font-size: 13px;
        }

        .line {
            display: flex;
            align-items: center;
        }

        .line .label,
        .line .value {
            width: auto; /* Adjust the width as needed for alignment */
        }
    </style>

</head>
<body>
    @foreach ($loans as $loan)
    <div>
        <h1>{{ $title }}</h1>
    </div>
    <table class="table table-bordered">
        <tr>
            <th>Name</th>
            <th>OR/JR</th>
            <th>Total Payment</th>
            <th>Date</th>
            {{-- <th>L/R Balance</th> --}}
        </tr>
        @foreach ($loan->billing as $billingRecord)
        <tr>
            <td>{{ $billingRecord->transaction_number }}</td>
            <td>{{ $billingRecord->applicant->first }} {{ $billingRecord->applicant->middle }} {{ $billingRecord->applicant->last }}</td>
            <td>{{ $billingRecord->amount }}</td>
            <td>{{ date('F d, Y', strtotime($billingRecord->created_at)) }}</td>
            {{-- <td>{{ $billingRecord->amountpdf }}</td> --}}
        </tr>
        @endforeach
    </table>
    <div style="page-break-after: always;"></div>
@endforeach
</body>
</html>
