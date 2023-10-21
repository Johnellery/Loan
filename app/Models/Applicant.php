<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use DateInterval;
use DateTime;
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


    private static function calculateCurrentPaymentSchedule(Applicant $record, $decrement = true): string
    {
        $installment = $record->installment;
        $startDate = Carbon::parse($record->start)->startOfDay();
        $endDate = Carbon::parse($record->end)->startOfDay();
        $today = Carbon::now()->startOfDay();
        $currentPaymentDate = $startDate;

        if ($installment === '4') {
            while ($currentPaymentDate->isBefore($today)) {
                $currentPaymentDate->addWeek();
            }
        } elseif ($installment === '1') {
            while ($currentPaymentDate->isBefore($today)) {
                $currentPaymentDate->addMonth();
            }
        }

        // Optionally decrement by one week or one month
        if ($decrement) {
            if ($installment === '4') {
                $currentPaymentDate->subWeek();
            } elseif ($installment === '1') {
                $currentPaymentDate->subMonth();
            }
        }

        return $currentPaymentDate->format('F j, Y');
    }

    private static function calculatePaymentStatus(Applicant $record): string
    {
        $currentDate = Carbon::now();
        $nextPaymentDate = Carbon::parse(self::calculateCurrentPaymentSchedule($record));

        $billingRecords = $record->billing->where('billing_status', 'remitted');

        $paidOnScheduledDate = $billingRecords
            ->where('created_at', '>=', $nextPaymentDate->startOfDay())
            ->where('created_at', '<', $nextPaymentDate->endOfDay())
            ->isNotEmpty();
        $paidWithinMonth = $billingRecords
            ->where('created_at', '>=', $currentDate->startOfMonth())
            ->isNotEmpty();

       if ($paidOnScheduledDate) {
            return "Paid";
        } elseif ($paidWithinMonth) {
            return "Paid";
        } elseif ($currentDate->lessThanOrEqualTo($nextPaymentDate)) {
            return "Missed";
        } else {
            return "Pending";
        }
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

        // Serialize the payment schedule array to JSON
        $paymentScheduleJson = json_encode($paymentSchedule);

        // Update the 'repossession' field in the database with the JSON data
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
