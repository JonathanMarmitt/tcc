<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class Pagamento extends TRecord
{
    const TABLENAME = 'pagamento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('documento_id');
        parent::addAttribute('valor');
        parent::addAttribute('data_entrada');
        parent::addAttribute('data_compensacao');
    }
}
?>