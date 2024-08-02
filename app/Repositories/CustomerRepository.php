<?php

namespace App\Repositories;

use App\Interfaces\CustomerRepositoryInterface;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function getAllCustomers($createdBy)
    {
        $customers = Customer::select(
            'customers.id',
            'customers.first_name',
            'customers.last_name',
            'customers.company_name',
            'customers.currency',
            'customers.email',
            'customers.phone_number',
            'customers.mobile_number',
            'customers.address',
            'customers.logo',
            DB::raw('COALESCE(SUM(payments.amount_received), 0) as total_amount_received'),
            DB::raw('COALESCE(SUM(invoices.total_amount), 0) as total_invoice_amount')
        )
            ->leftJoin('invoices', 'customers.id', '=', 'invoices.customer_id')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->where('customers.created_by', $createdBy)
            ->groupBy(
                'customers.id',
                'customers.first_name',
                'customers.last_name',
                'customers.company_name',
                'customers.currency',
                'customers.email',
                'customers.phone_number',
                'customers.mobile_number',
                'customers.address',
                'customers.logo'
            )
            ->get()
            ->map(function ($customer) {
                $customer->amount_received = $customer->total_amount_received;
                $customer->amount_due = $customer->total_invoice_amount - $customer->total_amount_received;
                unset($customer->total_amount_received, $customer->total_invoice_amount);
                return $customer;
            });

        return $customers;
    }

    public function getCustomerById($CustomerID)
    {
        return Customer::findOrFail($CustomerID);
    }

    public function deleteCustomer($CustomerID)
    {
        $customer = Customer::findOrFail($CustomerID);

        if ($customer->logo) {
            Storage::disk('public')->delete($customer->logo);
        }

        if ($customer) {
            $customer->delete();
        }
    }

    public function createCustomer(array $userDetails, $logo)
    {
        $user = Customer::create(array_merge($userDetails, ['logo' => $logo]));

        return $user;
    }
    public function updateCustomer($logo, array $userDetails)
    {
        if (isset($userDetails['customer_id'])) {
            $customer = Customer::find($userDetails['customer_id']);
            if ($customer && $logo) {
                // Delete the old logo if it exists
                if ($customer->logo) {
                    Storage::disk('public')->delete($customer->logo);
                }
            }
        }

        $user = Customer::where('id', $userDetails['customer_id'])->first();

        if ($user) {
            $user->update(array_merge($userDetails, ['logo' => $logo]));
            return $user;
        }

        return null;
    }
    public function findCustomerByEmail($email)
    {
        return Customer::where('email', $email)->first();
    }


}
