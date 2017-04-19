<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class People extends TRecord
{
    const TABLENAME = 'people';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}

    private $likes;

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('maps_location');
    }

    public function getLikes()
    {
        $this->loadLikes();

        return $this->likes;
    }

    public function setLikesFalse()
    {
        $this->loadLikes();

        foreach ($this->likes as $like)
        {
            $like->fl_list = 'f';
            $like->store();
        }
    }

    public function getLikesToList()
    {
        $l = array();

        if(!$this->likes)
            $this->loadLikes();

        foreach($this->likes as $like)
        {
            if($like->fl_list == 't')
                $l[] = $like;
        }

        return $l;
    }

    private function loadLikes()
    {
        if(!$this->likes)
        {
            $criteria = new TCriteria;
            $criteria->add(new TFilter('people_id','=',$this->id));

            $this->likes = PeopleLike::getObjects($criteria);
        }
    }
}
?>