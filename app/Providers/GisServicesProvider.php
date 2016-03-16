<?php

namespace Gis\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class GisServicesProvider extends ServiceProvider {

  /**
   * This will using for all of our application custom.
   * Bootstrap the application services.
   *
   * @return void
   */
  public function boot() {
      DB::listen(function($sql, $bindings) {

          for($j=0; $j<sizeof($bindings); $j++) {
              $sql = implode($bindings[$j], explode('?', $sql, 2));
          }
          $logFile = fopen(storage_path('logs/query.log'), 'a+');
          //write log to file
          fwrite($logFile, $sql . "\n");
          fclose($logFile);
      });
  }

  /**
   * Bind the returned class to interface.
   * Register our interfaces with Laravels IoC Container.
   * so when we change the class but stil implement this interface
   * If we decide to make a new PostRepositoryEloquent, we can simply tell Laravel about it here and all injected references in our app (PostRepository) will start using this one!
   *
   * @return void
   */
  public function register() {
    //User

    $this->app->bind(
      'Gis\Models\Repositories\UserRepository',
      'Gis\Models\Repositories\UserRepositoryEloquent'
    );

    //Group

    $this->app->bind(
      'Gis\Models\Repositories\GroupRepository',
      'Gis\Models\Repositories\GroupRepositoryEloquent'
    );

    $this->app->bind(
    		'Gis\Models\Repositories\FertilizerRepository',
    		'Gis\Models\Repositories\FertilizerRepositoryEloquent'
    );

    $this->app->bind(
    		'Gis\Models\Repositories\CropRepository',
    		'Gis\Models\Repositories\CropRepositoryEloquent'
    );

    $this->app->bind(
    		'Gis\Models\Repositories\StandardUserRepository',
    		'Gis\Models\Repositories\StandardUserRepositoryEloquent'
    );

    $this->app->bind(
    		'Gis\Models\Repositories\StandardCropRepository',
    		'Gis\Models\Repositories\StandardCropRepositoryEloquent'
    );

    $this->app->bind(
    		'Gis\Models\Repositories\StandardCropNitoRepository',
    		'Gis\Models\Repositories\StandardCropNitoRepositoryEloquent'
    );

    $this->app->bind(
    		'Gis\Models\Repositories\SystemFertilizerDefinitionDetailNitoRepository',
    		'Gis\Models\Repositories\SystemFertilizerDefinitionDetailNitoRepositoryEloquent'
    );
    $this->app->bind(
    		'Gis\Models\Repositories\SystemFertilizerDefinitionDetailKaliRepository',
    		'Gis\Models\Repositories\SystemFertilizerDefinitionDetailKaliRepositoryEloquent'
    );
    $this->app->bind(
    		'Gis\Models\Repositories\SystemFertilizerDefinitionDetailPhotphoRepository',
    		'Gis\Models\Repositories\SystemFertilizerDefinitionDetailPhotphoRepositoryEloquent'
    );

    $this->app->bind(
    		'Gis\Models\Repositories\StandardCropPhotphoRepository',
    		'Gis\Models\Repositories\StandardCropPhotphoRepositoryEloquent'
    );

    $this->app->bind(
    		'Gis\Models\Repositories\StandardCropKaliRepository',
    		'Gis\Models\Repositories\StandardCropKaliRepositoryEloquent'
    );

    $this->app->bind(
    		'Gis\Models\Repositories\DefaultStandardCropNitoRepository',
    		'Gis\Models\Repositories\DefaultStandardCropNitoRepositoryEloquent'
    );

    $this->app->bind(
    		'Gis\Models\Repositories\DefaultStandardCropPhotphoRepository',
    		'Gis\Models\Repositories\DefaultStandardCropPhotphoRepositoryEloquent'
    );

    $this->app->bind(
    		'Gis\Models\Repositories\DefaultStandardCropKaliRepository',
    		'Gis\Models\Repositories\DefaultStandardCropKaliRepositoryEloquent'
    );

    $this->app->bind(
            'Gis\Models\Repositories\StateOfUserRepository',
            'Gis\Models\Repositories\StateOfUserRepositoryEloquent'
    );

    $this->app->bind(
        'Gis\Models\Repositories\FertilizerDetailRepository',
        'Gis\Models\Repositories\FertilizerDetailRepositoryEloquent'
    );

    $this->app->bind(
        'Gis\Models\Repositories\MapColorRepository',
        'Gis\Models\Repositories\MapColorRepositoryEloquent'
    );

    //Bind Service.

    $this->app->bind(
      'Gis\Models\Services\UserServiceInterface',
      'Gis\Models\Services\UserService'
    );

    $this->app->bind(
      'Gis\Models\Services\SecurityServiceInterface',
      'Gis\Models\Services\SecurityService'
    );

    $this->app->bind(
      'Gis\Models\Services\FertilityMapServiceInterface',
      'Gis\Models\Services\FertilityMapService'
    );

      $this->app->bind(
      'Gis\Models\Services\GeometryServiceInterface',
      'Gis\Models\Services\GeometryService'
    );

      $this->app->bind(
      		'Gis\Models\Services\FertilizerServiceInterface',
      		'Gis\Models\Services\FertilizerService'
      );

      $this->app->bind(
      		'Gis\Models\Services\CropServiceInterface',
      		'Gis\Models\Services\CropService'
      );


    $this->app->bind(
      'Gis\Models\Services\FooterServiceInterface',
      'Gis\Models\Services\FooterService'
    );

    $this->app->bind(
      'Gis\Models\Repositories\HelpLinkRepository',
      'Gis\Models\Repositories\HelpLinkRepositoryEloquent'
    );

      $this->app->bind(
          'Gis\Models\Services\HelpLinkServiceInterface',
          'Gis\Models\Services\HelpLinkService'
      );

      $this->app->bind(
          'Gis\Models\Repositories\FooterRepository',
          'Gis\Models\Repositories\FooterRepositoryEloquent'
      );

      $this->app->bind(
          'Gis\Models\Repositories\FertilizerMapPaymentRepository',
          'Gis\Models\Repositories\FertilizerMapPaymentRepositoryEloquent'
      );

      $this->app->bind(
          'Gis\Models\Repositories\FertilizerMapInfoRepository',
          'Gis\Models\Repositories\FertilizerMapInfoRepositoryEloquent'
      );

      $this->app->bind(
          'Gis\Models\Repositories\FertilizerUnitPriceRepository',
          'Gis\Models\Repositories\FertilizerUnitPriceRepositoryEloquent'
      );

    $this->app->bind(
      'Gis\Models\Repositories\FertilityMapRepository',
      'Gis\Models\Repositories\FertilityMapRepositoryEloquent'
    );

    $this->app->bind(
          'Gis\Models\Repositories\FertilizerMapRepository',
          'Gis\Models\Repositories\FertilizerMapRepositoryEloquent'
        );

    $this->app->bind(
      'Gis\Models\Repositories\FertilizerMapInfoRepository',
      'Gis\Models\Repositories\FertilizerMapInfoRepositoryEloquent'
    );

    $this->app->bind(
    		'Gis\Models\Services\FolderServiceInterface',
    		'Gis\Models\Services\FolderService'
    );

      $this->app->bind(
          'Gis\Models\Repositories\FertilityMapRepository',
          'Gis\Models\Repositories\FertilityMapRepositoryEloquent'
      );
      $this->app->bind(
          'Gis\Models\Repositories\PostRepository',
          'Gis\Models\Repositories\PostRepositoryEloquent'
      );

    $this->app->bind(
    		'Gis\Models\Repositories\FolderRepository',
    		'Gis\Models\Repositories\FolderRepositoryEloquent'
    );
    
    $this->app->bind(
      'Gis\Models\Services\MapServiceInterface',
      'Gis\Models\Services\MapService'
    );

      $this->app->bind(
          'Gis\Models\Services\FertilizationPriceServiceInterface',
          'Gis\Models\Services\FertilizationPriceService'
      );

      $this->app->bind(
          'Gis\Models\Repositories\FertilizationPriceRepository',
          'Gis\Models\Repositories\FertilizationPriceRepositoryEloquent'
      );

      $this->app->bind(
          'Gis\Models\Services\OrganicMatterServiceInterface',
          'Gis\Models\Services\OrganicMatterService'
      );

      $this->app->bind(
          'Gis\Models\Repositories\HojoByproductRepository',
          'Gis\Models\Repositories\HojoByproductRepositoryEloquent'
      );

      $this->app->bind(
          'Gis\Models\Repositories\GreenManuresRepository',
          'Gis\Models\Repositories\GreenManuresRepositoryEloquent'
      );

      $this->app->bind(
          'Gis\Models\Repositories\FertilizerMapPropertyRepository',
          'Gis\Models\Repositories\FertilizerMapPropertyRepositoryEloquent'
      );

      $this->app->bind(
          'Gis\Models\Repositories\FertilizerEfficiencyOfCompostRepository',
          'Gis\Models\Repositories\FertilizerEfficiencyOfCompostRepositoryEloquent'
      );

      $this->app->bind(
          'Gis\Models\Repositories\CompostStandardDryMattersRepository',
          'Gis\Models\Repositories\CompostStandardDryMattersRepositoryEloquent'
      );

      $this->app->bind(
          'Gis\Models\Repositories\FertilityMapSelectionRepository',
          'Gis\Models\Repositories\FertilityMapSelectionRepositoryEloquent'
      );

      $this->app->bind(
          'Gis\Models\Repositories\FertilityMapSelectionInfoRepository',
          'Gis\Models\Repositories\FertilityMapSelectionInfoRepositoryEloquent'
      );

  }
}
