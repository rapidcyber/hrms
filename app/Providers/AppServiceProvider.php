<?php

namespace App\Providers;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
// app/Providers/AppServiceProvider.php

    public function boot(): void
    {
        // Register flux components namespace
        $this->loadViewComponentsAs('flux', [
            \App\View\Components\Flux\Table::class,
            \App\View\Components\Flux\Table\Heading::class,
            \App\View\Components\Flux\Table\Row::class,
            \App\View\Components\Flux\Table\Cell::class,
        ]);

        // Optional: If you want to publish views
        $this->loadViewsFrom(resource_path('views/components/flux'), 'flux');

        Collection::macro('paginate', function ($perPage = 15, $pageName = 'page') {
            $page = LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $this->forPage($page, $perPage), //Items for the current page
                $this->count(), //Total items
                $perPage, //Items per page
                $page, //Current page
                ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => $pageName] //Pagination related settings
            );
        });
    }
}
