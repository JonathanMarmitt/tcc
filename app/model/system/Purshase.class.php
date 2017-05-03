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

    private $purshasesWith;

    private $status;
    private $store;

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

    public function get_status()
    {
        if(empty($this->status))
            $this->status = new Status($this->status_id);

        return $this->status;
    }

    public function get_store()
    {
        if(empty($this->store))
            $this->store = new Store($this->store_id);

        return $this->store;
    }

    public static function cancel()
    {
        $this->status_id = 2; //FIXME
    }

    public function loadPurshaseWith()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('purshase_id','=',$this->id));

        $this->purshasesWith = PurshaseWith::getObjects($criteria);
    }

    public function getCurrentPeople()
    {
        $this->loadPurshaseWith();

        return count($this->purshasesWith);
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

    public function getLocation()
    {
        if(!$this->maps_address)
        {
            $people = new People($this->people_id);

            return $people->maps_location;
        }
        else
            return $this->maps_address;
    }

    public static function getCurrentByStore($store_id, $people_id = null)
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('store_id','=',$store_id));
        if($people_id)
            $criteria->add(new TFilter('people_id','<>',$people_id));

        return self::getObjects($criteria);
    }

    public function addPeople($people_id, $link, $price)
    {
        $this->loadPurshaseWith();

        if($this->max_people < count($this->purshasesWith))
            throw new Exception("Esta compra já atingiu o nº máximo de pessoas!");

        if($this->date_until < date('Y-m-d'))
            throw new Exception("Esta compra já não é mais válida!");

        $purshase_with = new PurshaseWith();
        $purshase_with->purshase_id = $this->id;
        $purshase_with->people_id = $people_id;
        $purshase_with->product_link = $link;
        $purshase_with->price = $price;

        if($purshase_with->store())
            return true;
        else
            new TMessage('error', 'Erro ao adicionar pessoa');
    }

    public function removePeople($people_id)
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('people_id','=',$people_id));
        $criteria->add(new TFilter('purshase_id','=',$this->id));

        $objs = PurshaseWith::getObjects($criteria);

        if($objs)
        {
            $objs[0]->delete();

            return true;
        }
        else
            throw new Exception("Nenhuma compra vinculada");
    }

    public function hasPeople($people_id)
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('people_id','=',$people_id));
        $criteria->add(new TFilter('purshase_id','=',$this->id));

        $objs = PurshaseWith::getObjects($criteria);

        return $objs ? $objs[0] : null;
    }

    public static function getMyActivePurshases()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('people_id','=', TSession::getValue('fb-id')));
        ##FIXME: adicionar os status

        return self::getObjects($criteria);
    }

    public function store()
    {
        $this->maps_address = json_encode(Geolocation::getLocation());

        return parent::store();
    }
}
?>