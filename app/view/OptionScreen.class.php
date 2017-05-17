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
class OptionScreen extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();

        TScript::create("setGeolocation()");
 
        require_once('app/templates/theme1/libraries.html');

        //TSession::setValue('fb-id', 1385233441523198);

        //TPage::include_css('app/resources/styles.css');
        $html1 = new THtmlRenderer('app/resources/views/option-screen.html');
        
        // replace the main section variables
        $html1->enableSection('main', array());
        $html1->show();
    }
}