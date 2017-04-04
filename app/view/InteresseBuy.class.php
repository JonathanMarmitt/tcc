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
class InteresseBuy extends TPage
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
        $people = new People(TSession::getValue('fb-id'));
        $likes = $people->getLikesToList();
        TTransaction::close();

        $replaces = array();
        $interests = array();

        foreach($likes as $like)
        {
            $interests[] = array('interest' => $like->page_name,
                                      'src' => $like->page_picture);
        }
        
        $html = new THtmlRenderer('app/resources/views/interesse-buy.html');
        
        // replace the main section variables
        $html->enableSection('interests', $interests, true);
        $html->enableSection('main', $replaces);
        $html->show();

        $maps = new Maps();
        $maps->show();
        
    }
}