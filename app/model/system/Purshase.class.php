<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class Purshase extends TRecord
{
    const TABLENAME = 'purshase';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('store_id');
        parent::addAttribute('people_id');
        parent::addAttribute('status_id');
        parent::addAttribute('min_people');
        parent::addAttribute('max_people');
        parent::addAttribute('date_until');
        parent::addAttribute('deposite_information');
        parent::addAttribute('track_link');
        parent::addAttribute('maps_address');
    }
}
?>