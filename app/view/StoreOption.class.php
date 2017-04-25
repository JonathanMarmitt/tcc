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
class StoreOption extends TPage
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

        $criteria = new TCriteria;
        $criteria->setProperty('order','id');
        $criteria->setProperty('direction','asc');

        $stores = Store::getObjects($criteria);

        foreach($stores as $store)
        {
            $s[] = array('store_id'    => $store->id, 
                         'description' => $store->description,
                         'store_img'   => "app/store_banner/".$store->id);
        }

        //TPage::include_css('app/resources/styles.css');
        $html1 = new THtmlRenderer('app/resources/views/store-option.html');
        
        // replace the main section variables
        $html1->enableSection('stores', $s, true);
        $html1->enableSection('main', array());
        $html1->show();

        TTransaction::close();
    }
}