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

            $buttons = self::getActionButtons($purshase);

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

            $buttons = self::getActionButtonsPurshaseWith($purshase);

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


    ##Action buttons
    public static function getActionButtons(Purshase $purshase, $html_return = true)
    {
        $btn_status = [1 => ['cancel','progress','date','people'],
                       2 => ['cancel','progress'],
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

        $btn['people'] = new TButton('people');
        $btn['people']->class = 'btn btn-primary';
        $btn['people']->title = 'Ver participantes';
        $btn['people']->setImage('fa:users');

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
            $html .= $btn[$status]->getHtml();
            $buttons[] = $btn[$status];   
        }

        return $html_return ? $html : $buttons;
    }

    public static function getActionButtonsPurshaseWith(Purshase $purshase, $html_return = true)
    {
        $btn_status = [1 => ['leave','people'],
                       2 => ['leave','people','receipt'],
                       3 => ['leave','people','track'],
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