<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class PurshaseWith extends TRecord
{
    const TABLENAME = 'purshase_with';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('people_id');
        parent::addAttribute('purshase_id');
        parent::addAttribute('product_link');
        parent::addAttribute('price');
        parent::addAttribute('receipt');
        parent::addAttribute('fl_deposit_received');
    }
}
?>