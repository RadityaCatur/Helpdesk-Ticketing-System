<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class CompanyObserver
{

    public static $observes = [
        Company::class
    ];

    /**
     * Handle the company "created" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function created(Company $company)
    {
        activity()
            ->performedOn($company)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah menambahkan daftar perusahaan dengan nama ' . $company->name);
    }

    /**
     * Handle the company "updated" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function updated(Company $company)
    {
        activity()
            ->performedOn($company)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah mengubah info perusahaan dengan nama ' . $company->name);
    }

    /**
     * Handle the company "deleting" event.
     * 
     * @param   \App\Models\Company $company
     * @return  void
     */
    public function deleting(Company $company) {
        $company->users()->each(function($user) {
            $user->company()->dissociate();
            $user->save();
        });
    }

    /**
     * Handle the company "deleted" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function deleted(Company $company)
    {
        activity()
            ->performedOn($company)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah menghapus ' . $company->name . ' dari daftar perusahaan');        
    }

    /**
     * Handle the company "restored" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function restored(Company $company)
    {
        //
    }

    /**
     * Handle the company "force deleted" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function forceDeleted(Company $company)
    {
        //
    }
}
