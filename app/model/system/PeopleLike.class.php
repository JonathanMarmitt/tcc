<?php
/**
 * System_user Active Record
 * @author  Jonathan Marmitt
 */
class PeopleLike extends TRecord
{
    const TABLENAME = 'people_like';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('people_id');
        parent::addAttribute('page_name');
        parent::addAttribute('category');
        parent::addAttribute('page_picture');
        parent::addAttribute('fl_list');
    }
}
?>