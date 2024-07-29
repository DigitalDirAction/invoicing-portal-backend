<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepository;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\PasswordResetRepository;
use App\Interfaces\PasswordResetRepositoryInterface;
use App\Repositories\CustomerRepository;
use App\Interfaces\CustomerRepositoryInterface;
use App\Repositories\BankingDetailRepository;
use App\Interfaces\BankingDetailRepositoryInterface;
use App\Repositories\InvoiceRepository;
use App\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\PaymentsRepository;
use App\Interfaces\PaymentsRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PasswordResetRepositoryInterface::class, PasswordResetRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(BankingDetailRepositoryInterface::class, BankingDetailRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(PaymentsRepositoryInterface::class, PaymentsRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
