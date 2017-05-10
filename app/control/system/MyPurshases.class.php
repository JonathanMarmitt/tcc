<?php
/**
 * WelcomeView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class MyPurshases extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
 
        require_once('app/templates/theme1/libraries.html');
        TTransaction::open('ship');

        $purshases = Purshase::getMyActivePurshases();

        foreach($purshases as $purshase)
        {
            $current = $purshase->getCurrentPeople();
            $count = "<font color='".$purshase->getColor($current)."'>".$current.' / '.$purshase->max_people."</font>";

            $btn_cancel = new TButton('cancel');
            $btn_cancel->class = 'btn btn-danger';
            $btn_cancel->title = 'Cancelar';
            $btn_cancel->addFunction("__adianti_post_data('form', 'class=PurshaseControl&method=onCancel&static=1&id={$purshase->id}');");
            $btn_cancel->setImage('fa:ban');

            $buttons = $btn_cancel->getHtml();
            switch ($purshase->status_id)
            {
                case 1:
                    $btn_finalize = new TButton('finalize');
                    $btn_finalize->class = 'btn btn-success';
                    $btn_finalize->title = 'Finalizar compra';
                    $btn_finalize->setImage('fa:arrow-right');
                    $buttons .= $btn_finalize->getHtml();

                    $btn_date = new TButton('date_until');
                    $btn_date->class = 'btn btn-primary';
                    $btn_date->title = 'Alterar data';
                    $btn_date->addFunction("customQuestion('Alterar Data Até', 'date', '{$purshase->date_until}', 'class=PurshaseControl&method=editDate&id={$purshase->id}&static=1')");
                    $btn_date->setImage('fa:calendar');
                    $buttons .= $btn_date->getHtml();

                    $btn_people = new TButton('people');
                    $btn_people->class = 'btn btn-primary';
                    $btn_people->title = 'Ver participantes';
                    $btn_people->setImage('fa:users');
                    $buttons .= $btn_people->getHtml();

                    break;
                case 2:
                    $btn_people = new TButton('receipt');
                    $btn_people->class = 'btn btn-primary';
                    $btn_people->title = 'Depósitos recebidos';
                    $btn_people->setImage('fa:users');
                    $buttons .= $btn_people->getHtml();

                    break;
                case 3:

                    $btn_track = new TButton('track');
                    $btn_track->class = 'btn btn-success';
                    $btn_track->title = 'Adicionar rastreio';
                    $btn_track->setImage('fa:map');
                    $buttons .= $btn_track->getHtml();

                    break;
            }

            $s[] = array('id'    => $purshase->id, 
                         'description' => $purshase->store_id,
                         'status' => $purshase->status->description,
                         'store' => $purshase->store->description,
                         'count' => $count,
                         'min' => $purshase->min_people,
                         'date_until' => TDate::date2br($purshase->date_until),
                         'buttons' => $buttons);
        }

        //TPage::include_css('app/resources/styles.css');
        $html1 = new THtmlRenderer('app/resources/views/my-purshases.html');
        
        // replace the main section variables
        $html1->enableSection('stores', $s, true);
        $html1->enableSection('main', array());
        $html1->show();

        ##################################################################################

        $l = new TLabel('Compras que participo:');
        $l->style = 'font-size: 30px';
        $l->show();

        ##################################################################################

        $purshases = PurshaseWith::getMyActivePurshases();

        foreach($purshases as $purshase)
        {
            $current = $purshase->getCurrentPeople();
            $count = "<font color='".$purshase->getColor($current)."'>".$current.' / '.$purshase->max_people."</font>";

            $btn_cancel = new TButton('cancel');
            $btn_cancel->class = 'btn btn-danger';
            $btn_cancel->title = 'Deixar de participar';
            $btn_cancel->setImage('fa:ban');

            $buttons = $btn_cancel->getHtml();
            switch ($purshase->status_id)
            {
                case 1:
                    $btn_receipt = new TButton('receipt');
                    $btn_receipt->class = 'btn btn-success';
                    $btn_receipt->title = 'Adicionar comprovante de depósito';
                    $btn_receipt->setImage('fa:arrow-right');

                    $buttons .= $btn_receipt->getHtml();

                    $btn_track = new TButton('track');
                    $btn_track->class = 'btn btn-success';
                    $btn_track->title = "Acessar rastreio";
                    $btn_track->setImage('fa:map');

                    $buttons .= $btn_track->getHtml();

                    $btn_people = new TButton('people');
                    $btn_people->class = 'btn btn-primary';
                    $btn_people->title = 'Participantes';
                    $btn_people->setImage('fa:users');

                    $buttons .= $btn_people->getHtml();

                    $btn_people = new TButton('people');
                    $btn_people->class = 'btn btn-primary';
                    $btn_people->title = 'Participantes';
                    $btn_people->setImage('fa:users');

                    $buttons .= $btn_people->getHtml();
                    break;
            }

            $sw[] = array('id' => $purshase->id, 
                         'description' => $purshase->store_id,
                         'status' => $purshase->status->description,
                         'store' => $purshase->store->description,
                         'count' => $count,
                         'min' => $purshase->min_people,
                         'date_until' => TDate::date2br($purshase->date_until),
                         'buttons' => $buttons);
        }

        //TPage::include_css('app/resources/styles.css');
        $html1 = new THtmlRenderer('app/resources/views/my-purshases.html');
        
        // replace the main section variables
        $html1->enableSection('stores', $sw, true);
        $html1->enableSection('main', array());
        $html1->show();

        TTransaction::close();
    }
}