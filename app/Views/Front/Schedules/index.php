<?php

use PhpParser\Node\Stmt\Echo_;

echo $this->extend('Front/Layout/main'); ?>


<?php echo $this->section('title'); ?>

<?php echo $title ??  'Home'; ?>

<?php echo $this->endSection(); ?>


<?php echo $this->section('css'); ?>


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

                <!--serviços do profissional(oculto na load da view) -->
                <div id="mainBoxServices" class="col-md-12 d-none mb-4">
                    <p class="lead">Escolha o serviço:</p>

                    <div id="boxServices">

                    </div>

                </div>
            </div>
        </div>

        <!--preview do que for sendo escolhido -->
        <div class="col-md-2 ms-auto">

            <p class="lead mt-4">Profissional escolhido:<br><span id="chosenDentistaText" class="text-muted-small"></span></p>
            <p class="lead">Serviço escolhido<br><span id="chosenServiceText" class="text-muted-small"></span></p>
            <p class="lead">Mês escolhido<br><span id="chosenMonthText" class="text-muted-small"></span></p>
            <p class="lead">Dia escolhido<br><span id="chosenDayText" class="text-muted-small"></span></p>
            <p class="lead">Horário escolhido<br><span id="chosenHourText" class="text-muted-small"></span></p>


        </div>

    </div>

</div>


<?php echo $this->endSection(); ?>

<?php echo $this->section('js'); ?>

<script>
    const URL_GET_SERVICES = '<?php echo route_to('get.dentista.servicos') ?>'

    const boxErrors = document.getElementById('boxErrors');

    const mainBoxServices = document.getElementById('mainBoxServices')
    const boxServices = document.getElementById('boxServices')



    //variáveis de escopo global que ultilizaremos na criação do agendamento
    let dentistaId = null;
    let serviceId = null;
    let chosenDay = null;
    let chosenHour = null;

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
            chosenServiceText = '';
            chosenMonthText = '';
            chosenDayText = '';
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
        elementService.addEventListener('change', (event)=>{
            
            serviceId = elementService.value ?? null;
            let serviceName = serviceId !== null ? elementService.options[event.target.selectedIndex].text:null;
            console.log(serviceName);

        });

    }
</script>

<?php echo $this->endSection(); ?>