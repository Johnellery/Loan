<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\DB;
class Applicant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
    'user_id',
    'branch_id',
    'picture',
    'valid_id_list',
    'valid_id',
    'barangay_clearance',
    'first',
    'middle',
    'last',
    'email',
    'age',
    'gender',
    'civil',
    'religion',
    'occupation',
    'spouse',
    'contact_spouse',
    'occupation_spouse',
    'unit',
    'barangay',
    'city',
    'province',
    'bike_id',
    'term',
    'installment',
    'down_payment',
    'start',
    'end',
    'status',
    'ci_status',
    'total_interest',
    'plus',
    'payment',
    'remaining_balance',
    'remaining_weeks',
    'customer_name',
    'bike_price',
    'payment_description',
    'is_paid',
    'is_computed',
    'repossession',
    'ci_sched',
    'week11',
    'week12',
    'week13',
    'week14',
    'week21',
    'week22',
    'week23',
    'week24',
    'week31',
    'week32',
    'week33',
    'week34',
    'week41',
    'week42',
    'week43',
    'week44',
    'week51',
    'week52',
    'week53',
    'week54',
    'week61',
    'week62',
    'week63',
    'week64',
    'month1',
    'month2',
    'month3',
    'month4',
    'month5',
    'month6',
    'is_status',
    'payment_schedule',
    'payment_schedule_slug',
    'payment_date',
];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function bike()
    {
        return $this->belongsTo(Bike::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function billing(): HasMany
    {
        return $this->HasMany(Billing::class, 'applicant_id');
    }
    public function isApproved()
    {
        return $this->status === 'approved';
    }
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
    public function isCIApproved()
    {
        return $this->ci_status === 'approved';
    }
    public function isCIRejected()
    {
        return $this->ci_status === 'rejected' || $this->ci_status === 'reject';
    }
    public function isSuccess()
    {
        return $this->is_status === 'success';
    }
    public function isFailed()
    {
        return $this->is_status === 'failed';
    }
    public function isRepossessing()
    {
        return $this->is_status === 'repossessing';
    }
    /// FUNCTIONALITY
    private static function calculatePaymentDescription(Applicant $record): string
{
    $installment = $record->installment;
    $startDate = Carbon::parse($record->start)->startOfDay();
    $today = Carbon::now()->startOfDay();
    $nextPaymentDate = $startDate;

    if ($installment === '4') {
        while ($nextPaymentDate->lessThan($today)) {
            $nextPaymentDate->addWeek();
        }
    } elseif ($installment === '1') {
        while ($nextPaymentDate->lessThan($today)) {
            $nextPaymentDate->addMonth();
        }
    }

    if ($nextPaymentDate->isSameDay($today)) {
        return "Today";
    } elseif ($nextPaymentDate->isSameDay($today->copy()->addDay())) {
        return "Tomorrow"; // Change this line to "Tomorrow" instead of "Today"
    } else {
        $remainingDays = $today->diffInDays($nextPaymentDate, false);
        return "Due in " . $remainingDays . " days";
    }
}


private static function calculatePaymentSchedule(Applicant $record): string
    {
        $installment = $record->installment;
        $startDate = Carbon::parse($record->start)->startOfDay();
        $endDate = Carbon::parse($record->end)->startOfDay();
        $today = Carbon::now()->startOfDay();
        $nextPaymentDate = $startDate;

        if ($installment === '4') {
            // Calculate the next payment date as a week from the start date
            $nextPaymentDate = $startDate->copy()->addWeek();
        } elseif ($installment === '1') {
            // Calculate the next payment date as a month from the start date
            $nextPaymentDate = $startDate->copy()->addMonth();
        }

        // Check if the calculated nextPaymentDate is before today and before the end date
        while ($nextPaymentDate->isBefore($today) && $nextPaymentDate->isBefore($endDate)) {
            if ($installment === '4') {
                $nextPaymentDate->addWeek();
            } elseif ($installment === '1') {
                $nextPaymentDate->addMonth();
            }
        }

        // Check if nextPaymentDate has exceeded the end date
        if ($nextPaymentDate->isAfter($endDate)) {
            return $endDate->format('F j, Y'); // Return the end date
        }

        return $nextPaymentDate->format('F j, Y');
    }
    private static function calculatePaymentSchedule1(Applicant $record): string
    {
        $installment = $record->installment;
        $startDate = Carbon::parse($record->start)->startOfDay();
        $endDate = Carbon::parse($record->end)->startOfDay();
        $today = Carbon::now()->startOfDay();
        $nextPaymentDate = $startDate;

        if ($installment === '4') {
            // Calculate the next payment date as a week from the start date
            $nextPaymentDate = $startDate->copy()->addWeek();
        } elseif ($installment === '1') {
            // Calculate the next payment date as a month from the start date
            $nextPaymentDate = $startDate->copy()->addMonth();
        }

        // Check if the calculated nextPaymentDate is before today and before the end date
        while ($nextPaymentDate->isBefore($today) && $nextPaymentDate->isBefore($endDate)) {
            if ($installment === '4') {
                $nextPaymentDate->addWeek();
            } elseif ($installment === '1') {
                $nextPaymentDate->addMonth();
            }
        }

        // Check if nextPaymentDate has exceeded the end date
        if ($nextPaymentDate->isAfter($endDate)) {
            return $endDate; // Return the end date
        }

        return $nextPaymentDate;
    }
private static function calculatePaymentStatus(Applicant $record): string
{
    $currentDate = Carbon::now();
    $nextPaymentDate = Carbon::parse(self::calculatePaymentSchedule($record))->startOfDay(); // Make sure it starts at the beginning of the day
    $billingRecords = $record->billing->where('billing_status', 'remitted');

    $paidOnScheduledDate = $billingRecords
    ->where('created_at', '<', $nextPaymentDate)
    ->isNotEmpty();


    if ($paidOnScheduledDate) {
        return "Paid";
    } elseif ($currentDate->equalTo($nextPaymentDate)) {
        return "Pending";
    } elseif ($currentDate->greaterThan($nextPaymentDate)) {
        return "Missed";
    } else {
        return "Pending";
    }
}
private static function calculateDate(Applicant $record): string
{
    $currentDate = Carbon::now();
    $nextPaymentDate = Carbon::parse(self::calculatePaymentSchedule($record))->startOfDay(); // Make sure it starts at the beginning of the day
    $billingRecords = $record->billing->where('billing_status', 'remitted');

    $created = $billingRecords->created_at;
    return $created;
}



    ///FUNCTIONALITY


    ///SAVE DATABASE
    public function updateRemainingBalance()
    {
        $totalLoanAmount = $this->remaining_balance;
        $unprocessedPayments = $this->billing()
            ->where('billing_status', 'remitted')
            ->where('is_processed', false)
            ->get();

        $totalPayment = $unprocessedPayments->sum('amount');
        $interest = $unprocessedPayments->sum('interests');
        $unprocessedPayments->each(function ($payment) {
            $payment->update(['is_processed' => true]);
        });
        $totalPayments = $totalPayment + $interest;
        $remainingBalance = $totalLoanAmount - $totalPayments;
        $this->update(['remaining_balance' => $remainingBalance]);
        return $remainingBalance;
    }
    public function updateStatus()
    {
        $ispaid = $this->calculatePaymentStatus($this);
        $this->update([
            'is_paid' => $ispaid,
        ]);
        return $ispaid;
    }
    public function updateSched()
    {
        $ispaid1 = $this->calculatePaymentSchedule($this);
        $ispaid = $this->calculatePaymentSchedule1($this);
        $this->update([
            'payment_schedule_slug' => $ispaid1,
            'payment_schedule' => $ispaid,
        ]);
        return $ispaid1;
    }
    public function updateDescription()
    {
        $billingRecords = $this->billing->where('billing_status', 'remitted');

        $startDate = new DateTime($this->start);
        $endDate = new DateTime($this->end);
        $installmentFrequency = $this->installment;
        $currentDate = Carbon::now();
        $paymentSchedule = [];

        while ($startDate <= $endDate) {
            $paymentDate = $startDate->format('F j, Y');

            $status = 'Pending';

            if ($currentDate->greaterThanOrEqualTo($startDate)) {
                $status = 'Missed';

                // Check if a payment was made between the current payment date and the next one
                $nextPaymentDate = clone $startDate;
                if ($installmentFrequency == 4) {
                    $nextPaymentDate->add(new DateInterval('P1W'));
                } elseif ($installmentFrequency == 1) {
                    $nextPaymentDate->add(new DateInterval('P1M'));
                }

                foreach ($billingRecords as $billingRecord) {
                    $recordDate = Carbon::parse($billingRecord->created_at)->format('F j, Y');
                    if ($recordDate >= $nextPaymentDate->format('F j, Y')) {
                        $status = 'Paid';
                        break;
                    }
                }
            }

            $paymentSchedule[] = [
                'date' => $paymentDate,
                'status' => $status,
            ];

            if ($installmentFrequency == 4) {
                $startDate->add(new DateInterval('P1W'));
            } elseif ($installmentFrequency == 1) {
                $startDate->add(new DateInterval('P1M'));
            }
        }

        $paymentScheduleJson = json_encode($paymentSchedule);

        $this->update([
            'repossession' => $paymentScheduleJson,
        ]);

        $description = $this->calculatePaymentDescription($this);
        $this->update([
            'payment_description' => $description,
        ]);
        return $description;
    }



    public function updateCompute()
    {

        $bike = $this->bike;
        $interest_rate = $bike->rate;
        $principal = $bike->price;
        $term = $this->term;
        $perweek = $this->installment;
        $bike_price = $this->bike->price;
        $down = $bike->down;
        $decimal_rate = $interest_rate / 100;
        $computed_interest = $principal * $decimal_rate;
        $complete = $principal + $computed_interest;

        $interest = $principal * $decimal_rate;
        $plus = $principal + $computed_interest;

        $afterdownpayment = $plus - $bike->down;
        $payment = ($afterdownpayment / $term) / $perweek;

        $full_name = $this->middle . ', ' . $this->first . ' ' . $this->last;


        $this->update([
            'total_interest' => $interest,
            'plus' => $plus,
            'payment' => $payment,
            'customer_name' => $full_name,
            'bike_price' => $bike_price,
            'down_payment' => $down,
        ]);
        return $full_name;
    }
    // public function updateremaining()
    // {
    //     $totalLoanAmount = $this->plus;
    //     $unprocessedPayments = Applicant::where('is_computed', false)
    //         ->get();

    //     $totalPayments = $unprocessedPayments->sum('down_payment');
    //     $unprocessedPayments->each(function ($payment) {
    //         $payment->update(['is_computed' => true]);
    //     });
    //     $remainingBalance = $totalLoanAmount - $totalPayments;
    //     $this->update(['remaining_balance' => $remainingBalance]);
    //     return $remainingBalance;
    // }
    // public function updateDate()
    // {
    //     $billingRecords = $this->billing->where('billing_status', 'remitted');

    //     $startDate = new DateTime($this->start);
    //     $endDate = new DateTime($this->end);
    //     $installmentFrequency = $this->installment;
    //     $currentDate = Carbon::now();
    //     $paymentSchedule = [];

    //     while ($startDate <= $endDate) {
    //         $paymentDate = $startDate->format('F j, Y');

    //         $status = 'Pending';

    //         if ($currentDate->greaterThanOrEqualTo($startDate)) {
    //             $status = 'Missed';

    //             // Check if a payment was made between the current payment date and the next one
    //             $nextPaymentDate = clone $startDate;
    //             if ($installmentFrequency == 4) {
    //                 $nextPaymentDate->add(new DateInterval('P1W'));
    //             } elseif ($installmentFrequency == 1) {
    //                 $nextPaymentDate->add(new DateInterval('P1M'));
    //             }

    //             foreach ($billingRecords as $billingRecord) {
    //                 $recordDate = Carbon::parse($billingRecord->created_at)->format('F j, Y');
    //                 if ($recordDate >= $paymentDate && $recordDate < $nextPaymentDate->format('F j, Y')) {
    //                     $status = 'Paid';
    //                     break;
    //                 }
    //             }
    //         }

    //         $paymentSchedule[] = [
    //             'date' => $paymentDate,
    //             'status' => $status,
    //         ];

    //         if ($installmentFrequency == 4) {
    //             $startDate->add(new DateInterval('P1W'));
    //         } elseif ($installmentFrequency == 1) {
    //             $startDate->add(new DateInterval('P1M'));
    //         }
    //     }

    //     // Serialize the payment schedule array to JSON
    //     $paymentScheduleJson = json_encode($paymentSchedule);

    //     // Update the 'repossession' field in the database with the JSON data
    //     $this->update([
    //         'repossession' => $paymentScheduleJson,
    //     ]);

    //     return $status; // Return the JSON string
    // }

    ///SAVE DATABASE
}
