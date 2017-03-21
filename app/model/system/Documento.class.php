<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class Documento extends TRecord
{
    const TABLENAME = 'documento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    public $pagamentos;
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('renavam');
        parent::addAttribute('ano');
        parent::addAttribute('placa');
        parent::addAttribute('placa_ant_uf');
        parent::addAttribute('chassi');
        parent::addAttribute('especie');
        parent::addAttribute('combustivel');
        parent::addAttribute('marca_modelo');
        parent::addAttribute('ano_fabricacao');
        parent::addAttribute('cap_pot_cil');
        parent::addAttribute('categoria');
        parent::addAttribute('cor');
        parent::addAttribute('premio_total');
        parent::addAttribute('observacao');
        parent::addAttribute('data_documento');
        parent::addAttribute('fl_avisado');
        parent::addAttribute('veiculo_id');
    }

    public function getSaldo()
    {
        $valor_pago = 0;
        if($this->pagamentos)
        {
            foreach($this->pagamentos as $pagamento)
            {
                if($pagamento->data_compensacao <= date("Y-m-d"))
                    $valor_pago += $pagamento->valor;
            }
        }

        return $this->premio_total - $valor_pago;
    }

    public function getValorPago()
    {
        $valor_pago = 0;
        if($this->pagamentos)
        {
            foreach($this->pagamentos as $pagamento)
            {
                $valor_pago += $pagamento->valor;
            }
        }

        return $valor_pago;
    }

    public function addPagamento(Pagamento $pagamento)
    {
        $this->pagamentos[] = $pagamento;
    }

    public function clearPagamentos()
    {
        $this->pagamentos = [];
    }

    public function getPagamentos()
    {
        return $this->pagamentos;
    }

    public function getVencimento()
    {
        $ultimo_digito = substr($this->placa, -1, 1);

        $repository = new TRepository('Vencimento');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('digito_placa','=',$ultimo_digito));
        $vencimentos = $repository->load($criteria);

        $vencimento = $vencimentos[0];

        $vencimento->data_vencimento = $this->ano.substr($vencimento->data_vencimento,4,6);

        return $vencimento;
    }

    public function load($id)
    {
        $repository = new TRepository('Pagamento');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('documento_id','=',$id));
        $this->pagamentos = $repository->load($criteria);

        return parent::load($id);
    }

    public function store()
    {
        parent::store();

        $criteria = new TCriteria;
        $criteria->add(new TFilter('documento_id','=',$this->id));
        $repository = new TRepository('Pagamento');
        $repository->delete($criteria);

        if($this->pagamentos)
        {
            foreach($this->pagamentos as $pagamento)
            {
                $pagamento->documento_id = $this->id;
                $pagamento->store();
            }
        }
    }
}
?>