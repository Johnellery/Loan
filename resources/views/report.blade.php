<!DOCTYPE html>
<html>
<head>
    <title>Loan Summary</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        .value {
            font-size: 12px;
        }

        table th {
            font-size: 15px;
        }

        .term {
        font-size: 10px;
        color: gray;
    }
    .page-break {
        page-break-before: always;
    }
    </style>
</head>
<body>
    <div>
        <h1>{{ $title }}</h1>
    </div>
    @if (count($billing) > 0)
    <div>
        <h1>Payment</h1>
    </div>
    <table class="table table-bordered">
        <tr>
            <th>OR/JR</th>
            <th>Name</th>
            <th>Total Payment</th>
            <th>Date</th>
        </tr>
        @foreach ($billing as $billingItem)
        <tr>
            <td class="value">{{ $billingItem->applicant->first }} {{ $billingItem->applicant->middle }} {{$billingItem->applicant->last }}</td>
            <td class="value">{{ $billingItem->transaction_number }}</td>
            <td class="value">{{ $billingItem->amount }}</td>
            <td class="value">{{ date('F d, Y', strtotime($billingItem->created_at)) }}</td>
        </tr>
    @endforeach
    </table>
    @else
    @endif

@if (count($loan) > 0)
    <div class="page-break">
        <h1>Bike Released</h1>
    </div>
    <table class="table table-bordered" >
        <tr>
            <th>Name</th>
            <th>Bike name</th>
            <th>Bike price</th>
            <th>Downpayment</th>
            <th>Term <span class="term">(Months)</span></th>
            <th>Installment</th>
            <th>Date</th>
        </tr>
        @foreach ($loan as $loan)
        <tr>
            <td class="value">{{ $loan->first }} {{ $loan->middle }} {{ $loan->last }}</td>
            <td class="value">{{ $loan->bike->name }}</td>
            <td class="value">{{ $loan->bike->price }}</td>
            <td class="value">{{ $loan->bike->down }}</td>
            <td class="value">
                {{ $loan->term }}
            </td>
            <td class="value">
                @if ($loan->installment == 4)
                    Weekly
                @elseif ($loan->installment == 1)
                    Monthly
                @else
                    Error
                @endif
            </td>
            <td class="value">{{ date('F d, Y', strtotime($loan->created_at)) }}</td>
        </tr>
        @endforeach
    </table>
@else

@endif
@if (count($repossession) > 0)
    <div class="page-break">
        <h1>Repossess Bike</h1>
    </div>
    <table class="table table-bordered" >
        <tr>
            <th>Name</th>
            <th>Bike name</th>
            <th>Bike price</th>
            <th>Downpayment</th>
            <th>Term <span class="term">(Months)</span></th>
            <th>Installment</th>
            <th>Remaining Balance</th>
            <th>Date</th>
        </tr>
        @foreach ($repossession as $repossession)
        <tr>
            <td class="value">{{ $repossession->first }} {{ $repossession->middle }} {{ $repossession->last }}</td>
            <td class="value">{{ $repossession->bike->name }}</td>
            <td class="value">{{ $repossession->bike->price }}</td>
            <td class="value">{{ $repossession->bike->down }}</td>
            <td class="value">
                {{ $repossession->term }}
            </td>
            <td class="value">
                @if ($repossession->installment == 4)
                    Weekly
                @elseif ($repossession->installment == 1)
                    Monthly
                @else
                    Error
                @endif
            </td>
            <td class="value">{{ $repossession->remaining_balance }}</td>
            <td class="value">{{ date('F d, Y', strtotime($repossession->repossession_date)) }}</td>
        </tr>
        @endforeach
    </table>
@else

@endif
</body>
</html>
