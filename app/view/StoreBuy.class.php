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
class StoreBuy extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {   
        try
        {
            parent::__construct();
     
            require_once('app/templates/theme1/libraries.html');
            TTransaction::open('ship');
            
            //$slider = new TSlider('min_people');
            //$slider->setRange(1000,5000,100);
            //$slider->setSize('100%');
            //$slider->onchange = "changemaps(this);";
            //$slider->show();
            $maps = new Maps();

            $val = isset($_GET['val']) ? $_GET['val'] : $maps->getLimit();

            $maps->setLimit($val);

            if(!isset($_GET['val']))
            {
                $d = new TEntry('distance');
                $d->setValue($maps->getLimit());
                $d->setSize(65);
                $d->show();

                $l = new TLabel('metros');
                $l->style = 'margin-left:5px;';
                $l->show();

                $btn = new TButton('distance_button');
                $btn->onclick = "changemaps();";
                $btn->setLabel('Filtrar distância');
                $btn->class = 'btn btn-sm btn-primary';
                $btn->style = 'margin-left: 10px;margin-bottom:6px;';
                $btn->show();
            }

            $store_id = isset($_GET['store_id']) ? $_GET['store_id'] : null;

            $current_purshases = Purshase::getCurrentByStore($store_id, TSession::getValue('fb-id'));

            if($current_purshases)
            {
                foreach($current_purshases as $purshase)
                {
                    if($purshase->max_people >= $purshase->getCurrentPeople())
                    {
                        $location = json_decode($purshase->getLocation());

                        if($location)
                            $maps->addMark($location->lat, $location->lng, $purshase->id);
                    }
                }
            }
            
            $maps->show();

            TTransaction::close();

            TScript::create("$(document).ready(function(){__adianti_unblock_ui();})");
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage()."<br>Fazer alguma coisa");
            return false;
        }
    }

    public function refresh()
    {
        //funcoes de ajax requerem metodo...
    }
}