<?php

use PhpParser\Node\Stmt\Echo_;

echo $this->extend('Front/Layout/main'); ?>


<?php echo $this->section('title'); ?>

<?php echo $title ??  'Home'; ?>

<?php echo $this->endSection(); ?>


<?php echo $this->section('css'); ?>

<style>
    /** para deixar o botão do dia um pouco menor */
    .btn-calendar-day {
        max-width: 36px !important;
        min-width: 36px !important;
        line-height: 0px !important;
        padding: 10% !important;
        height: 30px !important;
        display: table-cell !important;
        vertical-align: middle !important;
    }

    .btn-calendar-day-chosen {
        color: #fff !important;
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }

    .btn-hour {
        margin-bottom: 10px !important;
        max-width: 55px !important;
        min-width: 55px !important;
        padding-left: 8px !important;
        line-height: 0px !important;
        height: 30px !important;
    }

    .btn-hour-chosen {
        color: #fff !important;
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }


    /** para centralizar o conteúdo dentro da célula do calendário */
    td {
        text-align: center;
        vertical-align: middle;
    }

    /** para aparecer os options dos dropdowns */
    .wizard .content .form-control {
        padding: .375rem 0.75rem !important;
    }
</style>

<?php echo $this->endSection(); ?>

<?php echo $this->section('content'); ?>

<div class="container pt-5">
    <h1 class="mt-5"><?php echo $title; ?></h1>


    <div id="boxErrors" class="mt-4 mb-3"></div>

    <div class="row">

        <div class="col-md-8">
            <div class="row">
                <!-- Profissionais-->
                <div class="col-md-12 mb-4">
                    <p class="lead">
                        Escolha um profissional
                    </p>
                    <?php echo $dentistas ?>
                </div>

                <!-- Serviço do profissional escolhido -->
                <div id="mainBoxServices" class="col-md-12 d-none mb-4">
                    <p class="lead">Escolha o serviço:</p>

                    <div id="boxServices">

                    </div>

                    <!--Mês(oculto na load da view) -->
                    <div id="boxMonths" class="col-md-12 d-none mb-4 mt-3">
                        <p class="lead">Escolha o mês:</p>

                        <?php echo $months ?>

                    </div>

                    <div id="mainBoxCalendar" class="col-md-12 d-none mb-4">

                        <p class="lead">Escolha o dia e horário:</p>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <div id="boxCalendar"></div>
                            </div>

                            <div class="col-md-6 form-group">
                                <div id="boxHours"></div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <!--preview do que for sendo escolhido -->
        <div class="col-md-2 ms-auto">

            <p class="lead mt-4">Profissional escolhido:<br><span id="chosenDentistaText" class="text-muted-small"></span></p>
            <p class="lead">Serviço escolhido:<br><span id="chosenServiceText" class="text-muted-small"></span></p>
            <p class="lead">Mês escolhido:<br><span id="chosenMonthText" class="text-muted-small"></span></p>
            <p class="lead">Dia escolhido:<br><span id="chosenDayText" class="text-muted-small"></span></p>
            <p class="lead">Horário escolhido:<br><span id="chosenHourText" class="text-muted-small"></span></p>


        </div>

    </div>

</div>


<?php echo $this->endSection(); ?>

<?php echo $this->section('js'); ?>

