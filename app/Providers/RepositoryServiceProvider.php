<?php

namespace App\Providers;

use App\Interfaces\AdminInterface;
use App\Interfaces\Api\Driver\DriverRepositoryInterface;
use App\Interfaces\Api\User\UserRepositoryInterface;
use App\Repository\Api\User\UserRepository as UserApiRepository;
use App\Repository\Api\Driver\DriverRepository as DriverApiRepository;
use App\Interfaces\AreaInterface;
use App\Interfaces\AuthInterface;
use App\Interfaces\CityInterface;
use App\Interfaces\DriverDocumentInterface;
use App\Interfaces\DriverInterface;
use App\Interfaces\InsuranceDriverInterface;
use App\Interfaces\InsurancePaymentInterface;
use App\Interfaces\NotificationInterface;
use App\Interfaces\SettingInterface;
use App\Interfaces\SliderInterface;
use App\Interfaces\TripInterface;
use App\Interfaces\UserInterface;
use App\Repository\AdminRepository;
use App\Repository\AreaRepository;
use App\Repository\AuthRepository;
use App\Repository\CityRepository;
use App\Repository\DriverDocumentRepository;
use App\Repository\DriverRepository;
use App\Repository\InsuranceDriverRepository;
use App\Repository\InsurancePaymentRepository;
use App\Repository\NotificationRepository;
use App\Repository\SettingRepository;
use App\Repository\SliderRepository;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use Illuminate\Support\ServiceProvider;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // start Web classes and interfaces
        $this->app->bind(AuthInterface::class,AuthRepository::class);
        $this->app->bind(AdminInterface::class,AdminRepository::class);
        $this->app->bind(AreaInterface::class,AreaRepository::class);
        $this->app->bind(UserInterface::class,UserRepository::class);
        $this->app->bind(DriverInterface::class,DriverRepository::class);
        $this->app->bind(TripInterface::class,TripRepository::class);
        $this->app->bind(NotificationInterface::class,NotificationRepository::class);
        $this->app->bind(DriverDocumentInterface::class,DriverDocumentRepository::class);
        $this->app->bind(CityInterface::class,CityRepository::class);
        $this->app->bind(SliderInterface::class,SliderRepository::class);
        $this->app->bind(SettingInterface::class,SettingRepository::class);
        $this->app->bind(InsuranceDriverInterface::class,InsuranceDriverRepository::class);
        $this->app->bind(InsurancePaymentInterface::class,InsurancePaymentRepository::class);

        // ----------------------------------------------------------------



        // start Api classes and interfaces
        $this->app->bind(UserRepositoryInterface::class,UserApiRepository::class);
        $this->app->bind(DriverRepositoryInterface::class,DriverApiRepository::class);
        // ----------------------------------------------------------------

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
