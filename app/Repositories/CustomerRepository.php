<?php

namespace App\Repositories;

use App\Interfaces\CustomerRepositoryInterface;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function getAllCustomers($createdBy)
    {
        return Customer::where('created_by', $createdBy)->Paginate(10);
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
