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
            $f = new TEntry('asd');
            $f->onchange = "changemaps(this);";
            $f->setValue(1700);
            $f->show();

            $maps = new Maps();
            $maps->setLimit(1700);

            $store_id = $_GET['store_id'];

            $current_purshases = Purshase::getCurrentByStore($store_id, TSession::getValue('fb-id'));

            if($current_purshases)
            {
                foreach($current_purshases as $purshase)
                {
                    $location = json_decode($purshase->getLocation());

                    if($location)
                        $maps->addMark($location->lat, $location->lng, $purshase->id);
                }
            }
            //TPage::include_css('app/resources/styles.css');
            /*$html1 = new THtmlRenderer('app/resources/views/store-buy.html');
            
            // replace the main section variables
            $html1->enableSection('stores', $s, true);
            $html1->enableSection('main', array());
            $html1->show();*/
            
            $maps->show();

            TTransaction::close();
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage()."<br>Fazer alguma coisa");
            return false;
        }
    }
}