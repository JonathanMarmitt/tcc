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
            $count = "<font color='".$purshase->getColor()."'>".$purshase->getCurrentPeople().' / '.$purshase->max_people."</font>";

            $buttons = self::getActionButtons($purshase);

            $s = array('id'    => $purshase->id,
                       'description' => $purshase->store_id,
                       'status' => $purshase->status->description,
                       'store' => $purshase->store->description,
                       'count' => $count,
                       'deposit_count' => $purshase->getCurrentPeople(),
                       'deposit_done' => $purshase->getCountDeposited(),
                       'min' => $purshase->min_people,
                       'date_until' => TDate::date2br($purshase->date_until));

            ## tabela dos participantes da compra
            if($people = $purshase->getPeople())
            {
                $table_people = new TTable("table_people_{$purshase->id}");
                $table_people->class = 'table-people table table-condensed';

                foreach($people as $purshase_with)
                {
                    $btn_link = new TButton('link');
                    $btn_link->setImage('fa:link');
                    $btn_link->class = 'btn btn-primary';

                    $btn_receipt = new TButton('confirm_receipt');
                    $btn_receipt->setImage('fa:check');
                    $btn_receipt->class = 'btn btn-success';

                    $btn_delete = new TButton('exclude');
                    $btn_delete->setImage('fa:times');
                    $btn_delete->class = 'btn btn-danger';

                    $row = $table_people->addRow();

                    $row->addCell($btn_link);
                    $row->addCell($btn_receipt);
                    $row->addCell($btn_delete);
                    $row->addCell($purshase_with->people->name)->style = "width: 100%";
                }
            }
            else
                $table_people = "";

            $fields = new THtmlRenderer("app/resources/views/my-purshase-status-{$purshase->status_id}.html");
            $fields->enableSection('main', $s);

            $r[] = array('fields' => $fields->getContents(),
                         'buttons' => $buttons,
                         'table_people' => $table_people,
                         'id' => $purshase->id);
        }

        //TPage::include_css('app/resources/styles.css');
        $html = new THtmlRenderer('app/resources/views/my-purshases.html');
        
        // replace the main section variables
        $html->enableSection('stores', $r, true);
        $html->enableSection('main', array());
        $html->show();

        ##################################################################################

        $l = new TLabel('Compras que participo:');
        $l->style = 'font-size: 30px';
        $l->show();

        ##################################################################################

        $purshases = PurshaseWith::getMyActivePurshases();

        unset($r);
        foreach($purshases as $purshase)
        {
            $current = $purshase->getCurrentPeople();
            $count = "<font color='".$purshase->getColor($current)."'>".$current.' / '.$purshase->max_people."</font>";

            $buttons = self::getActionButtonsPurshaseWith($purshase);

            $s = array('id'    => $purshase->id, 
                       'description' => $purshase->store_id,
                       'status' => $purshase->status->description,
                       'store' => $purshase->store->description,
                       'count' => $count,
                       'min' => $purshase->min_people,
                       'date_until' => TDate::date2br($purshase->date_until));

            $fields = new THtmlRenderer("app/resources/views/my-purshase-status-{$purshase->status_id}.html");
            $fields->enableSection('main', $s);

            $r[] = array('fields' => $fields->getContents(), 'buttons' => $buttons);
        }

        //TPage::include_css('app/resources/styles.css');
        $html1 = new THtmlRenderer('app/resources/views/my-purshases.html');
        
        // replace the main section variables
        $html1->enableSection('stores', $r, true);
        $html1->enableSection('main', array());
        $html1->show();

        TTransaction::close();
    }


    ##Action buttons
    public static function getActionButtons(Purshase $purshase, $html_return = true)
    {
        $btn_status = [1 => ['cancel','progress','date','people'],
                       2 => ['cancel','progress','people'],
                       3 => ['cancel','track'],
                       4 => [],
                       5 => [],
                       6 => [],
                       7 => [],
                       8 => [],
                       9 => []];
        $btn = array();

        $btn['cancel'] = new TButton('cancel');
        $btn['cancel']->class = 'btn btn-danger';
        $btn['cancel']->title = 'Cancelar';
        $btn['cancel']->addFunction("__adianti_post_data('form', 'class=PurshaseControl&method=onCancel&static=1&id={$purshase->id}');");
        $btn['cancel']->setImage('fa:ban');

        $btn['progress'] = new TButton('progress');
        $btn['progress']->class = 'btn btn-success';
        $btn['progress']->title = 'Prosseguir';
        $btn['progress']->addFunction("__adianti_post_data('form', 'class=PurshaseControl&method=onProgress&static=1&id={$purshase->id}');");
        $btn['progress']->setImage('fa:arrow-right');

        $btn['date'] = new TButton('date_until');
        $btn['date']->class = 'btn btn-primary';
        $btn['date']->title = 'Alterar data';
        $btn['date']->addFunction("customQuestion('Alterar Data Até', 'date', '{$purshase->date_until}', 'class=PurshaseControl&method=editDate&id={$purshase->id}&static=1')");
        $btn['date']->setImage('fa:calendar');

        if($purshase->getCurrentPeople() > 0)
        {
            $btn['people'] = new TButton('people');
            $btn['people']->class = 'btn btn-primary';
            $btn['people']->title = 'Ver participantes';
            //$btn['people']->addFunction("Adianti.waitMessage = 'Carregando pessoas...';__adianti_post_data('form', 'class=PurshaseControl&method=onPeopleWith&static=1&id={$purshase->id}');");
            $btn['people']->addFunction("\$('.people_{$purshase->id}').slideToggle('slow')");
            $btn['people']->setImage('fa:arrow-down');
        }

        //$btn['receipt'] = new TButton('receipt');
        //$btn['receipt']->class = 'btn btn-primary';
        //$btn['receipt']->title = 'Depósitos recebidos';
        //$btn['receipt']->setImage('fa:users');

        $btn['track'] = new TButton('track');
        $btn['track']->class = 'btn btn-success';
        $btn['track']->title = 'Adicionar rastreio';
        $btn['track']->setImage('fa:map');

        $html = "";
        $buttons = array();

        foreach($btn_status[$purshase->status_id] as $status)
        {    
            if(isset($btn[$status]))
            {
                $html .= $btn[$status]->getHtml();
                $buttons[] = $btn[$status];   
            }
        }

        return $html_return ? $html : $buttons;
    }

    public static function getActionButtonsPurshaseWith(Purshase $purshase, $html_return = true)
    {
        $btn_status = [1 => ['leave'],
                       2 => ['leave','receipt'],
                       3 => ['leave','track'],
                       4 => [],
                       5 => [],
                       6 => [],
                       7 => [],
                       8 => [],
                       9 => []];
        $btn = array();

        /* criar botoes:
        1: alterar participantes*/

        $btn['leave'] = new TButton('leave');
        $btn['leave']->class = 'btn btn-danger';
        $btn['leave']->title = 'Deixar de participar';
        $btn['leave']->setImage('fa:ban');

        $btn['receipt'] = new TButton('receipt');
        $btn['receipt']->class = 'btn btn-success';
        $btn['receipt']->title = 'Adicionar comprovante de depósito';
        $btn['receipt']->setImage('fa:arrow-right');

        $btn['track'] = new TButton('track');
        $btn['track']->class = 'btn btn-success';
        $btn['track']->title = "Acessar rastreio";
        $btn['track']->setImage('fa:map');

        $btn['people'] = new TButton('people');
        $btn['people']->class = 'btn btn-primary';
        $btn['people']->title = 'Participantes';
        $btn['people']->setImage('fa:users');

        $html = "";
        $buttons = array();

        foreach($btn_status[$purshase->status_id] as $status)
        {    
            $html .= $btn[$status]->getHtml();
            $buttons[] = $btn[$status];   
        }

        return $html_return ? $html : $buttons;
    }
}