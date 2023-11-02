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
    'progress',
    'ci_date',
    'status_date',
    'remark',
    'ci_remark',
    'paid1',
    'paid2',
    'paid3',
    'paid4',
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


    public function updatepaid()
    {
                    //
                    $record = $this;
                    $term = $record->term;
                    $perweek = $record->installment;
                    $billingRecords = $record->billing->where('billing_status', 'remitted');
                    $currentDate = Carbon::now();
                    if($term === '4' && $perweek ==='1'){
                        $month1 = new DateTime($record->month1);
                        $month2 = new DateTime($record->month2);
                        $month3 = new DateTime($record->month3);
                        $month4 = new DateTime($record->month4);
                        $month5 = new DateTime($record->month5);
                        $formattedMonth1 = $month1->format('F j, Y');
                        $formattedMonth2 = $month2->format('F j, Y');
                        $formattedMonth3 = $month3->format('F j, Y');
                        $formattedMonth4 = $month4->format('F j, Y');

                        $paid1 = "";
                        $paid2 = "";
                        $paid3 = "";
                        $paid4 = "";
                        //
                        $paidOnScheduledDate = $billingRecords
                            ->where('created_at', '>=', $month1)
                            ->where('created_at', '<', $month2)
                            ->isNotEmpty();
                        if ($paidOnScheduledDate) {
                            $paid1 = "Paid";
                        } elseif ($currentDate > $month1) {
                            $paid1 = "Missed";
                        } else {
                            $paid1 = "Pending";
                        }
                        //
                        $paidOnScheduledDate2 = $billingRecords
                        ->where('created_at', '>=', $month2)
                        ->where('created_at', '<', $month3)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate2) {
                            $paid2 = "Paid";
                        } elseif ($currentDate > $month2) {
                            $paid2 = "Missed";
                        } else {
                            $paid2 = "Pending";
                        }
                        //
                        $paidOnScheduledDate3 = $billingRecords
                        ->where('created_at', '>=', $month3)
                        ->where('created_at', '<', $month4)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate3) {
                            $paid3 = "Paid";
                        } elseif ($currentDate > $month3) {
                            $paid3 = "Missed";
                        } else {
                            $paid3 = "Pending";
                        }
                        //
                        $paidOnScheduledDate4 = $billingRecords
                        ->where('created_at', '>=', $month4)
                        ->where('created_at', '<', $month5)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate4) {
                            $paid4 = "Paid";
                        } elseif ($currentDate > $month4) {
                            $paid4 = "Missed";
                        } else {
                            $paid4 = "Pending";
                        }
                        $result = $formattedMonth1 . '---' . $paid1 . "\n_________________________________________________________\n" .
                                  $formattedMonth2 . '---' . $paid2 . "\n_________________________________________________________\n" .
                                  $formattedMonth3 . '---' . $paid3 ."\n_________________________________________________________\n" .
                                  $formattedMonth4 . '---'. $paid4;
                                //   $record->update([
                                //     'paid1' => $paid1,
                                //   ]);
                        return $result;

                    } else if ($term === '4' && $perweek ==='4'){
                        $week11DateTime = new DateTime($record->week11);
                        $week12DateTime = new DateTime($record->week12);
                        $week13DateTime = new DateTime($record->week13);
                        $week14DateTime = new DateTime($record->week14);
                        $week21DateTime = new DateTime($record->week21);
                        $week22DateTime = new DateTime($record->week22);
                        $week23DateTime = new DateTime($record->week23);
                        $week24DateTime = new DateTime($record->week24);
                        $week31DateTime = new DateTime($record->week31);
                        $week32DateTime = new DateTime($record->week32);
                        $week33DateTime = new DateTime($record->week33);
                        $week34DateTime = new DateTime($record->week34);
                        $week41DateTime = new DateTime($record->week41);
                        $week42DateTime = new DateTime($record->week42);
                        $week43DateTime = new DateTime($record->week43);
                        $week44DateTime = new DateTime($record->week44);
                        $week51DateTime = new DateTime($record->week51);


                        $week11Formatted = $week11DateTime->format('F j, Y');
                        $week12Formatted = $week12DateTime->format('F j, Y');
                        $week13Formatted = $week13DateTime->format('F j, Y');
                        $week14Formatted = $week14DateTime->format('F j, Y');
                        $week21Formatted = $week21DateTime->format('F j, Y');
                        $week22Formatted = $week22DateTime->format('F j, Y');
                        $week23Formatted = $week23DateTime->format('F j, Y');
                        $week24Formatted = $week24DateTime->format('F j, Y');
                        $week31Formatted = $week31DateTime->format('F j, Y');
                        $week32Formatted = $week32DateTime->format('F j, Y');
                        $week33Formatted = $week33DateTime->format('F j, Y');
                        $week34Formatted = $week34DateTime->format('F j, Y');
                        $week41Formatted = $week41DateTime->format('F j, Y');
                        $week42Formatted = $week42DateTime->format('F j, Y');
                        $week43Formatted = $week43DateTime->format('F j, Y');
                        $week44Formatted = $week44DateTime->format('F j, Y');

                        $paid11 = "";
                        $paid12 = "";
                        $paid13 = "";
                        $paid14 = "";
                        $paid21 = "";
                        $paid22 = "";
                        $paid23 = "";
                        $paid24 = "";
                        $paid31 = "";
                        $paid32 = "";
                        $paid33 = "";
                        $paid34 = "";
                        $paid41 = "";
                        $paid42 = "";
                        $paid43 = "";
                        $paid44 = "";

                        //
                    $paidOnScheduledDate11 = $billingRecords
                        ->where('created_at', '>=', $week11DateTime)
                        ->where('created_at', '<', $week12DateTime)
                        ->isNotEmpty();
                    if ($paidOnScheduledDate11) {
                        $paid11 = "Paid";
                    } else if ($currentDate > $week11DateTime) {
                        $paid11 = "Missed";
                    } else {
                        $paid11 = "Pending";
                    }
                    //
                    $paidOnScheduledDate12 = $billingRecords
                    ->where('created_at', '>=', $week12DateTime)
                    ->where('created_at', '<', $week13DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate12) {
                        $paid12 = "Paid";
                    } else if ($currentDate > $week12DateTime) {
                        $paid12 = "Missed";
                    } else {
                        $paid12 = "Pending";
                    }
                    //
                    $paidOnScheduledDate13 = $billingRecords
                    ->where('created_at', '>=', $week13DateTime)
                    ->where('created_at', '<', $week14DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate13) {
                        $paid13 = "Paid";
                    } else if ($currentDate > $week13DateTime) {
                        $paid13 = "Missed";
                    } else {
                        $paid13 = "Pending";
                    }
                    //
                    $paidOnScheduledDate14 = $billingRecords
                    ->where('created_at', '>=', $week14DateTime)
                    ->where('created_at', '<', $week21DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate14) {
                        $paid14 = "Paid";
                    } elseif ($currentDate > $week14DateTime) {
                        $paid14 = "Missed";
                    } else {
                        $paid14 = "Pending";
                    }
                    //
                    $paidOnScheduledDate21 = $billingRecords
                    ->where('created_at', '>=', $week21DateTime)
                    ->where('created_at', '<', $week22DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate21) {
                        $paid21 = "Paid";
                    } elseif ($currentDate > $week21DateTime) {
                        $paid21 = "Missed";
                    } else {
                        $paid21 = "Pending";
                    }
                    //
                    $paidOnScheduledDate22 = $billingRecords
                    ->where('created_at', '>=', $week22DateTime)
                    ->where('created_at', '<', $week23DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate22) {
                        $paid22 = "Paid";
                    } elseif ($currentDate > $week22DateTime) {
                        $paid22 = "Missed";
                    } else {
                        $paid22 = "Pending";
                    }
                    //
                    $paidOnScheduledDate23 = $billingRecords
                    ->where('created_at', '>=', $week23DateTime)
                    ->where('created_at', '<', $week24DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate23) {
                        $paid23 = "Paid";
                    } elseif ($currentDate > $week23DateTime) {
                        $paid23 = "Missed";
                    } else {
                        $paid23 = "Pending";
                    }
                    //
                    $paidOnScheduledDate24 = $billingRecords
                    ->where('created_at', '>=', $week24DateTime)
                    ->where('created_at', '<', $week31DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate24) {
                        $paid24 = "Paid";
                    } elseif ($currentDate > $week24DateTime) {
                        $paid24 = "Missed";
                    } else {
                        $paid24 = "Pending";
                    }
                    //
                    $paidOnScheduledDate31 = $billingRecords
                    ->where('created_at', '>=', $week31DateTime)
                    ->where('created_at', '<', $week32DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate31) {
                        $paid31 = "Paid";
                    } elseif ($currentDate > $week31DateTime) {
                        $paid31 = "Missed";
                    } else {
                        $paid31 = "Pending";
                    }
                    //
                    $paidOnScheduledDate32 = $billingRecords
                    ->where('created_at', '>=', $week32DateTime)
                    ->where('created_at', '<', $week33DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate32) {
                        $paid32 = "Paid";
                    } elseif ($currentDate > $week32DateTime) {
                        $paid32 = "Missed";
                    } else {
                        $paid32 = "Pending";
                    }
                    //
                    $paidOnScheduledDate33 = $billingRecords
                    ->where('created_at', '>=', $week33DateTime)
                    ->where('created_at', '<', $week34DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate33) {
                        $paid33 = "Paid";
                    } elseif ($currentDate > $week33DateTime) {
                        $paid33 = "Missed";
                    } else {
                        $paid33 = "Pending";
                    }
                    //
                    $paidOnScheduledDate34 = $billingRecords
                    ->where('created_at', '>=', $week34DateTime)
                    ->where('created_at', '<', $week41DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate34) {
                        $paid34 = "Paid";
                    } elseif ($currentDate > $week34DateTime) {
                        $paid34 = "Missed";
                    } else {
                        $paid34 = "Pending";
                    }
                    //




                    $paidOnScheduledDate41 = $billingRecords
                    ->where('created_at', '>=', $week41DateTime)
                    ->where('created_at', '<', $week42DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate41) {
                        $paid41 = "Paid";
                    } elseif ($currentDate > $week41DateTime) {
                        $paid41 = "Missed";
                    } else {
                        $paid41 = "Pending";
                    }
                    //
                    $paidOnScheduledDate42 = $billingRecords
                    ->where('created_at', '>=', $week42DateTime)
                    ->where('created_at', '<', $week43DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate42) {
                        $paid42 = "Paid";
                    } elseif ($currentDate > $week42DateTime) {
                        $paid42 = "Missed";
                    } else {
                        $paid42 = "Pending";
                    }
                    //
                    $paidOnScheduledDate43 = $billingRecords
                    ->where('created_at', '>=', $week43DateTime)
                    ->where('created_at', '<', $week44DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate43) {
                        $paid43 = "Paid";
                    } elseif ($currentDate > $week43DateTime) {
                        $paid43 = "Missed";
                    } else {
                        $paid43 = "Pending";
                    }
                    //
                    $paidOnScheduledDate44 = $billingRecords
                    ->where('created_at', '>=', $week44DateTime)
                    ->where('created_at', '<', $week51DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate44) {
                        $paid44 = "Paid";
                    } elseif ($currentDate > $week44DateTime) {
                        $paid44 = "Missed";
                    } else {
                        $paid44 = "Pending";
                    }
                    //
                        return $week11Formatted . '---' . $paid11 ."\n_________________________________________________________\n" .
                                $week12Formatted . '---' . $paid12 ."\n_________________________________________________________\n" .
                                $week13Formatted . '---' . $paid13 ."\n_________________________________________________________\n" .
                                $week14Formatted . '---' . $paid14 ."\n_________________________________________________________\n" .
                                $week21Formatted . '---' . $paid21 ."\n_________________________________________________________\n" .
                                $week22Formatted . '---' . $paid22 ."\n_________________________________________________________\n" .
                                $week23Formatted . '---' . $paid23 ."\n_________________________________________________________\n" .
                                $week24Formatted . '---' . $paid24 ."\n_________________________________________________________\n" .
                                $week31Formatted . '---' . $paid31 ."\n_________________________________________________________\n" .
                                $week32Formatted . '---' . $paid32 ."\n_________________________________________________________\n" .
                                $week33Formatted . '---' . $paid33 ."\n_________________________________________________________\n" .
                                $week34Formatted . '---' . $paid34 ."\n_________________________________________________________\n" .
                                $week41Formatted . '---' . $paid41 ."\n_________________________________________________________\n" .
                                $week42Formatted . '---' . $paid42 ."\n_________________________________________________________\n" .
                                $week43Formatted . '---' . $paid43 ."\n_________________________________________________________\n" .
                                $week44Formatted . '---' . $paid44 ;

                    }  else if($term === '5' && $perweek ==='1'){
                        $billingRecords = $record->billing->where('billing_status', 'remitted');
                        $currentDate = Carbon::now();
                        $month1 = new DateTime($record->month1);
                        $month2 = new DateTime($record->month2);
                        $month3 = new DateTime($record->month3);
                        $month4 = new DateTime($record->month4);
                        $month5 = new DateTime($record->month5);
                        $month6 = new DateTime($record->month6);
                        $formattedMonth1 = $month1->format('F j, Y');
                        $formattedMonth2 = $month2->format('F j, Y');
                        $formattedMonth3 = $month3->format('F j, Y');
                        $formattedMonth4 = $month4->format('F j, Y');
                        $formattedMonth5 = $month5->format('F j, Y');
                        $formattedMonth6 = $month6->format('F j, Y');

                        $paid1 = "";
                        $paid2 = "";
                        $paid3 = "";
                        $paid4 = "";
                        $paid5 = "";
                        $paid6 = "";
                        //
                        $paidOnScheduledDate = $billingRecords
                            ->where('created_at', '>=', $month1)
                            ->where('created_at', '<', $month2)
                            ->isNotEmpty();
                        if ($paidOnScheduledDate) {
                            $paid1 = "Paid";
                        } elseif ($currentDate > $month1) {
                            $paid1 = "Missed";
                        } else {
                            $paid1 = "Pending";
                        }
                        //
                        $paidOnScheduledDate2 = $billingRecords
                        ->where('created_at', '>=', $month2)
                        ->where('created_at', '<', $month3)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate2) {
                            $paid2 = "Paid";
                        } elseif ($currentDate > $month2) {
                            $paid2 = "Missed";
                        } else {
                            $paid2 = "Pending";
                        }
                        //
                        $paidOnScheduledDate3 = $billingRecords
                        ->where('created_at', '>=', $month3)
                        ->where('created_at', '<', $month4)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate3) {
                            $paid3 = "Paid";
                        } elseif ($currentDate > $month3) {
                            $paid3 = "Missed";
                        } else {
                            $paid3 = "Pending";
                        }
                        //
                        $paidOnScheduledDate4 = $billingRecords
                        ->where('created_at', '>=', $month4)
                        ->where('created_at', '<', $month5)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate4) {
                            $paid4 = "Paid";
                        } elseif ($currentDate > $month4) {
                            $paid4 = "Missed";
                        } else {
                            $paid4 = "Pending";
                        }
                        //


                        $paidOnScheduledDate5 = $billingRecords
                        ->where('created_at', '>=', $month5)
                        ->where('created_at', '<', $month6)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate5) {
                            $paid5 = "Paid";
                        } elseif ($currentDate > $month5) {
                            $paid5 = "Missed";
                        } else {
                            $paid5 = "Pending";
                        }
                        $result = $formattedMonth1 . '---' . $paid1 . "\n_________________________________________________________\n" .
                                  $formattedMonth2 . '---' . $paid2 . "\n_________________________________________________________\n" .
                                  $formattedMonth3 . '---' . $paid3 ."\n_________________________________________________________\n" .
                                  $formattedMonth4 . '---'. $paid4 ."\n_________________________________________________________\n" .
                                  $formattedMonth5 . '---'. $paid5;


                        return $result;

                    } else if ($term === '5' && $perweek ==='4'){
                        $week11DateTime = new DateTime($record->week11);
                        $week12DateTime = new DateTime($record->week12);
                        $week13DateTime = new DateTime($record->week13);
                        $week14DateTime = new DateTime($record->week14);
                        $week21DateTime = new DateTime($record->week21);
                        $week22DateTime = new DateTime($record->week22);
                        $week23DateTime = new DateTime($record->week23);
                        $week24DateTime = new DateTime($record->week24);
                        $week31DateTime = new DateTime($record->week31);
                        $week32DateTime = new DateTime($record->week32);
                        $week33DateTime = new DateTime($record->week33);
                        $week34DateTime = new DateTime($record->week34);
                        $week41DateTime = new DateTime($record->week41);
                        $week42DateTime = new DateTime($record->week42);
                        $week43DateTime = new DateTime($record->week43);
                        $week44DateTime = new DateTime($record->week44);
                        $week51DateTime = new DateTime($record->week51);
                        $week52DateTime = new DateTime($record->week52);
                        $week53DateTime = new DateTime($record->week53);
                        $week54DateTime = new DateTime($record->week54);
                        $week61DateTime = new DateTime($record->week61);


                        $week11Formatted = $week11DateTime->format('F j, Y');
                        $week12Formatted = $week12DateTime->format('F j, Y');
                        $week13Formatted = $week13DateTime->format('F j, Y');
                        $week14Formatted = $week14DateTime->format('F j, Y');
                        $week21Formatted = $week21DateTime->format('F j, Y');
                        $week22Formatted = $week22DateTime->format('F j, Y');
                        $week23Formatted = $week23DateTime->format('F j, Y');
                        $week24Formatted = $week24DateTime->format('F j, Y');
                        $week31Formatted = $week31DateTime->format('F j, Y');
                        $week32Formatted = $week32DateTime->format('F j, Y');
                        $week33Formatted = $week33DateTime->format('F j, Y');
                        $week34Formatted = $week34DateTime->format('F j, Y');
                        $week41Formatted = $week41DateTime->format('F j, Y');
                        $week42Formatted = $week42DateTime->format('F j, Y');
                        $week43Formatted = $week43DateTime->format('F j, Y');
                        $week44Formatted = $week44DateTime->format('F j, Y');
                        $week51Formatted = $week51DateTime->format('F j, Y');
                        $week52Formatted = $week52DateTime->format('F j, Y');
                        $week53Formatted = $week53DateTime->format('F j, Y');
                        $week54Formatted = $week54DateTime->format('F j, Y');

                        $paid11 = "";
                        $paid12 = "";
                        $paid13 = "";
                        $paid14 = "";
                        $paid21 = "";
                        $paid22 = "";
                        $paid23 = "";
                        $paid24 = "";
                        $paid31 = "";
                        $paid32 = "";
                        $paid33 = "";
                        $paid34 = "";
                        $paid41 = "";
                        $paid42 = "";
                        $paid43 = "";
                        $paid44 = "";
                        $paid51 = "";
                        $paid52 = "";
                        $paid53 = "";
                        $paid54 = "";

                        //
                    $paidOnScheduledDate11 = $billingRecords
                        ->where('created_at', '>=', $week11DateTime)
                        ->where('created_at', '<', $week12DateTime)
                        ->isNotEmpty();
                    if ($paidOnScheduledDate11) {
                        $paid11 = "Paid";
                    } else if ($currentDate > $week11DateTime) {
                        $paid11 = "Missed";
                    } else {
                        $paid11 = "Pending";
                    }
                    //
                    $paidOnScheduledDate12 = $billingRecords
                    ->where('created_at', '>=', $week12DateTime)
                    ->where('created_at', '<', $week13DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate12) {
                        $paid12 = "Paid";
                    } else if ($currentDate > $week12DateTime) {
                        $paid12 = "Missed";
                    } else {
                        $paid12 = "Pending";
                    }
                    //
                    $paidOnScheduledDate13 = $billingRecords
                    ->where('created_at', '>=', $week13DateTime)
                    ->where('created_at', '<', $week14DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate13) {
                        $paid13 = "Paid";
                    } else if ($currentDate > $week13DateTime) {
                        $paid13 = "Missed";
                    } else {
                        $paid13 = "Pending";
                    }
                    //
                    $paidOnScheduledDate14 = $billingRecords
                    ->where('created_at', '>=', $week14DateTime)
                    ->where('created_at', '<', $week21DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate14) {
                        $paid14 = "Paid";
                    } elseif ($currentDate > $week14DateTime) {
                        $paid14 = "Missed";
                    } else {
                        $paid14 = "Pending";
                    }
                    //
                    $paidOnScheduledDate21 = $billingRecords
                    ->where('created_at', '>=', $week21DateTime)
                    ->where('created_at', '<', $week22DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate21) {
                        $paid21 = "Paid";
                    } elseif ($currentDate > $week21DateTime) {
                        $paid21 = "Missed";
                    } else {
                        $paid21 = "Pending";
                    }
                    //
                    $paidOnScheduledDate22 = $billingRecords
                    ->where('created_at', '>=', $week22DateTime)
                    ->where('created_at', '<', $week23DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate22) {
                        $paid22 = "Paid";
                    } elseif ($currentDate > $week22DateTime) {
                        $paid22 = "Missed";
                    } else {
                        $paid22 = "Pending";
                    }
                    //
                    $paidOnScheduledDate23 = $billingRecords
                    ->where('created_at', '>=', $week23DateTime)
                    ->where('created_at', '<', $week24DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate23) {
                        $paid23 = "Paid";
                    } elseif ($currentDate > $week23DateTime) {
                        $paid23 = "Missed";
                    } else {
                        $paid23 = "Pending";
                    }
                    //
                    $paidOnScheduledDate24 = $billingRecords
                    ->where('created_at', '>=', $week24DateTime)
                    ->where('created_at', '<', $week31DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate24) {
                        $paid24 = "Paid";
                    } elseif ($currentDate > $week24DateTime) {
                        $paid24 = "Missed";
                    } else {
                        $paid24 = "Pending";
                    }
                    //
                    $paidOnScheduledDate31 = $billingRecords
                    ->where('created_at', '>=', $week31DateTime)
                    ->where('created_at', '<', $week32DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate31) {
                        $paid31 = "Paid";
                    } elseif ($currentDate > $week31DateTime) {
                        $paid31 = "Missed";
                    } else {
                        $paid31 = "Pending";
                    }
                    //
                    $paidOnScheduledDate32 = $billingRecords
                    ->where('created_at', '>=', $week32DateTime)
                    ->where('created_at', '<', $week33DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate32) {
                        $paid32 = "Paid";
                    } elseif ($currentDate > $week32DateTime) {
                        $paid32 = "Missed";
                    } else {
                        $paid32 = "Pending";
                    }
                    //
                    $paidOnScheduledDate33 = $billingRecords
                    ->where('created_at', '>=', $week33DateTime)
                    ->where('created_at', '<', $week34DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate33) {
                        $paid33 = "Paid";
                    } elseif ($currentDate > $week33DateTime) {
                        $paid33 = "Missed";
                    } else {
                        $paid33 = "Pending";
                    }
                    //
                    $paidOnScheduledDate34 = $billingRecords
                    ->where('created_at', '>=', $week34DateTime)
                    ->where('created_at', '<', $week41DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate34) {
                        $paid34 = "Paid";
                    } elseif ($currentDate > $week34DateTime) {
                        $paid34 = "Missed";
                    } else {
                        $paid34 = "Pending";
                    }
                    //
                    $paidOnScheduledDate41 = $billingRecords
                    ->where('created_at', '>=', $week41DateTime)
                    ->where('created_at', '<', $week42DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate41) {
                        $paid41 = "Paid";
                    } elseif ($currentDate > $week41DateTime) {
                        $paid41 = "Missed";
                    } else {
                        $paid41 = "Pending";
                    }
                    //
                    $paidOnScheduledDate42 = $billingRecords
                    ->where('created_at', '>=', $week42DateTime)
                    ->where('created_at', '<', $week43DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate42) {
                        $paid42 = "Paid";
                    } elseif ($currentDate > $week42DateTime) {
                        $paid42 = "Missed";
                    } else {
                        $paid42 = "Pending";
                    }
                    //
                    $paidOnScheduledDate43 = $billingRecords
                    ->where('created_at', '>=', $week43DateTime)
                    ->where('created_at', '<', $week44DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate43) {
                        $paid43 = "Paid";
                    } elseif ($currentDate > $week43DateTime) {
                        $paid43 = "Missed";
                    } else {
                        $paid43 = "Pending";
                    }
                    //
                    $paidOnScheduledDate44 = $billingRecords
                    ->where('created_at', '>=', $week44DateTime)
                    ->where('created_at', '<', $week51DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate44) {
                        $paid44 = "Paid";
                    } elseif ($currentDate > $week44DateTime) {
                        $paid44 = "Missed";
                    } else {
                        $paid44 = "Pending";
                    }
                    //


                    $paidOnScheduledDate51 = $billingRecords
                    ->where('created_at', '>=', $week51DateTime)
                    ->where('created_at', '<', $week52DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate51) {
                        $paid51 = "Paid";
                    } elseif ($currentDate > $week51DateTime) {
                        $paid51 = "Missed";
                    } else {
                        $paid51 = "Pending";
                    }
                    //
                    $paidOnScheduledDate52 = $billingRecords
                    ->where('created_at', '>=', $week52DateTime)
                    ->where('created_at', '<', $week53DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate52) {
                        $paid52 = "Paid";
                    } elseif ($currentDate > $week52DateTime) {
                        $paid52 = "Missed";
                    } else {
                        $paid52 = "Pending";
                    }
                    //
                    $paidOnScheduledDate53 = $billingRecords
                    ->where('created_at', '>=', $week53DateTime)
                    ->where('created_at', '<', $week54DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate53) {
                        $paid53 = "Paid";
                    } elseif ($currentDate > $week53DateTime) {
                        $paid53 = "Missed";
                    } else {
                        $paid53 = "Pending";
                    }
                    //
                    $paidOnScheduledDate54 = $billingRecords
                    ->where('created_at', '>=', $week54DateTime)
                    ->where('created_at', '<', $week61DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate54) {
                        $paid54 = "Paid";
                    } elseif ($currentDate > $week54DateTime) {
                        $paid54 = "Missed";
                    } else {
                        $paid54 = "Pending";
                    }
                    //
                        return $week11Formatted . '---' . $paid11 ."\n_________________________________________________________\n" .
                                $week12Formatted . '---' . $paid12 ."\n_________________________________________________________\n" .
                                $week13Formatted . '---' . $paid13 ."\n_________________________________________________________\n" .
                                $week14Formatted . '---' . $paid14 ."\n_________________________________________________________\n" .
                                $week21Formatted . '---' . $paid21 ."\n_________________________________________________________\n" .
                                $week22Formatted . '---' . $paid22 ."\n_________________________________________________________\n" .
                                $week23Formatted . '---' . $paid23 ."\n_________________________________________________________\n" .
                                $week24Formatted . '---' . $paid24 ."\n_________________________________________________________\n" .
                                $week31Formatted . '---' . $paid31 ."\n_________________________________________________________\n" .
                                $week32Formatted . '---' . $paid32 ."\n_________________________________________________________\n" .
                                $week33Formatted . '---' . $paid33 ."\n_________________________________________________________\n" .
                                $week34Formatted . '---' . $paid34 ."\n_________________________________________________________\n" .
                                $week41Formatted . '---' . $paid41 ."\n_________________________________________________________\n" .
                                $week42Formatted . '---' . $paid42 ."\n_________________________________________________________\n" .
                                $week43Formatted . '---' . $paid43 ."\n_________________________________________________________\n" .
                                $week44Formatted . '---' . $paid44 ."\n_________________________________________________________\n" .
                                $week51Formatted . '---' . $paid51 ."\n_________________________________________________________\n" .
                                $week52Formatted . '---' . $paid52 ."\n_________________________________________________________\n" .
                                $week53Formatted . '---' . $paid53 ."\n_________________________________________________________\n" .
                                $week54Formatted . '---' . $paid54;

                    }  else if($term === '6' && $perweek ==='1'){
                        $month1 = new DateTime($record->month1);
                        $month2 = new DateTime($record->month2);
                        $month3 = new DateTime($record->month3);
                        $month4 = new DateTime($record->month4);
                        $month5 = new DateTime($record->month5);
                        $month6 = new DateTime($record->month6);
                        $month7 = clone $month6;
                        $month7->add(new DateInterval('P1M'));
                        $formattedMonth1 = $month1->format('F j, Y');
                        $formattedMonth2 = $month2->format('F j, Y');
                        $formattedMonth3 = $month3->format('F j, Y');
                        $formattedMonth4 = $month4->format('F j, Y');
                        $formattedMonth5 = $month5->format('F j, Y');
                        $formattedMonth6 = $month6->format('F j, Y');
                        $formattedMonth7 = $month7->format('F j, Y');

                        $paid1 = "";
                        $paid2 = "";
                        $paid3 = "";
                        $paid4 = "";
                        $paid5 = "";
                        $paid6 = "";
                        //
                        $paidOnScheduledDate = $billingRecords
                            ->where('created_at', '>=', $month1)
                            ->where('created_at', '<', $month2)
                            ->isNotEmpty();
                        if ($paidOnScheduledDate) {
                            $paid1 = "Paid";
                        } elseif ($currentDate > $month1) {
                            $paid1 = "Missed";
                        } else {
                            $paid1 = "Pending";
                        }
                        //
                        $paidOnScheduledDate2 = $billingRecords
                        ->where('created_at', '>=', $month2)
                        ->where('created_at', '<', $month3)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate2) {
                            $paid2 = "Paid";
                        } elseif ($currentDate > $month2) {
                            $paid2 = "Missed";
                        } else {
                            $paid2 = "Pending";
                        }
                        //
                        $paidOnScheduledDate3 = $billingRecords
                        ->where('created_at', '>=', $month3)
                        ->where('created_at', '<', $month4)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate3) {
                            $paid3 = "Paid";
                        } elseif ($currentDate > $month3) {
                            $paid3 = "Missed";
                        } else {
                            $paid3 = "Pending";
                        }
                        //
                        $paidOnScheduledDate4 = $billingRecords
                        ->where('created_at', '>=', $month4)
                        ->where('created_at', '<', $month5)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate4) {
                            $paid4 = "Paid";
                        } elseif ($currentDate > $month4) {
                            $paid4 = "Missed";
                        } else {
                            $paid4 = "Pending";
                        }
                        //
                        $paidOnScheduledDate5 = $billingRecords
                        ->where('created_at', '>=', $month5)
                        ->where('created_at', '<', $month6)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate5) {
                            $paid5 = "Paid";
                        } elseif ($currentDate > $month5) {
                            $paid5 = "Missed";
                        } else {
                            $paid5 = "Pending";
                        }

                        //
                        $paidOnScheduledDate6 = $billingRecords
                        ->where('created_at', '>=', $month6)
                        ->where('created_at', '<', $month7)
                        ->isNotEmpty();
                        if ($paidOnScheduledDate6) {
                            $paid6 = "Paid";
                        } elseif ($currentDate > $month6) {
                            $paid6 = "Missed";
                        } else {
                            $paid6 = "Pending";
                        }
                        $result = $formattedMonth1 . '---' . $paid1 . "\n_________________________________________________________\n" .
                                  $formattedMonth2 . '---' . $paid2 . "\n_________________________________________________________\n" .
                                  $formattedMonth3 . '---' . $paid3 ."\n_________________________________________________________\n" .
                                  $formattedMonth4 . '---'. $paid4 ."\n_________________________________________________________\n" .
                                  $formattedMonth5 . '---'. $paid5 ."\n_________________________________________________________\n" .
                                  $formattedMonth6 . '---'. $paid6;


                        return $result;

                    } else if ($term === '6' && $perweek ==='4'){
                        $week11DateTime = new DateTime($record->week11);
                        $week12DateTime = new DateTime($record->week12);
                        $week13DateTime = new DateTime($record->week13);
                        $week14DateTime = new DateTime($record->week14);
                        $week21DateTime = new DateTime($record->week21);
                        $week22DateTime = new DateTime($record->week22);
                        $week23DateTime = new DateTime($record->week23);
                        $week24DateTime = new DateTime($record->week24);
                        $week31DateTime = new DateTime($record->week31);
                        $week32DateTime = new DateTime($record->week32);
                        $week33DateTime = new DateTime($record->week33);
                        $week34DateTime = new DateTime($record->week34);
                        $week41DateTime = new DateTime($record->week41);
                        $week42DateTime = new DateTime($record->week42);
                        $week43DateTime = new DateTime($record->week43);
                        $week44DateTime = new DateTime($record->week44);
                        $week51DateTime = new DateTime($record->week51);
                        $week52DateTime = new DateTime($record->week52);
                        $week53DateTime = new DateTime($record->week53);
                        $week54DateTime = new DateTime($record->week54);
                        $week61DateTime = new DateTime($record->week61);
                        $week62DateTime = new DateTime($record->week62);
                        $week63DateTime = new DateTime($record->week63);
                        $week64DateTime = new DateTime($record->week64);
                        $week71DateTime = clone $week64DateTime;
                        $week71DateTime->add(new DateInterval('P7D'));


                        $week11Formatted = $week11DateTime->format('F j, Y');
                        $week12Formatted = $week12DateTime->format('F j, Y');
                        $week13Formatted = $week13DateTime->format('F j, Y');
                        $week14Formatted = $week14DateTime->format('F j, Y');
                        $week21Formatted = $week21DateTime->format('F j, Y');
                        $week22Formatted = $week22DateTime->format('F j, Y');
                        $week23Formatted = $week23DateTime->format('F j, Y');
                        $week24Formatted = $week24DateTime->format('F j, Y');
                        $week31Formatted = $week31DateTime->format('F j, Y');
                        $week32Formatted = $week32DateTime->format('F j, Y');
                        $week33Formatted = $week33DateTime->format('F j, Y');
                        $week34Formatted = $week34DateTime->format('F j, Y');
                        $week41Formatted = $week41DateTime->format('F j, Y');
                        $week42Formatted = $week42DateTime->format('F j, Y');
                        $week43Formatted = $week43DateTime->format('F j, Y');
                        $week44Formatted = $week44DateTime->format('F j, Y');
                        $week51Formatted = $week51DateTime->format('F j, Y');
                        $week52Formatted = $week52DateTime->format('F j, Y');
                        $week53Formatted = $week53DateTime->format('F j, Y');
                        $week54Formatted = $week54DateTime->format('F j, Y');
                        $week61Formatted = $week61DateTime->format('F j, Y');
                        $week62Formatted = $week62DateTime->format('F j, Y');
                        $week63Formatted = $week63DateTime->format('F j, Y');
                        $week64Formatted = $week64DateTime->format('F j, Y');

                        $paid11 = "";
                        $paid12 = "";
                        $paid13 = "";
                        $paid14 = "";
                        $paid21 = "";
                        $paid22 = "";
                        $paid23 = "";
                        $paid24 = "";
                        $paid31 = "";
                        $paid32 = "";
                        $paid33 = "";
                        $paid34 = "";
                        $paid41 = "";
                        $paid42 = "";
                        $paid43 = "";
                        $paid44 = "";
                        $paid51 = "";
                        $paid52 = "";
                        $paid53 = "";
                        $paid54 = "";
                        $paid61 = "";
                        $paid62 = "";
                        $paid63 = "";
                        $paid64 = "";

                        //
                    $paidOnScheduledDate11 = $billingRecords
                        ->where('created_at', '>=', $week11DateTime)
                        ->where('created_at', '<', $week12DateTime)
                        ->isNotEmpty();
                    if ($paidOnScheduledDate11) {
                        $paid11 = "Paid";
                    } else if ($currentDate > $week11DateTime) {
                        $paid11 = "Missed";
                    } else {
                        $paid11 = "Pending";
                    }
                    //
                    $paidOnScheduledDate12 = $billingRecords
                    ->where('created_at', '>=', $week12DateTime)
                    ->where('created_at', '<', $week13DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate12) {
                        $paid12 = "Paid";
                    } else if ($currentDate > $week12DateTime) {
                        $paid12 = "Missed";
                    } else {
                        $paid12 = "Pending";
                    }
                    //
                    $paidOnScheduledDate13 = $billingRecords
                    ->where('created_at', '>=', $week13DateTime)
                    ->where('created_at', '<', $week14DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate13) {
                        $paid13 = "Paid";
                    } else if ($currentDate > $week13DateTime) {
                        $paid13 = "Missed";
                    } else {
                        $paid13 = "Pending";
                    }
                    //
                    $paidOnScheduledDate14 = $billingRecords
                    ->where('created_at', '>=', $week14DateTime)
                    ->where('created_at', '<', $week21DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate14) {
                        $paid14 = "Paid";
                    } elseif ($currentDate > $week14DateTime) {
                        $paid14 = "Missed";
                    } else {
                        $paid14 = "Pending";
                    }
                    //
                    $paidOnScheduledDate21 = $billingRecords
                    ->where('created_at', '>=', $week21DateTime)
                    ->where('created_at', '<', $week22DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate21) {
                        $paid21 = "Paid";
                    } elseif ($currentDate > $week21DateTime) {
                        $paid21 = "Missed";
                    } else {
                        $paid21 = "Pending";
                    }
                    //
                    $paidOnScheduledDate22 = $billingRecords
                    ->where('created_at', '>=', $week22DateTime)
                    ->where('created_at', '<', $week23DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate22) {
                        $paid22 = "Paid";
                    } elseif ($currentDate > $week22DateTime) {
                        $paid22 = "Missed";
                    } else {
                        $paid22 = "Pending";
                    }
                    //
                    $paidOnScheduledDate23 = $billingRecords
                    ->where('created_at', '>=', $week23DateTime)
                    ->where('created_at', '<', $week24DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate23) {
                        $paid23 = "Paid";
                    } elseif ($currentDate > $week23DateTime) {
                        $paid23 = "Missed";
                    } else {
                        $paid23 = "Pending";
                    }
                    //
                    $paidOnScheduledDate24 = $billingRecords
                    ->where('created_at', '>=', $week24DateTime)
                    ->where('created_at', '<', $week31DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate24) {
                        $paid24 = "Paid";
                    } elseif ($currentDate > $week24DateTime) {
                        $paid24 = "Missed";
                    } else {
                        $paid24 = "Pending";
                    }
                    //
                    $paidOnScheduledDate31 = $billingRecords
                    ->where('created_at', '>=', $week31DateTime)
                    ->where('created_at', '<', $week32DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate31) {
                        $paid31 = "Paid";
                    } elseif ($currentDate > $week31DateTime) {
                        $paid31 = "Missed";
                    } else {
                        $paid31 = "Pending";
                    }
                    //
                    $paidOnScheduledDate32 = $billingRecords
                    ->where('created_at', '>=', $week32DateTime)
                    ->where('created_at', '<', $week33DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate32) {
                        $paid32 = "Paid";
                    } elseif ($currentDate > $week32DateTime) {
                        $paid32 = "Missed";
                    } else {
                        $paid32 = "Pending";
                    }
                    //
                    $paidOnScheduledDate33 = $billingRecords
                    ->where('created_at', '>=', $week33DateTime)
                    ->where('created_at', '<', $week34DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate33) {
                        $paid33 = "Paid";
                    } elseif ($currentDate > $week33DateTime) {
                        $paid33 = "Missed";
                    } else {
                        $paid33 = "Pending";
                    }
                    //
                    $paidOnScheduledDate34 = $billingRecords
                    ->where('created_at', '>=', $week34DateTime)
                    ->where('created_at', '<', $week41DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate34) {
                        $paid34 = "Paid";
                    } elseif ($currentDate > $week34DateTime) {
                        $paid34 = "Missed";
                    } else {
                        $paid34 = "Pending";
                    }
                    //
                    $paidOnScheduledDate41 = $billingRecords
                    ->where('created_at', '>=', $week41DateTime)
                    ->where('created_at', '<', $week42DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate41) {
                        $paid41 = "Paid";
                    } elseif ($currentDate > $week41DateTime) {
                        $paid41 = "Missed";
                    } else {
                        $paid41 = "Pending";
                    }
                    //
                    $paidOnScheduledDate42 = $billingRecords
                    ->where('created_at', '>=', $week42DateTime)
                    ->where('created_at', '<', $week43DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate42) {
                        $paid42 = "Paid";
                    } elseif ($currentDate > $week42DateTime) {
                        $paid42 = "Missed";
                    } else {
                        $paid42 = "Pending";
                    }
                    //
                    $paidOnScheduledDate43 = $billingRecords
                    ->where('created_at', '>=', $week43DateTime)
                    ->where('created_at', '<', $week44DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate43) {
                        $paid43 = "Paid";
                    } elseif ($currentDate > $week43DateTime) {
                        $paid43 = "Missed";
                    } else {
                        $paid43 = "Pending";
                    }
                    //
                    $paidOnScheduledDate44 = $billingRecords
                    ->where('created_at', '>=', $week44DateTime)
                    ->where('created_at', '<', $week51DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate44) {
                        $paid44 = "Paid";
                    } elseif ($currentDate > $week44DateTime) {
                        $paid44 = "Missed";
                    } else {
                        $paid44 = "Pending";
                    }
                    //



                    $paidOnScheduledDate51 = $billingRecords
                    ->where('created_at', '>=', $week51DateTime)
                    ->where('created_at', '<', $week52DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate51) {
                        $paid51 = "Paid";
                    } elseif ($currentDate > $week51DateTime) {
                        $paid51 = "Missed";
                    } else {
                        $paid51 = "Pending";
                    }
                    //
                    $paidOnScheduledDate52 = $billingRecords
                    ->where('created_at', '>=', $week52DateTime)
                    ->where('created_at', '<', $week53DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate52) {
                        $paid52 = "Paid";
                    } elseif ($currentDate > $week52DateTime) {
                        $paid52 = "Missed";
                    } else {
                        $paid52 = "Pending";
                    }
                    //
                    $paidOnScheduledDate53 = $billingRecords
                    ->where('created_at', '>=', $week53DateTime)
                    ->where('created_at', '<', $week54DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate53) {
                        $paid53 = "Paid";
                    } elseif ($currentDate > $week53DateTime) {
                        $paid53 = "Missed";
                    } else {
                        $paid53 = "Pending";
                    }
                    //
                    $paidOnScheduledDate54 = $billingRecords
                    ->where('created_at', '>=', $week54DateTime)
                    ->where('created_at', '<', $week61DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate54) {
                        $paid54 = "Paid";
                    } elseif ($currentDate > $week54DateTime) {
                        $paid54 = "Missed";
                    } else {
                        $paid54 = "Pending";
                    }
                    //

                    $paidOnScheduledDate61 = $billingRecords
                    ->where('created_at', '>=', $week61DateTime)
                    ->where('created_at', '<', $week62DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate61) {
                        $paid61 = "Paid";
                    } elseif ($currentDate > $week61DateTime) {
                        $paid61 = "Missed";
                    } else {
                        $paid61 = "Pending";
                    }
                    //
                    $paidOnScheduledDate62 = $billingRecords
                    ->where('created_at', '>=', $week62DateTime)
                    ->where('created_at', '<', $week63DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate62) {
                        $paid62 = "Paid";
                    } elseif ($currentDate > $week62DateTime) {
                        $paid62 = "Missed";
                    } else {
                        $paid62 = "Pending";
                    }
                    //
                    $paidOnScheduledDate63 = $billingRecords
                    ->where('created_at', '>=', $week63DateTime)
                    ->where('created_at', '<', $week64DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate63) {
                        $paid63 = "Paid";
                    } elseif ($currentDate > $week63DateTime) {
                        $paid63 = "Missed";
                    } else {
                        $paid63 = "Pending";
                    }
                    //
                    $paidOnScheduledDate64 = $billingRecords
                    ->where('created_at', '>=', $week64DateTime)
                    ->where('created_at', '<', $week71DateTime)
                    ->isNotEmpty();
                    if ($paidOnScheduledDate64) {
                        $paid64 = "Paid";
                    } elseif ($currentDate > $week64DateTime) {
                        $paid64 = "Missed";
                    } else {
                        $paid64 = "Pending";
                    }
                    //
                        return $week11Formatted . '---' . $paid11 ."\n_________________________________________________________\n" .
                                $week12Formatted . '---' . $paid12 ."\n_________________________________________________________\n" .
                                $week13Formatted . '---' . $paid13 ."\n_________________________________________________________\n" .
                                $week14Formatted . '---' . $paid14 ."\n_________________________________________________________\n" .
                                $week21Formatted . '---' . $paid21 ."\n_________________________________________________________\n" .
                                $week22Formatted . '---' . $paid22 ."\n_________________________________________________________\n" .
                                $week23Formatted . '---' . $paid23 ."\n_________________________________________________________\n" .
                                $week24Formatted . '---' . $paid24 ."\n_________________________________________________________\n" .
                                $week31Formatted . '---' . $paid31 ."\n_________________________________________________________\n" .
                                $week32Formatted . '---' . $paid32 ."\n_________________________________________________________\n" .
                                $week33Formatted . '---' . $paid33 ."\n_________________________________________________________\n" .
                                $week34Formatted . '---' . $paid34 ."\n_________________________________________________________\n" .
                                $week41Formatted . '---' . $paid41 ."\n_________________________________________________________\n" .
                                $week42Formatted . '---' . $paid42 ."\n_________________________________________________________\n" .
                                $week43Formatted . '---' . $paid43 ."\n_________________________________________________________\n" .
                                $week44Formatted . '---' . $paid44 ."\n_________________________________________________________\n" .
                                $week51Formatted . '---' . $paid51 ."\n_________________________________________________________\n" .
                                $week52Formatted . '---' . $paid52 ."\n_________________________________________________________\n" .
                                $week53Formatted . '---' . $paid53 ."\n_________________________________________________________\n" .
                                $week54Formatted . '---' . $paid54 ."\n_________________________________________________________\n" .
                                $week61Formatted . '---' . $paid61 ."\n_________________________________________________________\n" .
                                $week62Formatted . '---' . $paid62 ."\n_________________________________________________________\n" .
                                $week63Formatted . '---' . $paid63 ."\n_________________________________________________________\n" .
                                $week64Formatted . '---' . $paid64;

                    } else{
                        return "Error";
                    }
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
