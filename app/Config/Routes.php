<?php

use App\Controllers\HomeController as WebController;
use App\Controllers\SchedulesController;
use CodeIgniter\Router\RouteCollection;

use App\Controllers\Super\HomeController;
use App\Controllers\Super\DentistasController;
use App\Controllers\Super\DentistasServicesController;
use App\Controllers\Super\ServicesController;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', [WebController::class, 'index'], ['as' => 'home']);



$routes->group('super', static function ($routes) {

    $routes->get('/', [HomeController::class, 'index'], ['as' => 'super.home']);

    /**rotas de dentistas */
    $routes->group('dentistas', static function ($routes) {
        $routes->get('/', [DentistasController::class, 'index'], ['as' => 'dentistas']);
        $routes->get('new', [DentistasController::class, 'new'], ['as' => 'dentistas.new']);
        $routes->get('edit/(:num)', [DentistasController::class, 'edit/$1'], ['as' => 'dentistas.edit']);
        $routes->post('create', [DentistasController::class, 'create'], ['as' => 'dentistas.create']);
        $routes->put('update/(:num)', [DentistasController::class, 'update/$1'], ['as' => 'dentistas.update']);
        $routes->put('action/(:num)', [DentistasController::class, 'action/$1'], ['as' => 'dentistas.action']); //aiva destiva um registro
        $routes->delete('destroy/(:num)', [DentistasController::class, 'destroy/$1'], ['as' => 'dentistas.destroy']); //exclui um registro

        //rotas dos serviços do dentista
        $routes->get('services/(:num)', [DentistasServicesController::class, 'services/$1'], ['as' => 'dentistas.services']);
        $routes->put('services/store/(:num)', [DentistasServicesController::class, 'store/$1'], ['as' => 'dentistas.services.store']);
    });

    /**rota de serviços */
    $routes->group('services', static function ($routes) {
        $routes->get('/', [ServicesController::class, 'index'], ['as' => 'services']);
        $routes->get('new', [ServicesController::class, 'new'], ['as' => 'services.new']);
        $routes->get('edit/(:num)', [ServicesController::class, 'edit/$1'], ['as' => 'services.edit']);
        $routes->post('create', [ServicesController::class, 'create'], ['as' => 'services.create']);
        $routes->put('update/(:num)', [ServicesController::class, 'update/$1'], ['as' => 'services.update']);
        $routes->put('action/(:num)', [ServicesController::class, 'action/$1'], ['as' => 'services.action']); //aiva destiva um registro
        $routes->delete('destroy/(:num)', [ServicesController::class, 'destroy/$1'], ['as' => 'services.destroy']); //exclui um registro
    });


});


$routes->group('schedules', static function ($routes) {
    $routes->get('/', [SchedulesController::class, 'index'], ['as' => 'schedules.new']);
    $routes->get('services', [SchedulesController::class, 'dentistaServices'], ['as' => 'get.dentista.servicos']);
    $routes->get('calendar', [SchedulesController::class, 'getCalendar'], ['as' => 'get.calendar']); //recuperamos via fetch API o calendário para o mês desejado
    $routes->get('hours', [SchedulesController::class, 'getHours'], ['as' => 'get.hours']);//recuperamos via fetch API os horários disponíveis para o dia desejado
   
});

