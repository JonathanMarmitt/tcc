<?php
/**
 * System_userList Listing
 * @author  <your name here>
 */
class Interesses extends TPage
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
        $this->form = new TForm('form_search_dashboard');
        $this->form->class = 'tform';
        
        // creates a table
        $table = new TTable;
        $table->style = 'width:100%';
        
        $table->addRowSet( new TLabel('Documentos à vencer'), '' )->class = 'tformtitle';
        
        $dias = new TEntry('dias');
        
        $table->addRowSet(new TLabel('Diferença de dias à vencer:'), $dias);

        $find_button = new TButton('find');

        $find_button->setAction(new TAction(array($this, 'onSearch')), _t('Find'));
        $find_button->setImage('fa:search');

        $container = new THBox;
        $container->add($find_button);

        $row=$table->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $container );
        $cell->colspan = 2;
        
        // define wich are the form fields
        $this->form->setFields(array($dias,$find_button));
        

        // add the table inside the form
        $this->form->add($table);
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(320);
        $this->datagrid->style = 'width: 100%';
        
        // creates the datagrid columns
        //$id      = new TDataGridColumn('id',    'Código', 'right');
        $cliente = new TDataGridColumn('cliente',  'Cliente', 'left');
        $contato = new TDataGridColumn('contato',  'Contato', 'left');
        $veiculo = new TDataGridColumn('veiculo',  'Veículo', 'left');
        $placa   = new TDataGridColumn('placa',    'Placa', 'left');
        $premio  = new TDataGridColumn('premio',   'Prêmio', 'left');
        $saldo   = new TDataGridColumn('saldo',    'Saldo (aberto)', 'left');
        $dias    = new TDataGridColumn('dias',     'Dias à vencer', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($cliente);
        $this->datagrid->addColumn($contato);
        $this->datagrid->addColumn($veiculo);
        $this->datagrid->addColumn($placa);
        $this->datagrid->addColumn($premio);
        $this->datagrid->addColumn($saldo);
        $this->datagrid->addColumn($dias);

        // creates the datagrid column actions
        $order_id= new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        //$id->setAction($order_id);

        $order_name= new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        //$nome->setAction($order_name);

        // creates two datagrid actions
        $action1 = new TDataGridAction(array('VeiculoForm', 'onEdit'));
        $action1->setLabel("Ver detalhes do veículo");
        $action1->setImage('fa:car fa-lg');
        $action1->setField('veiculo_id');
        
        $action2 = new TDataGridAction(array('DocumentoForm', 'onEdit'));
        $action2->setLabel("Ver detalhes do documento");
        $action2->setImage('fa:file-o fa-lg');
        $action2->setField('id');

        $action3 = new TDataGridAction(array($this, 'onAvisado'));
        $action3->setLabel('Marcar como avisado');
        $action3->setImage('fa:check fa-lg');
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
    
    public function onAvisado($params)
    {
        try
        {
            TTransaction::open('despachante');

            $documento = new Documento($params['id']);
            $documento->fl_avisado = 't';
            $documento->store();

            TTransaction::close();
        }
        catch(Exception $e)
        {
            new TMessage('info', $e->getMessage());
        }

        $this->onReload();
    }
    
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        TSession::setValue('dash_dias', '');

        if ( $data->dias )
            TSession::setValue('dash_dias', $data->dias);
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
            //FIXME: deixar alteravel pro usuario

            $dias = TSession::getValue('dash_dias') ?: 31;
            // open a transaction with database 'permission'
            TTransaction::open('despachante');
            
            if( ! isset($param['order']) )
                $param['order'] = 'id';
            
            // creates a repository for System_user
            $repository = new TRepository('Documento');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('fl_avisado','=','f'));
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            // load the objects according to criteria
            $objects = $repository->load($criteria);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    $vencimento = $object->getVencimento();
                    
                    $data_vencimento = new DateTime($vencimento->data_vencimento);
                    $data_atual = new DateTime(date('Y-m-d'));

                    $data_diferenca = $data_vencimento->diff($data_atual);

                    //var_dump($object->ano.date('-m-d'));
                    if($data_diferenca->days <= $dias ||
                       $vencimento->data_vencimento < date('Y-m-d'))
                    {
                        $veiculo = new Veiculo($object->veiculo_id);
                        $cliente = new Cliente($veiculo->cliente_id);

                        $object->cliente = $cliente->nome;
                        $object->contato = $cliente->getContato();
                        $object->veiculo = $object->marca_modelo;
                        $object->premio  = "R$ ".number_format($object->premio_total, 2, ",", ".");
                        $object->saldo   = "R$ ".number_format($object->getSaldo(), 2, ",",".");
                        
                        if($vencimento->data_vencimento >= date('Y-m-d'))
                        {
                            if($data_diferenca->days >= 7)
                                $object->dias = "<font color=green>".$data_diferenca->days." dias (".$data_vencimento->format('d/m/Y').")</font>";
                            else
                                $object->dias = "<font color='#FFD600'>".$data_diferenca->days." dias (".$data_vencimento->format('d/m/Y').")</font>";
                        }
                        else
                            $object->dias = "<font color=red>Vencido à {$data_diferenca->days} dias!</font>";

                        // add the object inside the datagrid
                        $this->datagrid->addItem($object);
                    }
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
            $object = new Cidade($key);
     
            if($object->existClientes())
                throw new Exception("Existem clientes cadastrados para esta cidade! Remova-os antes de deletar a cidade");       
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