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
    <div>
        <h1>{{ $title }}</h1>
    </div>

    <table>
        <tr>
            <td class="label">Name:</td>
            <td class="value">{{ $loan->customer_name }}</td>
        </tr>
        <tr>
            <td class="label">Address:</td>
            <td class="value">{{ $loan->unit }}, {{ $loan->barangay }}, {{ $loan->city }}, {{ $loan->province }}</td>
        </tr>
        <tr>
            <td class="label">Tel. no.:</td>
            <td class="value">{{ $loan->user->phone }}</td>
        </tr>
        <tr>
            <td class="label">Term:</td>
            <td class="value">{{ $loan->term }} Months</td>
        </tr>
        <tr>
            <td class="label">Total Amount:</td>
            <td class="value">{{ $loan->plus }}</td>
        </tr>
        <tr>
            <td class="label">Loan Total Contract:</td>
            <td class="value">{{ $loan->bike_price }}</td>
        </tr>
        <tr>
            <td class="label">Date Released:</td>
            <td class="value">{{ $loan->start }}</td>
        </tr>
        <tr>
            <td class="label">Due Date:</td>
            <td class="value">{{ $loan->end }}</td>
        </tr>
        <tr>
            <td class="label">Amortization:</td>
            <td class="value">{{ number_format($loan->payment, 2)  }}</td>
        </tr>
        <tr>
            <td class="label">Payment Mode:</td>
            <td class="value">
                @if ($loan->installment == 4)
                    Weekly
                @elseif ($loan->installment == 1)
                    Monthly
                @else
                    Error
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Due as of {{ $date }}:</td>
            <td class="value">{{ number_format($loan->remaining_balance, 2) }}</td>
        </tr>
    </table>

    <table class="table table-bordered">
        <tr>
            <th>Date</th>
            <th>OR/JR</th>
            <th>Total Payment</th>
            {{-- <th>L/R Balance</th> --}}
        </tr>
        @foreach ($loan->billing as $billingRecord)
        <tr>
            <td>{{ date('F d, Y', strtotime($billingRecord->created_at)) }}</td>
            <td>{{ $billingRecord->transaction_number }}</td>
            <td>{{ $billingRecord->amount }}</td>
            {{-- <td>{{ $billingRecord->amountpdf }}</td> --}}
        </tr>
        @endforeach
    </table>
</body>
</html>
