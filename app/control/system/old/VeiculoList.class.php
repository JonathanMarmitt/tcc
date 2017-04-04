<?php
/**
 * System_userList Listing
 * @author  <your name here>
 */
class VeiculoList extends TPage
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
        $this->form = new TForm('form_search_veiculo');
        $this->form->class = 'tform';
        
        // creates a table
        $table = new TTable;
        $table->style = 'width:100%';
        
        $table->addRowSet( new TLabel('Veiculos'), '' )->class = 'tformtitle';
        
        // add the table inside the form
        $this->form->add($table);
        
        $nome  = new TEntry('nome');
        $marca = new TEntry('marca');
        $placa = new TEntry('placa');

        $cliente           = new TDBSeekButton('cliente', 'despachante', 'form_search_veiculo', 'Cliente', 'nome', 'cliente', 'cliente_nome');
        $cliente->setValue(TSession::getValue('veiculo_cliente'));
        $cliente->setSize(60);
        $cliente_nome = new TEntry('cliente_nome');
        $cliente_nome->setEditable(false);
        $cliente_nome->setSize(300);
        
        $placa->setMask("AAA-9999");
        $placa->setSize(70);
        // add a row for the filter field
        //$table->addRowSet(new TLabel('Código:'), $id);
        $table->addRowSet(new TLabel('Placa:'), $placa);
        $table->addRowSet(new TLabel('Cliente:'), array($cliente,$cliente_nome));
        $table->addRowSet(new TLabel('Descrição:'), $nome);
        $table->addRowSet(new TLabel('Marca:'), $marca);

        // create two action buttons to the form
        $find_button = new TButton('find');
        $new_button  = new TButton('new');
        // define the button actions
        $find_button->setAction(new TAction(array($this, 'onSearch')), _t('Find'));
        $find_button->setImage('fa:search');
        
        $new_button->setAction(new TAction(array('VeiculoForm', 'onEdit')), _t('New'));
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
        $this->form->setFields(array($cliente, $placa, $nome, $marca, $find_button, $new_button));
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(320);
        $this->datagrid->style = 'width: 100%';
        
        // creates the datagrid columns
        $cliente  = new TDataGridColumn('cliente', 'Cliente',   'left');
        $nome     = new TDataGridColumn('nome',    'Descrição', 'left');
        $marca    = new TDataGridColumn('marca',   'Marca',     'left');
        $cor      = new TDataGridColumn('cor',     'Cor',       'left');
        $placa    = new TDataGridColumn('placa',   'Placa',     'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($cliente);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($marca);
        $this->datagrid->addColumn($cor);
        $this->datagrid->addColumn($placa);

        // creates the datagrid column actions
        $order_id= new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');

        $order_name= new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        $nome->setAction($order_name);

        // inline editing
        //$name_edit = new TDataGridAction(array($this, 'onInlineEdit'));
        //$name_edit->setField('id');
        //$name->setEditAction($name_edit);

        // creates two datagrid actions
        $action1 = new TDataGridAction(array('VeiculoForm', 'onEdit'));
        $action1->setLabel(_t('Edit'));
        $action1->setImage('fa:pencil-square-o blue fa-lg');
        $action1->setField('id');
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel(_t('Delete'));
        $action2->setImage('fa:trash-o red fa-lg');
        $action2->setField('id');

        $action3 = new TDataGridAction(array($this, 'onFormDocumento'));
        $action3->setLabel("Cadastrar novo documento");
        $action3->setImage('fa:plus-square green fa-lg');
        $action3->setField('id');
        
        $action4 = new TDataGridAction(array($this, 'onListDocumentos'));
        $action4->setLabel('Ver Documentos');
        $action4->setImage('fa:file-o fa-lg');
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
    
    public function onFormDocumento($params)
    {
        TApplication::gotoPage("DocumentoForm","onEdit",array("veiculo_id"=>$params['id']));
    }

    public function onListDocumentos($params)
    {
        $filter = new TFilter('veiculo_id', '=', $params['id']);

        TSession::setValue('documento_veiculo', $params['id']);
        TSession::setValue('documento_veiculo_filter',   $filter);

        TApplication::gotoPage("DocumentoList");
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
        
        TSession::setValue('veiculo_placa', '');
        TSession::setValue('veiculo_placa_filter',   NULL);
        
        TSession::setValue('veiculo_cliente', '');
        TSession::setValue('veiculo_cliente_filter',   NULL);

        TSession::setValue('veiculo_nome', '');
        TSession::setValue('veiculo_nome_filter',   NULL);

        TSession::setValue('veiculo_marca', '');
        TSession::setValue('veiculo_marca_filter',   NULL);

        if ( $data->placa )
        {
            $filter = new TFilter('placa', 'ilike', "%{$data->placa}%");
            
            TSession::setValue('veiculo_placa_filter',   $filter);
            TSession::setValue('veiculo_placa', $data->placa);
        }

        if ( $data->cliente )
        {
            $filter = new TFilter('cliente_id', '=', $data->cliente);
            
            TSession::setValue('veiculo_cliente_filter',   $filter);
            TSession::setValue('veiculo_cliente', $data->cliente);
        }

        if ( $data->nome )
        {
            $filter = new TFilter('nome', 'ilike', "%{$data->nome}%");
            
            TSession::setValue('veiculo_nome_filter',   $filter);
            TSession::setValue('veiculo_nome', $data->nome);
        }

        if ( $data->marca )
        {
            $filter = new TFilter('marca', 'ilike', "%{$data->marca}%");
            
            TSession::setValue('veiculo_marca_filter',   $filter);
            TSession::setValue('veiculo_marca', $data->marca);
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
            $repository = new TRepository('Veiculo');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('veiculo_placa_filter'))
                $criteria->add(TSession::getValue('veiculo_placa_filter'));

            if (TSession::getValue('veiculo_cliente_filter'))
                $criteria->add(TSession::getValue('veiculo_cliente_filter'));
            
            if (TSession::getValue('veiculo_nome_filter'))
                $criteria->add(TSession::getValue('veiculo_nome_filter'));

            if (TSession::getValue('veiculo_marca_filter'))
                $criteria->add(TSession::getValue('veiculo_marca_filter'));

            // load the objects according to criteria
            $objects = $repository->load($criteria);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
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
            $object = new Veiculo($key);
            
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