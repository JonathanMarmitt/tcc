<?php

class StoreFormList extends TStandardFormList
{
	protected $form;
	protected $datagrid;
	protected $loaded;
	protected $pageNavigation;

	public function __construct()
	{
		parent::__construct();

		parent::setDatabase('ship');

		parent::setActiveRecord('Store');

		parent::setDefaultOrder('id', 'asc');

		$this->setLimit(-1);

		$this->form = new TQuickForm('form');
		$this->form->class = 'tform';
		$this->form->setFormTitle('Store');

		$id          = new TEntry('id');
		$description = new TEntry('description');
		$link        = new TEntry('link');
		$banner      = new TFile('banner');

		$id->setEditable(false);
		$banner->setCompleteAction(new TAction(array($this, 'onBanner')));

		$description->addValidation(_t('Store name'), new TRequiredValidator);
		$link->addValidation(_t('Link'), new TRequiredValidator);
		$banner->addValidation(_t('Banner'), new TRequiredValidator);

		$this->form->addQuickField('ID', $id, 40);
		$this->form->addQuickField('Store', $description, 250);
		$this->form->addQuickField('Link', $link, 250);
		$this->form->addQuickField('Banner', $banner, 250);

		$this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')),  'ico_save.png');
		$this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'ico_new.png');

		$this->datagrid = new TQuickGrid;
		$this->datagrid->style = 'width: 100%';
		$this->datagrid->setHeight(320);

		//$this->datagrid->addQuickColumn('ID', 'id', 'center', 50, new TAction(array($this, 'onReload')), array('order', 'id'));
		$this->datagrid->addQuickColumn('Store', 'description', 'center', 50, new TAction(array($this, 'onReload')), array('order', 'id'));

		$this->datagrid->addQuickAction('Edit', new TDataGridAction(array($this, 'onEdit')), 'id', 'ico_edit.png');

		$this->datagrid->createModel();

		$vbox = new TVBox;
		$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
		$vbox->add($this->form);
		$vbox->add($this->datagrid);
		$vbox->style = "width: 100%;";

		parent::add($vbox);
	}

	public static function onBanner($param = null)
	{
		//todo??
	}

	public function onSave()
    {
        try
        {
            // open a transaction with database
            TTransaction::open($this->database);
            
            // get the form data

            // validate data
            $this->form->validate();
            
            $data = $this->form->getData();
            
            $store = new Store($data->id);
            $store->description = $data->description;
            $store->link        = $data->link;
            $store->store();

            //banner transformation
            $banner = 'tmp/'.$data->banner;

            rename($banner, 'app/store_banner/'.$store->id);
            
            // fill the form with the active record data
            $this->form->setData($store);
            
            // close the transaction
            TTransaction::close();
            
            // shows the success message
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
            // reload the listing
            $this->onReload();
            
            return $store;
        }
        catch (Exception $e) // in case of exception
        {
            // get the form data
            $object = $this->form->getData($this->activeRecord);
            
            // fill the form with the active record data
            $this->form->setData($object);
            
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}