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

    public static function getStatusStarted()
    {
        return Config::find('STATUS_WAITING_PEOPLE')->content;
    }

    public static function getStatusCanceled()
    {
        return Config::find('STATUS_CANCELED')->content;
    }

    /* Status possiveis:
    1 - aguardando participantes
    2 - aguardando deposito(s)
    3 - processo de compra na loja
    4 - produtos a caminho (Loja)
    5 - entregando produtos
    6 - finalizada
    9 - cancelada

    status parciais (para cada pessoa que participa)
    7 - aguardando entrega do produto
    8 - produto entregado
    */
}
?>