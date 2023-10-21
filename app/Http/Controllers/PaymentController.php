<?php
namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;
use Omnipay\Omnipay;
use App\Models\Billing;

class PaymentController extends Controller
{
    private $gateway;

    public function __construct()
    {
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId(env('PAYPAL_CLIENT_ID'));
        $this->gateway->setSecret(env('PAYPAL_CLIENT_SECRET'));
        $this->gateway->setTestMode(true);
    }

    public function pay(Applicant $record)
    {
        try {
            $loan = $record;
            $amount = $loan->payment;
            $user = $loan->user;
            $response = $this->gateway->purchase([
                'amount' => $amount,
                // 'amountpdf' => $amount,
                // 'applicant_user_id' => $user, // Add this line
                'currency' => env('PAYPAL_CURRENCY'),
                'returnUrl' => route('success'),
                'cancelUrl' => route('error'),
            ])->send();

            if ($response->isRedirect()) {
                $response->redirect();
            } else {
                return $response->getMessage();
            }
            session(['applicant' => $record]);

            // Redirect to the success route
            return redirect()->route('success');
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
        // Pass $record to the success route
        // return redirect()->route('success', ['record' => $record]);
    }


    public function success(Request $request)
    {
        if ($request->input('paymentId') && $request->input('PayerID')) {
            // Retrieve the Applicant model from the session
            $applicant = session('applicant');

            // Check if the Applicant is available
            if ($applicant) {
                $transaction = $this->gateway->completePurchase([
                    'payer_id' => $request->input('PayerID'),
                    'transactionReference' => $request->input('paymentId'),
                ]);

                $response = $transaction->send();

                if ($response->isSuccessful()) {
                    $arr = $response->getData();
                    $payment = new Billing();

                    $transactionNumber = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);

                    $amountpdf = $applicant->payment;
                    $remainingpdf = $applicant->remaining_balance;
                    $pdf = $remainingpdf -  $amountpdf;




                    $payment->transaction_number = $transactionNumber;
                    $payment->amount = $arr['transactions'][0]['amount']['total'];
                    $payment->currency = env('PAYPAL_CURRENCY');
                    $payment->billing_status = 'remitted';
                    $payment->cashier = 'Paypal';
                    $payment->applicant_user_id = $applicant->user->id; // Adjust this line
                    $payment->amountpdf = $pdf;
                    $payment->payment_type = 'in_partial';
                    $payment->branch_id = $applicant->user->branch->id;
                    $payment->applicant_id = $applicant->id;
                    $payment->save();

                    return redirect('/customer/loannns');
                } else {
                    return $response->getMessage();
                }
            } else {
                return 'Applicant data not found.';
            }
        } else {
            return redirect('/customer/loannns');
        }
    }

    public function error()
    {
        return redirect('/customer/loannns');
    }
}
