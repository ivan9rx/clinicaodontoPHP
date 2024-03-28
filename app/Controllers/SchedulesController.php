<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\libraries\CalendarService;
use App\Libraries\DentistaAvaiableHoursService;
use App\libraries\ScheduleService;
use CodeIgniter\Config\Factories;
use CodeIgniter\HTTP\ResponseInterface;

class SchedulesController extends BaseController
{

    /**
     * @var $ScheduleService
     */
    private ScheduleService $scheduleService;

    /**
     * @var CalendarService 
     * 
     */

    private CalendarService $calendarService;

    /**Construtor */

    public function __construct()
    {
        $this->scheduleService = Factories::class(ScheduleService::class);
        $this->calendarService = Factories::class(CalendarService::class);
    }

    public function index(): string
    {
        $data = [
            'title' => 'Criar Agendamento',
            'dentistas' => $this->scheduleService->renderDentistas(),
            'months' => $this->calendarService->renderMonths()
        ];

        return view('Front/Schedules/index', $data);
    }

    /**
     * recupera os serviços do profissional informado no request
     * @return ResponseInterface
     */
    public function dentistaServices()
    {

        try {
            $this->checkMethod('ajax');
            $dentistaId = (int) $this->request->getGet('dentista_id');
            $services = $this->scheduleService->renderDentistasServices(dentistaId: $dentistaId);
            return $this->response->setJSON([
                'services' => $services
            ]);
        } catch (\Throwable $th) {
            log_message('error', '[ERROR] {exception}', ['exception' => $th]);
            $this->response->setStatusCode(500);
        }
    }


    /**
     * recupera o calendario para o mês desejado
     * @return ResponseInterface
     */
    public function getCalendar()
    {

        try {
            $this->checkMethod('ajax');
            $month = (int) $this->request->getGet('month');
            return $this->response->setJSON([
                'calendar' => $this->calendarService->generate(month:$month)
            ]);
        } catch (\Throwable $th) {
            log_message('error', '[ERROR] {exception}', ['exception' => $th]);
            $this->response->setStatusCode(500);
        }
    }

    
    /**
     * recupera os horários disponiveis
     * @return ResponseInterface
     */
    public function getHours()
    {

        try {
            $this->checkMethod('ajax');
            return $this->response->setJSON([
                'hours' => Factories::class(DentistaAvaiableHoursService::class)->renderHours($this->request->getGet())
                
            ]);
        } catch (\Throwable $th) {
            log_message('error', '[ERROR] {exception}', ['exception' => $th]);
            $this->response->setStatusCode(500);
        }
    }
}
