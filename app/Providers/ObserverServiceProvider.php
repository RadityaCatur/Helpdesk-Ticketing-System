<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Priority;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use App\Observers\CategoryObserver;
use App\Observers\CompanyObserver;
use App\Observers\PriorityObserver;
use App\Observers\ProjectObserver;
use App\Observers\TicketObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Ticket::observe(TicketObserver::class);
        Project::observe(ProjectObserver::class);
        Priority::observe(PriorityObserver::class);
        Company::observe(CompanyObserver::class);
        Category::observe(CategoryObserver::class);
    }    
}
