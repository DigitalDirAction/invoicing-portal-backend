<?php

namespace App\Interfaces;

interface CustomerRepositoryInterface
{
    public function getAllCustomers();
    public function getCustomerById($CustomerID);
    public function deleteCustomer($CustomerID);
    public function createCustomer(array $userDetails, $logo);
    public function updateCustomer($logo, array $newDetails);
    public function findCustomerByEmail($email);
}