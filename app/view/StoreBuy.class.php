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
        parent::__construct();
 
        require_once('app/templates/theme1/libraries.html');
        TTransaction::open('ship');
        
        $store_id = $_GET['store_id'];

        //TPage::include_css('app/resources/styles.css');
        /*$html1 = new THtmlRenderer('app/resources/views/store-buy.html');
        
        // replace the main section variables
        $html1->enableSection('stores', $s, true);
        $html1->enableSection('main', array());
        $html1->show();*/

        $maps = new Maps();
        $maps->show();

        TTransaction::close();
    }
}