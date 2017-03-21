<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class Cliente extends TRecord
{
    const TABLENAME = 'cliente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    private $veiculos;
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('nome');
        parent::addAttribute('cpf');
        parent::addAttribute('rg');
        parent::addAttribute('cnpj');
        parent::addAttribute('email');
        parent::addAttribute('rua');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('telefone_fixo');
        parent::addAttribute('telefone_celular');
        parent::addAttribute('cidade_id');
    }

    public function getVeiculos()
    {
        return $this->veiculos;
    }

    public function getDocumento()
    {
        return $this->cpf ? self::formatCPF($this->cpf) : self::formatCNPJ($this->cnpj);
    }

    public function getContato()
    {
        ($this->telefone_fixo && $this->telefone_celular) ? $delimiter = " / " : $delimiter = "";

        return $this->telefone_fixo.$delimiter.$this->telefone_celular;
    }

    public function load($id)
    {
        $repository = new TRepository('Veiculo');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cliente_id','=',$id));
        $this->veiculos = $repository->load($criteria);

        return parent::load($id);
    }

    public static function formatCPF($cpf)
    {
        return substr($cpf,0,3).".".substr($cpf,3,3).".".substr($cpf,6,3)."-".substr($cpf,9,2);
    }

    public static function formatCNPJ($cnpj)
    {
        return substr($cnpj,0,2).".".substr($cnpj,2,3).".".substr($cnpj,5,3)."/".substr($cnpj,8,4)."-".substr($cnpj,12,2);
    }
}
?>