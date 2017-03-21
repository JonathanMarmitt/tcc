<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class Cidade extends TRecord
{
    const TABLENAME = 'cidade';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('nome');
        parent::addAttribute('estado_nome');
        parent::addAttribute('estado_uf');
    }

    public function existClientes()
    {
        $repository = new TRepository('Cliente');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cidade_id','=',$this->id));
        $clientes = $repository->load($criteria);

        return $clientes ? true : false;
    }
}
?>