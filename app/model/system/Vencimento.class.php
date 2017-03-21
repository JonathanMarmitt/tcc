<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class Vencimento extends TRecord
{
    const TABLENAME = 'vencimento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('digito_placa');
        parent::addAttribute('data_vencimento');
    }
}
?>