<?php

namespace App\Observers;

use App\Models\AccountClass;
use App\Models\AccountType;

class AccountClassObserver
{
    /**
     * Handle the AccountClass "created" event.
     *
     * @param  \App\Models\AccountClass  $accountClass
     * @return void
     */
    public function created(AccountClass $accountClass)
    { 
        
    }

    /**
     * Handle the AccountClass "updated" event.
     *
     * @param  \App\Models\AccountClass  $accountClass
     * @return void
     */
    public function updated(AccountClass $accountClass)
    {
        //
    }

    /**
     * Handle the AccountClass "deleted" event.
     *
     * @param  \App\Models\AccountClass  $accountClass
     * @return void
     */
    public function deleted(AccountClass $accountClass)
    {
        //
    }

    /**
     * Handle the AccountClass "restored" event.
     *
     * @param  \App\Models\AccountClass  $accountClass
     * @return void
     */
    public function restored(AccountClass $accountClass)
    {
        //
    }

    /**
     * Handle the AccountClass "force deleted" event.
     *
     * @param  \App\Models\AccountClass  $accountClass
     * @return void
     */
    public function forceDeleted(AccountClass $accountClass)
    {
        //
    }
}
