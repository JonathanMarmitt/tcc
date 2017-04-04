<?php
/**
 * System_userForm Registration
 * @author  <your name here>
 */
class PagamentoForm extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        // creates the form
        $this->form = new TForm('form_pagamento');
        $this->form->class = 'tform';

        // creates the table container
        $table = new TTable;
        
        $hbox1 = new THBox;
        
        $table->style = 'width: 100%';
        $this->form->add($table);
        
        $table->addRowSet( new TLabel("Pagamentos"), '', '','' )->class = 'tformtitle';
        $table->addRowSet($hbox1);
        
        // add the table inside the form
        $this->form->add($table);
        
        $documento = new TEntry('documento_id');
        $veiculo_desc = new TEntry('veiculo_desc');

        $valor            = new TEntry('valor');
        $data_entrada     = new TDate('data_entrada');
        $data_compensacao = new TDate('data_compensacao');

        $multifield_pagamentos = new TMultiField("pagamentos");
        $multifield_pagamentos->setClass('Pagamento');
        $multifield_pagamentos->addField('valor',           'Valor',            $valor, 250, true);
        $multifield_pagamentos->addField('data_entrada',    'Data',             $data_entrada, 250, true);
        $multifield_pagamentos->addField('data_compensacao','Data Compensação', $data_compensacao, 250, true);
        $multifield_pagamentos->setHeight(200);

        $documento->setSize(80);
        $documento->setEditable(false);
        $veiculo_desc->setEditable(false);
        $veiculo_desc->setSize(400);
        $valor->setSize(80);
        $valor->setNumericMask(2,",",".");
        $data_entrada->setSize(80);
        $data_entrada->setMask("dd/mm/yyyy");
        $data_compensacao->setSize(80);
        $data_compensacao->setMask("dd/mm/yyyy");

        $valor->addValidation("Valor", new TRequiredValidator);
        $data_entrada->addValidation("Data", new TRequiredValidator);
        $data_compensacao->addValidation("Data Compensação", new TRequiredValidator);

        $table->addRowSet(new TLabel('Documento:'), array($documento, $veiculo_desc));

        $row = $table->addRow();
        $row->addCell(new TLabel('Pagamentos'))->style='width:150px';
        $row->addCell($multifield_pagamentos);

        // create an action button (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('fa:floppy-o');
        
        // create an new button (edit with no parameters)
        $new_button=new TButton('new');
        $new_button->setAction(new TAction(array($this, 'onEdit')), _t('New'));
        $new_button->setImage('fa:plus-square green');
        
        $list_button=new TButton('list');
        $list_button->setAction(new TAction(array('DocumentoList','onReload')), _t('Back to the listing'));
        $list_button->setImage('fa:table blue');
        
        // define the form fields
        $this->form->setFields(array($documento,$veiculo_desc,$multifield_pagamentos,$save_button,$new_button,$list_button));
        
        $buttons = new THBox;
        $buttons->add($save_button);
        $buttons->add($new_button);
        $buttons->add($list_button);

        $row=$table->addRow();
        $row->class = 'tformaction';
        $row->addCell( $buttons );

        $container = new TTable;
        $container->style = 'width: 80%';
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml', 'DocumentoList'));
        $container->addRow()->addCell($this->form);

        // add the form to the page
        parent::add($container);
    }

    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    function onSave()
    {
        try
        {
            // open a transaction with database 'permission'
            TTransaction::open('despachante');
            
            // get the form data into an active record System_user
            $objects = $this->form->getData('stdClass');
            
            // form validation
            $this->form->validate();
            
            $documento = new Documento($objects->documento_id);
            $documento->clearPagamentos();

            foreach($objects->pagamentos as $object)
            {
                $object->valor = number_format((float)$object->valor, 2, '.', ',');
                $documento->addPagamento($object); 
            }
            
            $documento->store(); // stores the object
            
            // fill the form with the active record data
            $this->form->setData($objects);
            
            // close the transaction
            TTransaction::close();
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            // reload the listing
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
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                
                // open a transaction with database 'permission'
                TTransaction::open('despachante');
                
                // instantiates object System_user
                $object = new Documento($key);

                $object->documento_id = $object->id;
                $object->veiculo_desc = $object->marca_modelo;
                
                if($object->pagamentos)
                {
                    foreach($object->pagamentos as $pagamento)
                    {
                        $pagamento->valor = number_format($pagamento->valor, 2, ',', '.');; 

                        $entrada = new DateTime($pagamento->data_entrada);
                        $pagamento->data_entrada = $entrada->format('d/m/Y');

                        $compensacao = new DateTime($pagamento->data_compensacao);
                        $pagamento->data_compensacao = $compensacao->format('d/m/Y');
                    }
                }

                // fill the form with the active record data
                $this->form->setData($object);
                
                // close the transaction
                TTransaction::close();
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}
?>