<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class Veiculo extends TRecord
{
    const TABLENAME = 'veiculo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    public $documentos;
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('nome');
        parent::addAttribute('marca');
        parent::addAttribute('cor');
        parent::addAttribute('placa');
        parent::addAttribute('observacao');
        parent::addAttribute('cliente_id');
    }

    public function load($id)
    {
        $repository = new TRepository('Documento');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('veiculo_id','=',$id));
        $this->documentos = $repository->load($criteria);

        return parent::load($id);
    }
}
?>