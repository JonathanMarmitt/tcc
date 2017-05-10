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
        parent::addAttribute('status_id');
        parent::addAttribute('purshase_id');
        parent::addAttribute('product_link');
        parent::addAttribute('price');
        parent::addAttribute('receipt');
        parent::addAttribute('fl_deposit_received');
    }

    public static function getMyActivePurshases()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('people_id','=', TSession::getValue('fb-id')));
        $criteria->add(new TFilter('status_id','<>', Status::getStatusCanceled()));
        ##FIXME: adicionar os status

        $purshases = array();
        $purshases_with = self::getObjects($criteria);
        
        if($purshases_with)
        {
            foreach($purshases_with as $pw)
            {
                $purshases[] = new Purshase($pw->purshase_id);
            }
        }

        return $purshases;
    }
}
?>