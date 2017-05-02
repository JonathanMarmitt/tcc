<?php
/**
 * System_userList Listing
 * @author  <your name here>
 */
class BoughtsWith extends TPage
{
    //private $form;     // registration form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        //$this->form = new TForm('form_search_System_user');
        //$this->form->class = 'tform';
        
        // creates a table
        //$table = new TTable;
        //$table->style = 'width:100%';
        
        //$table->addRowSet( new TLabel('Cidades'), '' )->class = 'tformtitle';
        
        // add the table inside the form
        //$this->form->add($table);
        
        // create the form fields
        //$id = new TEntry('id');
        //$id->setValue(TSession::getValue('System_user_id'));
        
        //$nome = new TEntry('nome');
        //$name->setValue(TSession::getValue('System_user_name'));
        
        // add a row for the filter field
        //$table->addRowSet(new TLabel('Código:'), $id);
        //$table->addRowSet(new TLabel('Nome:'), $nome);
        
        // create two action buttons to the form
        //$find_button = new TButton('find');
        //$new_button  = new TButton('new');
        // define the button actions
        //$find_button->setAction(new TAction(array($this, 'onSearch')), _t('Find'));
        //$find_button->setImage('fa:search');
        
        //$new_button->setAction(new TAction(array('CidadeForm', 'onEdit')), _t('New'));
        //$new_button->setImage('fa:plus-square green');
        
        // add a row for the form actions
        //$container = new THBox;
        //$container->add($find_button);
        //$container->add($new_button);

        //$row=$table->addRow();
        //$row->class = 'tformaction';
        //$cell = $row->addCell( $container );
        //$cell->colspan = 2;
        
        // define wich are the form fields
        //$this->form->setFields(array($nome, $find_button, $new_button));
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(320);
        $this->datagrid->style = 'width: 100%';
        
        // creates the datagrid columns
        //$id      = new TDataGridColumn('id',    'Código', 'right');
        //$nome    = new TDataGridColumn('people_id',  'Nome', 'left');
        $store       = new TDataGridColumn('option', 'Loja', 'left');
        $status      = new TDataGridColumn('status_id', 'Status', 'left');
        $goal        = new TDataGridColumn('goal', 'Total', 'left');
        //$min_people  = new TDataGridColumn('min_people', 'Min', 'left');
        //$max_people  = new TDataGridColumn('max_people', 'Max', 'left');
        //$date_until  = new TDataGridColumn('date_until', 'Até', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($store);
        $this->datagrid->addColumn($status);
        $this->datagrid->addColumn($goal);
        //$this->datagrid->addColumn($min_people);
        //$this->datagrid->addColumn($max_people);
        //$this->datagrid->addColumn($date_until);

        // creates the datagrid column actions
        //$order_id= new TAction(array($this, 'onReload'));
        //$order_id->setParameter('order', 'id');
        //$id->setAction($order_id);

        //$order_name= new TAction(array($this, 'onReload'));
        //$order_name->setParameter('order', 'name');
        //$nome->setAction($order_name);

        // inline editing
        //$name_edit = new TDataGridAction(array($this, 'onInlineEdit'));
        //$name_edit->setField('id');
        //$name->setEditAction($name_edit);

        // creates two datagrid actions
        $action1 = new TDataGridAction(array($this, 'onDetail'));
        $action1->setLabel(_t('List'));
        $action1->setImage('fa:list blue fa-lg');
        $action1->setField('id');
        
        $action2 = new TDataGridAction(array($this, 'onCancel'));
        $action2->setLabel(_t('Cancel'));
        $action2->setImage('fa:ban red fa-lg');
        $action2->setField('id');

        $action3 = new TDataGridAction(array($this, 'onConclude'));
        $action3->setLabel(_t('Make'));
        $action3->setImage('fa:gavel green fa-lg');
        $action3->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        $this->datagrid->addAction($action3);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // creates the page structure using a table
        $table = new TTable;
        $table->style = 'width: 100%';
        $table->addRow()->addCell(new TXMLBreadCrumb('menu.xml', __CLASS__));
        //$table->addRow()->addCell($this->form);
        $table->addRow()->addCell($this->datagrid);
        //$table->addRow()->addCell($this->pageNavigation);
        
        // add the table inside the page
        parent::add($table);
    }
    
    /**
     * method onSearch()
     * Register the filter in the session when the user performs a search
     
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        TSession::setValue('cidade_nome', '');
        TSession::setValue('cidade_nome_filter',   NULL);
        
        if ( $data->nome )
        {
            // creates a filter using what the user has typed
            $filter = new TFilter('nome', 'ilike', "%{$data->nome}%");
            
            // stores the filter in the session
            TSession::setValue('cidade_nome_filter',   $filter);
            TSession::setValue('cidade_nome', $data->nome);
        }
        // fill the form with data again
        $this->form->setData($data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }*/
    
    static function onDetail($param = null)
    {
        new TMessage('info', 'Mostrar detalhes da compra');
    }

    static function onConclude($param = null)
    {
        new TMessage('info', 'Processo de finalizacao da compra pelo comprador');
    }

    /**
     * method onReload()
     * Load the datagrid with the database objects
     */
    function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'permission'
            TTransaction::open('ship');

            $repository = new TRepository('Purshase');
            $criteria = new TCriteria;
            $criteria->setProperties($param);
            $criteria->add(new TFilter('people_id', '=', TSession::getValue('fb-id')));
            //$criteria->setProperty('limit', $limit);
            
            $objects = $repository->load($criteria);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    if(!$option = Store::find($object->store_id))
                    {
                        $option = new PeopleLike($object->like_id);
                        $option->description = $option->page_name;
                    }
                    $status = new Status($object->status_id);

                    $current = $object->getCurrentPeople();

                    $object->option = $option->description;
                    $object->status_id = $status->description;

                    $object->goal = "<font color='".$object->getColor($current)."'>".$current.' / '.$object->max_people."</font>";

                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            

            //$this->pageNavigation->setCount($count); // count of records
            //$this->pageNavigation->setProperties($param); // order, page
            //$this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onDelete()
     * executed whenever the user clicks at the delete button
     * Ask if the user really wants to delete the record
     */
    function onCancel($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Cancel'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * method Delete()
     * Delete a record
     */
    function Cancel($param)
    {
        try
        {
            // get the parameter $key
            $key = $param['key'];
            // open a transaction with database 'permission'
            TTransaction::open('ship');
            
            // instantiates object System_user
            $object = new Purshase($key);
     
            //if($object->existClientes())
            //    throw new Exception("Existem clientes cadastrados para esta cidade! Remova-os antes de deletar a cidade");       
            // deletes the object from the database
            $object->cancel();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload( $param );
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'));
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method show()
     * Shows the page
     */
    function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded)
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
?>