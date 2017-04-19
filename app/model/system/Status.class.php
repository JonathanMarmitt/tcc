<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class Status extends TRecord
{
    const TABLENAME = 'status';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('description');
    }
}
?>