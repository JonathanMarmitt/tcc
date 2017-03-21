<?php
/**
 * System_userList Listing
 * @author  <your name here>
 */
class ClienteList extends TPage
{
    private $form;     // registration form
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
        $this->form = new TForm('form_search_cliente');
        $this->form->class = 'tform';
        
        // creates a table
        $table = new TTable;
        $table->style = 'width:100%';
        
        $table->addRowSet( new TLabel('Clientes'), '' )->class = 'tformtitle';
        
        // add the table inside the form
        $this->form->add($table);
        
        // create the form fields
        $id               = new TEntry('id');
        $nome             = new TEntry('nome');
        $cidade           = new TDBSeekButton('cidade', 'despachante', 'form_search_cliente', 'Cidade', 'nome', 'cidade', 'cidade_nome');
        $cidade_nome = new TEntry('cidade_nome');
        $cidade_nome->setEditable(false);
        $cidade_nome->setSize(300);

        $id->setSize(70);

        // add a row for the filter field
        $table->addRowSet(new TLabel('Código:'), $id);
        $table->addRowSet(new TLabel('Nome:'), $nome);
        $table->addRowSet(new TLabel('Cidade:'), array($cidade,$cidade_nome));

        // create two action buttons to the form
        $find_button = new TButton('find');
        $new_button  = new TButton('new');
        // define the button actions
        $find_button->setAction(new TAction(array($this, 'onSearch')), _t('Find'));
        $find_button->setImage('fa:search');
        
        $new_button->setAction(new TAction(array('ClienteForm', 'onEdit')), _t('New'));
        $new_button->setImage('fa:plus-square green');
        
        // add a row for the form actions
        $container = new THBox;
        $container->add($find_button);
        $container->add($new_button);

        $row=$table->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $container );
        $cell->colspan = 2;
        
        // define wich are the form fields
        $this->form->setFields(array($id, $nome,$cidade, $find_button, $new_button));
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(320);
        $this->datagrid->style = 'width: 100%';
        
        // creates the datagrid columns
        $id       = new TDataGridColumn('id',       'Código',    'right');
        $nome     = new TDataGridColumn('nome',     'Nome',      'left');
        $telefone = new TDataGridColumn('telefone', 'Telefones', 'left');
        $cidade   = new TDataGridColumn('cidade',   'Cidade',    'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($telefone);
        $this->datagrid->addColumn($cidade);

        // creates the datagrid column actions
        $order_id= new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $id->setAction($order_id);

        $order_name= new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        $nome->setAction($order_name);

        // inline editing
        //$name_edit = new TDataGridAction(array($this, 'onInlineEdit'));
        //$name_edit->setField('id');
        //$name->setEditAction($name_edit);

        // creates two datagrid actions
        $action1 = new TDataGridAction(array('ClienteForm', 'onEdit'));
        $action1->setLabel(_t('Edit'));
        $action1->setImage('fa:pencil-square-o blue fa-lg');
        $action1->setField('id');
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel(_t('Delete'));
        $action2->setImage('fa:trash-o red fa-lg');
        $action2->setField('id');

        $action3 = new TDataGridAction(array($this, 'onListVeiculos'));
        $action3->setLabel('Ver Veículos');
        $action3->setImage('fa:car fa-lg');
        $action3->setField('id');

        $action4 = new TDataGridAction(array($this, 'onFormVeiculos'));
        $action4->setLabel('Cadastrar Veículo');
        $action4->setImage('fa:plus-square green fa-lg');
        $action4->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        $this->datagrid->addAction($action3);
        $this->datagrid->addAction($action4);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // creates the page structure using a table
        $table = new TTable;
        $table->style = 'width: 80%';
        $table->addRow()->addCell(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $table->addRow()->addCell($this->form);
        $table->addRow()->addCell($this->datagrid);
        $table->addRow()->addCell($this->pageNavigation);
        
        // add the table inside the page
        parent::add($table);
    }
    
    public function onListVeiculos($params)
    {
        $filter = new TFilter('cliente_id', '=', $params['id']);

        TSession::setValue('veiculo_cliente', $params['id']);
        TSession::setValue('veiculo_cliente_filter',   $filter);

        TApplication::gotoPage("VeiculoList");
    }

    public function onFormVeiculos($params)
    {
        //$filter = new TFilter('cliente_id', '=', $params['id']);

        //TSession::setValue('veiculo_cliente', $params['id']);
        TSession::setValue('veiculo_cliente_id',   $params['id']);

        TApplication::gotoPage("VeiculoForm");
    }

    /**
     * method onInlineEdit()
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    /*function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            // open a transaction with database 'permission'
            TTransaction::open('permission');
            
            // instantiates object System_user
            $object = new SystemUser($key);
            // deletes the object from the database
            $object->{$field} = $value;
            $object->store();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload($param);
            // shows the success message
            new TMessage('info', _t('Record Updated'));
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }*/
    
    /**
     * method onSearch()
     * Register the filter in the session when the user performs a search
     */
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        TSession::setValue('cidade_id', '');
        TSession::setValue('cidade_id_filter',   NULL);

        TSession::setValue('cliente_nome', '');
        TSession::setValue('cliente_nome_filter',   NULL);

        TSession::setValue('cliente_cidade', '');
        TSession::setValue('cliente_cidade_filter',   NULL);
        
        if ( $data->id )
        {
            // creates a filter using what the user has typed
            $filter = new TFilter('id', '=', $data->id);
            
            // stores the filter in the session
            TSession::setValue('cliente_id_filter',   $filter);
            TSession::setValue('cliente_id', $data->id);
        }

        if ( $data->nome )
        {
            // creates a filter using what the user has typed
            $filter = new TFilter('nome', 'ilike', "%{$data->nome}%");
            
            // stores the filter in the session
            TSession::setValue('cliente_nome_filter',   $filter);
            TSession::setValue('cliente_nome', $data->nome);
        }

        if ( $data->cidade )
        {
            // creates a filter using what the user has typed
            $filter = new TFilter('cidade_id', '=', $data->cidade);
            
            // stores the filter in the session
            TSession::setValue('cliente_cidade_filter',   $filter);
            TSession::setValue('cliente_cidade', $data->cidade);
        }

        // fill the form with data again
        $this->form->setData($data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
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
            TTransaction::open('despachante');
            
            if( ! isset($param['order']) )
                $param['order'] = 'id';
            
            // creates a repository for System_user
            $repository = new TRepository('Cliente');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('cliente_id_filter'))
                $criteria->add(TSession::getValue('cliente_id_filter'));

            if (TSession::getValue('cliente_nome_filter'))
                $criteria->add(TSession::getValue('cliente_nome_filter'));
            
            if (TSession::getValue('cliente_cidade_filter'))
                $criteria->add(TSession::getValue('cliente_cidade_filter'));

            $objects = $repository->load($criteria);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    $cidade = new Cidade($object->cidade_id);

                    $object->telefone = $object->getContato();
                    $object->cidade   = $cidade->nome;
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
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
    function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * method Delete()
     * Delete a record
     */
    function Delete($param)
    {
        try
        {
            // get the parameter $key
            $key=$param['key'];
            // open a transaction with database 'permission'
            TTransaction::open('despachante');
            
            // instantiates object System_user
            $object = new Cliente($key);
            
            if($object->getVeiculos())
            {
                throw new Exception("Este cliente possui veículos vinculados! Remova-os antes de deletar o cliente");
            }

            // deletes the object from the database
            $object->delete();
            
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