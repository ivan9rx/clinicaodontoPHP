<?php

namespace App\libraries;

use App\Models\DentistaModel;
use App\Models\ServiceModel;
use Exception;
use InvalidArgumentException;

class ScheduleService
{


    /**
     * renderiza a lista com  as opções de serviços disponíveis para associação através de checkbox
     * @return string
     */
    public function renderDentistas(): string
    {

        //dentistas ativos e com serviços associados

        $where = [
            'active' => 1,
            'servicos !=' => ''
        ];

        $dentistas = model(DentistaModel::class)->where($where)->orderBy('nome', 'ASC')->findAll();

        if (empty($dentistas)) {


            return '<div class="text-info mt-5" >Não há dentistas para agendamento disponíveis</div>';
        }

        //valor padrão
        $radios = '';

        foreach ($dentistas as $dentista) {

            $radios .= '<div class="form-check mb-2">';
            $radios .= "<input type='radio' name='dentista_id' data-dentista='{$dentista->nome} \nEndereço: {$dentista->endereco}' value='{$dentista->id}' class='form-check-input' id='radio-dentista-{$dentista->id}'>";
            $radios .= "<label class='form-check-label' for='radio-dentista-{$dentista->id}'>{$dentista->nome}<br>{$dentista->endereco}</label>";
            $radios .= '</div>';
        }


        // retorna a lista de opções
        return $radios;
    }

/**
 * recupera os serviços associados ao profissional informado como um dropdownHTML
 * @param integer
 * @return string
 */

    public function renderDentistasServices(int $dentistaId):string{
        //validamos a existência do profissional ativo com serviços
        $dentista = model(DentistaModel::class)->where(['active'=>1, 'servicos !=' =>null, 'servicos !='=> ''])->findOrFail($dentistaId);

        $services = model(ServiceModel::class)->whereIn('id', $dentista->servicos)->where('active',1)->orderBy('nome', 'ASC')->findAll();

        if(empty($services)){
            throw new InvalidArgumentException("Os serviços associados ao profissional {$dentista->nome} não estão ativos ou não existem");
        }

        $options = [];
        $options[null]= '---Escolha---';

        foreach($services as $service){
            $options[$service->id] = $service->nome;
        }

        return form_dropdown(data:'service', options:$options, selected:[], extra:['id'=>'service_id','class'=>'form-select']);

    }
}
