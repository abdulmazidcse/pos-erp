<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
 
use App\Models\Company;
use App\Observers\CompanyObserver;
use App\Observers\AccountClassObserver;


use App\Models\AccountClass;
use App\Models\AccountType;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Company::observe(CompanyObserver::class);
        AccountClass::observe(AccountClassObserver::class);
    }
}
