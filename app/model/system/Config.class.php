<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class Config extends TRecord
{
    const TABLENAME = 'config';
    const PRIMARYKEY= 'key';
    const IDPOLICY =  'serial'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('content');
    }
}
?>