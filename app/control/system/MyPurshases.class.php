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

        //$criteria = new TCriteria;
        //$criteria->setProperty('order','id');
        //$criteria->setProperty('direction','asc');

        $purshases = Purshase::getMyActivePurshases();

        foreach($purshases as $purshase)
        {
            $current = $purshase->getCurrentPeople();
            $count = "<font color='".$purshase->getColor($current)."'>".$current.' / '.$purshase->max_people."</font>";

            $btn_cancel = new TButton('cancel');
            $btn_cancel->class = 'btn btn-danger';
            $btn_cancel->title = 'Cancelar';
            $btn_cancel->setImage('fa:ban');

            $buttons = $btn_cancel->getHtml();
            switch ($purshase->status_id)
            {
                case 1:
                    $btn_finalize = new TButton('finalize');
                    $btn_finalize->class = 'btn btn-success';
                    ##$btn_finalize->setLabel('Finalizar');
                    $btn_finalize->setImage('fa:arrow-right');

                    $buttons .= $btn_finalize->getHtml();

                    //$btn_track = new TButton('track');
                    //$btn_track->class = 'btn btn-success';
                    ##$btn_track->setLabel('Rastreio');
                    //$btn_track->setImage('fa:map');

                    //$buttons .= $btn_track->getHtml();

                    $btn_people = new TButton('people');
                    $btn_people->class = 'btn btn-primary';
                    ##$btn_people->setLabel('Participantes');
                    $btn_people->setImage('fa:users');

                    $buttons .= $btn_people->getHtml();
                    break;
            }

            $s[] = array('store_id'    => $purshase->id, 
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

        TTransaction::close();
    }
}