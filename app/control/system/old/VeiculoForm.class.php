<?php
/**
 * System_userForm Registration
 * @author  <your name here>
 */
class VeiculoForm extends TPage
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
        $this->form = new TForm('form_veiculo');
        $this->form->class = 'tform';

        // creates the table container
        $table = new TTable;
        $table1 = new TTable;
        //$table2 = new TTable;
        
        $hbox1 = new THBox;
        //$hbox2 = new THBox;
        
        $table->style = 'width: 100%';
        $table1->style = 'width: 100%';
        //$table2->style = 'width: 100%';
        $this->form->add($table);
        
        $table->addRowSet( new TLabel("Veículos"), '', '','' )->class = 'tformtitle';
        $table->addRowSet($hbox1);
        //$table->addRowSet($hbox2);
        
        $hbox1->add($table1);
        //$hbox1->add($table2);
        // add the table inside the form
        $this->form->add($table);
        
        // create the form fields
        $id         = new TEntry('id');
        $nome       = new TEntry('nome');
        $placa      = new TEntry('placa');
        $cor        = new TEntry('cor');
        $marca      = new TEntry('marca');
        $observacao = new TText('observacao');

        $cliente = new TDBSeekButton('cliente_id', 'despachante', 'form_veiculo', 'Cliente', 'nome', 'cliente_id', 'cliente_nome');
        $cliente->setValue(TSession::getValue('veiculo_cliente_id'));
        $cliente->setSize(60);
        $cliente->id = "cliente_id";
        $cliente_nome = new TEntry('cliente_nome');
        $cliente_nome->setEditable(false);
        $cliente_nome->setSize(300);

        $scroll = new TScroll;
        $scroll->setSize(290, 240);

        // outros
        $id->setEditable(false);
        $id->setSize(70);
        $nome->setSize(384);
        $cor->setSize(384);
        $marca->setSize(384);
        $placa->setMask("AAA-9999");
        $placa->setSize(70);
        $observacao->setSize(384);

        // validations
        $nome->addValidation('Nome', new TRequiredValidator);
        $placa->addValidation('Placa', new TRequiredValidator);
        $cor->addValidation('Cor', new TRequiredValidator);
        $marca->addValidation('Marca', new TRequiredValidator);
        $cliente->addValidation('Cliente', new TRequiredValidator);
        
        // add a row for the field id
        $row = $table1->addRow();
        $row->addCell(new TLabel('ID:'))->style='width:150px';
        $row->addCell($id);
        
        $table1->addRowSet(new TLabel('Cliente:'), array($cliente, $cliente_nome));

        $row = $table1->addRow();
        $row->addCell(new TLabel('Nome:'))->style='width:150px';
        $row->addCell($nome);

        $row = $table1->addRow();
        $row->addCell(new TLabel('Placa:'))->style='width:150px';
        $row->addCell($placa);

        $row = $table1->addRow();
        $row->addCell(new TLabel('Cor:'))->style='width:150px';
        $row->addCell($cor);

        $row = $table1->addRow();
        $row->addCell(new TLabel('Marca:'))->style='width:150px';
        $row->addCell($marca);

        $row = $table1->addRow();
        $row->addCell(new TLabel('Observações:'))->style='width:150px';
        $row->addCell($observacao);
        
        // create an action button (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('fa:floppy-o');
        
        // create an new button (edit with no parameters)
        $new_button=new TButton('new');
        $new_button->setAction(new TAction(array($this, 'onEdit')), _t('New'));
        $new_button->setImage('fa:plus-square green');
        
        $list_button=new TButton('list');
        $list_button->setAction(new TAction(array('VeiculoList','onReload')), _t('Back to the listing'));
        $list_button->setImage('fa:table blue');
        
        // define the form fields
        $this->form->setFields(array($id,$nome,$cliente,$placa,$cor,$marca,$observacao,$save_button,$new_button,$list_button));
        
        $buttons = new THBox;
        $buttons->add($save_button);
        $buttons->add($new_button);
        $buttons->add($list_button);

        $row=$table->addRow();
        $row->class = 'tformaction';
        $row->addCell( $buttons );

        $container = new TTable;
        $container->style = 'width: 80%';
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml', 'VeiculoList'));
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
            $object = $this->form->getData('Veiculo');
            
            // form validation
            $this->form->validate();
            
            $object->placa = strtoupper($object->placa);
            $object->store(); // stores the object
                
            $object = new Veiculo($object->id);
            
            if($object->documentos)
            {
                foreach($object->documentos as $documento)
                {
                    $documento->placa        = $object->placa;
                    $documento->cor          = $object->cor;
                    $documento->marca_modelo = $object->nome." / ".$object->marca;

                    $documento->store();
                }
            }

            // fill the form with the active record data
            $this->form->setData($object);
            
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
                $object = new Veiculo($key);
                
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

    public function show()
    {
        parent::show();

        TScript::create("$(document).ready(function(){
            $('#cliente_id').blur()
        });");
    }
}
?>