<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class Store extends TRecord
{
    const TABLENAME = 'store';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('description');
        parent::addAttribute('link');
        parent::addAttribute('date_creation');
    }
}
?>