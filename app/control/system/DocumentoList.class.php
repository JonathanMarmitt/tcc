<?php
/**
 * System_userList Listing
 * @author  <your name here>
 */
class DocumentoList extends TPage
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
        $this->form = new TForm('form_search_documento');
        $this->form->class = 'tform';
        
        // creates a table
        $table = new TTable;
        $table->style = 'width:100%';
        
        $table->addRowSet( new TLabel('Documentos'), '' )->class = 'tformtitle';
        
        // add the table inside the form
        $this->form->add($table);
        
        $marca     = new TEntry('marca');
        $placa     = new TEntry('placa');
        $ano_min   = new TEntry('ano_min');
        $ano_max   = new TEntry('ano_max');
        $categoria = new TEntry('categoria');

        $cliente = new TDBSeekButton('cliente', 'despachante', 'form_search_documento', 'Cliente', 'nome', 'cliente', 'cliente_nome');
        $cliente->setSize(60);
        $cliente_nome = new TEntry('cliente_nome');
        $cliente_nome->setEditable(false);
        $cliente_nome->setSize(300);

        $veiculo           = new TDBSeekButton('veiculo', 'despachante', 'form_search_documento', 'Veiculo', 'nome', 'veiculo', 'veiculo_nome');
        $veiculo->setSize(60);
        $veiculo->setValue(TSession::getValue('documento_veiculo'));
        $veiculo_nome = new TEntry('veiculo_nome');
        $veiculo_nome->setEditable(false);
        $veiculo_nome->setSize(300);
        
        $placa->setMask("AAA-9999");
        $placa->setSize(70);

        $ano_min->setSize(50);
        $ano_max->setSize(50);
        // add a row for the filter field
        //$table->addRowSet(new TLabel('Código:'), $id);
        $table->addRowSet(new TLabel('Cliente:'), array($cliente,$cliente_nome));
        $table->addRowSet(new TLabel('Veículo:'), array($veiculo,$veiculo_nome));
        $table->addRowSet(new TLabel('Placa:'), $placa);
        $table->addRowSet(new TLabel('Ano:'), array($ano_min,new TLabel("Até:&nbsp;"),$ano_max));
        $table->addRowSet(new TLabel('Categoria:'), $categoria);
        $table->addRowSet(new TLabel('Marca:'), $marca);

        // create two action buttons to the form
        $find_button = new TButton('find');
        $new_button  = new TButton('new');
        // define the button actions
        $find_button->setAction(new TAction(array($this, 'onSearch')), _t('Find'));
        $find_button->setImage('fa:search');
        
        $new_button->setAction(new TAction(array('DocumentoForm', 'onEdit')), _t('New'));
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
        $this->form->setFields(array($cliente, $veiculo, $placa, $ano_min, $ano_max, $marca, $categoria, $find_button, $new_button));
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(320);
        $this->datagrid->style = 'width: 100%';
        
        // creates the datagrid columns
        $cliente    = new TDataGridColumn('cliente',      'Cliente',         'left');
        $nome       = new TDataGridColumn('nome',         'Descrição',       'left');
        $marca      = new TDataGridColumn('marca',        'Marca',           'left');
        $cor        = new TDataGridColumn('cor',          'Cor',             'left');
        $placa      = new TDataGridColumn('placa',        'Placa',           'left');
        $premio     = new TDataGridColumn('premio_total', 'Valor',           'left');
        $valor_pago = new TDataGridColumn('valor_pago',   'Valor pago',      'left');
        $saldo      = new TDataGridColumn('saldo',        'Saldo em aberto', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($cliente);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($marca);
        $this->datagrid->addColumn($cor);
        $this->datagrid->addColumn($placa);
        $this->datagrid->addColumn($premio);
        $this->datagrid->addColumn($valor_pago);
        $this->datagrid->addColumn($saldo);

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
        $action1 = new TDataGridAction(array('DocumentoForm', 'onEdit'));
        $action1->setLabel(_t('Edit'));
        $action1->setImage('fa:pencil-square-o blue fa-lg');
        $action1->setField('id');
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel(_t('Delete'));
        $action2->setImage('fa:trash-o red fa-lg');
        $action2->setField('id');

        $action3 = new TDataGridAction(array('PagamentoForm', 'onEdit'));
        $action3->setLabel("Registrar Pagamento");
        $action3->setImage('fa:dollar fa-lg');
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
        $table->style = 'width: 80%';
        $table->addRow()->addCell(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $table->addRow()->addCell($this->form);
        $table->addRow()->addCell($this->datagrid);
        $table->addRow()->addCell($this->pageNavigation);
        
        // add the table inside the page
        parent::add($table);
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
        
        TSession::setValue('documento_cliente', '');
        TSession::setValue('documento_cliente_filter',   NULL);

        TSession::setValue('documento_veiculo', '');
        TSession::setValue('documento_veiculo_filter',   NULL);
        
        TSession::setValue('documento_placa', '');
        TSession::setValue('documento_placa_filter',   NULL);

        TSession::setValue('documento_ano_min', '');
        TSession::setValue('documento_ano_min_filter',   NULL);

        TSession::setValue('documento_ano_max', '');
        TSession::setValue('documento_ano_max_filter',   NULL);

        TSession::setValue('documento_categoria', '');
        TSession::setValue('documento_categoria_filter',   NULL);

        TSession::setValue('documento_marca', '');
        TSession::setValue('documento_marca_filter',   NULL);

        if ( $data->cliente )
        {
            $filter = new TFilter('veiculo_id', 'in', "NOESC: (SELECT id FROM veiculo WHERE cliente_id={$data->cliente})");
            
            TSession::setValue('documento_cliente_filter',   $filter);
            TSession::setValue('documento_cliente', $data->cliente);
        }

        if ( $data->placa )
        {
            $filter = new TFilter('placa', 'ilike', "%{$data->placa}%");
            
            TSession::setValue('documento_placa_filter',   $filter);
            TSession::setValue('documento_placa', $data->placa);
        }

        if ( $data->veiculo )
        {
            $filter = new TFilter('veiculo_id', '=', $data->veiculo);
            
            TSession::setValue('documento_veiculo_filter',   $filter);
            TSession::setValue('documento_veiculo', $data->veiculo);
        }

        if ( $data->ano_min )
        {
            $filter = new TFilter('ano', '>=', "{$data->ano_min}");
            
            TSession::setValue('documento_ano_min_filter',   $filter);
            TSession::setValue('documento_ano_min', $data->ano_min);
        }

        if ( $data->ano_max )
        {
            $filter = new TFilter('ano', '<=', "{$data->ano_max}");
            
            TSession::setValue('documento_ano_max_filter',   $filter);
            TSession::setValue('documento_ano_max', $data->ano_max);
        }

        if ( $data->marca )
        {
            $filter = new TFilter('marca', 'ilike', "%{$data->marca}%");
            
            TSession::setValue('documento_marca_filter',   $filter);
            TSession::setValue('documento_marca', $data->marca);
        }

        if ( $data->categoria )
        {
            $filter = new TFilter('categoria', 'ilike', "%{$data->categoria}%");
            
            TSession::setValue('documento_categoria_filter',   $filter);
            TSession::setValue('documento_categoria', $data->categoria);
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
            $repository = new TRepository('Documento');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('documento_cliente_filter'))
                $criteria->add(TSession::getValue('documento_cliente_filter'));

            if (TSession::getValue('documento_placa_filter'))
                $criteria->add(TSession::getValue('documento_placa_filter'));

            if (TSession::getValue('documento_veiculo_filter'))
                $criteria->add(TSession::getValue('documento_veiculo_filter'));
            
            if (TSession::getValue('documento_ano_min_filter'))
                $criteria->add(TSession::getValue('documento_ano_min_filter'));

            if (TSession::getValue('documento_ano_max_filter'))
                $criteria->add(TSession::getValue('documento_ano_max_filter'));

            if (TSession::getValue('documento_marca_filter'))
                $criteria->add(TSession::getValue('documento_marca_filter'));

            if (TSession::getValue('documento_categoria_filter'))
                $criteria->add(TSession::getValue('documento_categoria_filter'));

            // load the objects according to criteria
            $objects = $repository->load($criteria);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    $veiculo = new Veiculo($object->veiculo_id);
                    $cliente = new Cliente($veiculo->cliente_id);

                    $object->cliente      = $cliente->nome;
                    $object->nome         = $veiculo->nome;
                    $object->marca        = $veiculo->marca;
                    $object->saldo        = "R$ ".number_format((float)$object->getSaldo(), 2, ",", ".");
                    $object->valor_pago   = "R$ ".number_format((float)$object->getValorpago(), 2, ",", ".");
                    $object->premio_total = "R$ ".number_format((float)$object->premio_total, 2, ",", ".");

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
            $object = new Documento($key);
            
            if($object->pagamentos)
                throw new Exception("Este documento possui pagamentos vinculados! Delete-os antes de remover o documento!");

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