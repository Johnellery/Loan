<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\Seller;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;


class DownloadPdfController extends Controller
{
    public function download (Billing $record)
    {
        $user = Auth::user();
        $bike = $record->applicant->bike->name;
        $installment = $record->amount;
        $email = $record->user->email;
        $branch = $record->user->branch->name;
        $role = $record->user->role->name;
        $first = $record->user->first;
        $middle = $record->user->middle;
        $last = $record->user->last;
        $customer = new Buyer([
            'lname' => $last,
            'name' => $first,
            'custom_fields' => [
                $branch => $role,
            ],
        ]);
        $seller = new Seller([
            'address' => $branch,
            'custom_fields' => [
                $branch => $role,
            ],
        ]);
        $item = (new InvoiceItem())->title($bike)->pricePerUnit($installment);

        $invoice = Invoice::make()
            ->buyer($customer)
            ->seller($seller)
            ->addItem($item);

        return $invoice->stream();

    }
}
