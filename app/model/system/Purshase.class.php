<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class Purshase extends TRecord
{
    const TABLENAME = 'purshase';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('store_id');
        parent::addAttribute('like_id');
        parent::addAttribute('people_id');
        parent::addAttribute('status_id');
        parent::addAttribute('min_people');
        parent::addAttribute('max_people');
        parent::addAttribute('date_until');
        parent::addAttribute('deposite_information');
        parent::addAttribute('track_link');
        parent::addAttribute('maps_address');
    }

    public static function cancel()
    {
        $this->status_id = 2; //FIXME
    }

    public function getCurrent()
    {
        return 2;
    }

    public function getColor($current)
    {
        if($current >= $this->max_people)
            return 'green';
        else if($current >= $this->min_people)
            return 'yellow';
        else if($current < $this->min_people)
            return 'red';
        else
            throw new Exception('Erro ao buscar cor!');
    }
}
?>