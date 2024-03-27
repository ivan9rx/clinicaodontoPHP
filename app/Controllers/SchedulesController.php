<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\libraries\ScheduleService;
use CodeIgniter\Config\Factories;
use CodeIgniter\HTTP\ResponseInterface;

class SchedulesController extends BaseController
{

    /**
     * @var $ScheduleService
     */
    private ScheduleService $scheduleService;

    /**Construtor */

    public function __construct()
    {
        $this->scheduleService = Factories::class(ScheduleService::class);
    }

    public function index(): string
    {
        $data = [
            'title' => 'Criar Agendamento',
            'dentistas' => $this->scheduleService->renderDentistas()
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
            $services = $this->scheduleService->renderDentistasServices(dentistaId:$dentistaId);
            return $this->response->setJSON([
                'services' => $services
            ]);
        } catch (\Throwable $th) {
            log_message('error', '[ERROR] {exception}', ['exception' => $th]);
            $this->response->setStatusCode(500);
        }
    }
}