<script>
    const URL_GET_SERVICES = '<?php echo route_to('get.dentista.servicos') ?>'
    const URL_GET_CALENDAR = '<?php echo route_to('get.calendar') ?>'
    const URL_GET_HOURS = '<?php echo route_to('get.hours') ?>'


    const boxErrors = document.getElementById('boxErrors');

    const mainBoxServices = document.getElementById('mainBoxServices');
    const boxServices = document.getElementById('boxServices');
    const boxMonths = document.getElementById('boxMonths');
    const mainBoxCalendar = document.getElementById('mainBoxCalendar');
    const boxCalendar = document.getElementById('boxCalendar');
    const boxHours = document.getElementById('boxHours');







    //variáveis de escopo global que ultilizaremos na criação do agendamento
    let dentistaId = null;
    let serviceId = null;
    let chosenDay = null;
    let chosenHour = null;
    let chosenMonth = null;

    const dentistas = document.getElementsByName('dentista_id')
    //preview do que está sendo escolhido
    let chosenDentistaText = document.getElementById('chosenDentistaText');
    let chosenServiceText = document.getElementById('chosenServiceText');
    let chosenMonthText = document.getElementById('chosenMonthText');
    let chosenDayText = document.getElementById('chosenDayText');
    let chosenHourText = document.getElementById('chosenHourText');


    dentistas.forEach(element => {
        //adicionar para cada elemento um 'listener' ou um ouvinte
        element.addEventListener('click', (event) => {
            mainBoxServices.classList.remove('d-none');
            //atribuo à variável gllobal ao dentista clicado
            dentistaId = element.value;

            if (!dentistaId) {
                alert('Error ao determinar a unidade escolhida');
                return
            }

            chosenDentistaText.innerText = element.getAttribute('data-dentista');
            chosenHourText = '';


            getServices()
        })
    })


    //recupera os serviços do profissional
    const getServices = async () => {
        //box errors criar depois
        boxErrors.innerHTML = '';

        let url = URL_GET_SERVICES + '?' + setParameters({
            dentista_id: dentistaId
        })

        const response = await fetch(url, {
            method: 'get',
            headers: setHeadersRequest()
        });

        if (!response.ok) {
            boxErrors.innerHTML = showErrorMessage('Não foi possível recuperar os serviços');
            throw new Error(`HTTP error! Status: ${response.status}`);
            return
        }

        const data = await response.json()

        //colocamos na div os serviços devolvidos
        boxServices.innerHTML = data.services;
        const elementService = document.getElementById('service_id');
        elementService.addEventListener('change', (event) => {

            serviceId = elementService.value ?? null;
            let serviceName = serviceId !== '' ? elementService.options[event.target.selectedIndex].text : null;

            // CORRECTION: Assign the service name to the innerText property
            console.log('O serviço foi escolhido?', serviceId !== '');

            chosenServiceText.innerText = serviceName;


            serviceId !== '' ? boxMonths.classList.remove('d-none') : boxMonths.classList.add('d-none')

        });

    }

    document.getElementById('month').addEventListener('change', (event) => {
        //limpa o preview do mês escolhido a cada mudança
        chosenMonthText.innerText = '';


        /**
         * @todo
         */

        const month = event.target.value;

        if (!month) {
            /**
             * @todo criar função
             */

            //resetMonthDataVariables();

            //resetBoxCalendar();

            return;
        }

        //Mês valido escolhido


        //atribuimos a variavel de escopo global o valor do mês escolhido
        chosenMonth = event.target.value;

        chosenMonthText.innerText = event.target.options[event.target.selectedIndex].text;



        //finalmente buscamos o calendario para o mês escolhido

        getCalendar();

    })

    //calendario
    const getCalendar = async () => {
        //limpa os erros
        boxErrors.innerHTML = '';

        //limpa o preview do dia e da hora escolhidos, pois o user precisara clicar novamente;
        chosenDayText.innerText = '';
        chosenHourText.innerText = '';

        let url = URL_GET_CALENDAR + '?' + setParameters({
            month: chosenMonth
        })

        const response = await fetch(url, {
            'method': 'get',
            headers: setHeadersRequest()
        })

        if (!response.ok) {
            boxErrors.innerHTML = showErrorMessage('Não foi possível recuperar o calendário para o mês informado');
            throw new Error(`HTTP error! Status: ${response.status}`);
            return
        }

        //recuperamos a resposta
        const data = await response.json()

        //exibo a div do calendario e das horas
        mainBoxCalendar.classList.remove('d-none');

        //colocamos na div o calendario criado
        boxCalendar.innerHTML = data.calendar;

        //agora recuperamos o elemento que tenham a classe 'chosenday'

        const buttonsChosenDay = document.querySelectorAll('.chosenDay');

        buttonsChosenDay.forEach(element => {
            //fico 'estutando' o click no elemento
            //a cada click recupero o valor de 'data-day'

            element.addEventListener('click', (event) => {
                //limpa o preview da hora
                chosenDayText.innerText = '';

                //redefina para null, para garantir
                chosenHour = null;

                /**
                 * @todo criar função para remover a classe dos botões clicados
                 */

                event.target.classList.add('btn-calendar-day-chosen')

                chosenDay = event.target.dataset.day;

                //dia escolhido no preview
                chosenDayText.innerText = chosenDay;

                console.log(event.target.dataset.day)

                getHours();


            })
        })

    }

    const getHours = async () => {
        boxErrors.innerHTML = '';

        //o profissional realmente foi escolhido?
        if (!dentistaId) {
            boxErrors.innerHTML = showErrorMessage('Você precisa escolher a unidade de atendimento');
            return;
        }

        let url = URL_GET_HOURS + '?' + setParameters({
            dentista_id: dentistaId,
            month: chosenMonth,
            day: chosenDay
        });

        const response = await fetch(url, {
            method: 'get',
            headers: setHeadersRequest()
        });

        if (!response.ok) {
            boxErrors.innerHTML = showErrorMessage('Não foi possível recuperar os horários disponiveis');
            throw new Error(`HTTP error! Status: ${response.status}`);
            return
        }

        const data = await response.json();

        //recupero as horas
        const hours = data.hours;

        if (hours === null) {
            boxHours.innerHTML = showErrorMessage(`Não há horários disponiveis para o dia ${chosenDay}`);
            chosenDay = null;
            return;
        }

        //colocamos na div as horas
        boxHours.innerHTML = hours;
    }
</script>

<?php echo $this->endSection(); ?>