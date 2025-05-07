<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Complaint;
use App\Observers\ComplaintObserver;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot()
    {
        Complaint::observe(ComplaintObserver::class);
        JsonResource::withoutWrapping();
        Paginator::useBootstrap(); // ✅ use Bootstrap pagination style
    }
}
