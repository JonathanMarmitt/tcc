<?php
/**
 * System_userForm Registration
 * @author  <your name here>
 */
class ClienteForm extends TPage
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
        $this->form = new TForm('form_cliente');
        $this->form->class = 'tform';

        // creates the table container
        $table = new TTable;
        $table1 = new TTable;
        $table2 = new TTable;
        
        $hbox1 = new THBox;
        $hbox2 = new THBox;
        
        $table->style = 'width: 100%';
        $table1->style = 'width: 100%';
        $table2->style = 'width: 100%';
        $this->form->add($table);
        
        $table->addRowSet( new TLabel(_t('User')), '', '','' )->class = 'tformtitle';
        $table->addRowSet($hbox1);
        $table->addRowSet($hbox2);
        
        $hbox1->add($table1);
        $hbox1->add($table2);
        // add the table inside the form
        $this->form->add($table);
        
        // create the form fields
        $id               = new TEntry('id');
        $nome             = new TEntry('nome');
        $cidade           = new TDBSeekButton('cidade_id', 'despachante', 'form_cliente', 'Cidade', 'nome', 'cidade_id', 'cidade_nome');
        $cpf              = new TEntry('cpf');
        $rg               = new TEntry('rg');
        $cnpj             = new TEntry('cnpj');
        $email            = new TEntry('email');
        $rua              = new TEntry('rua');
        $numero           = new TEntry('numero');
        $complemento      = new TEntry('complemento');
        $telefone_fixo    = new TEntry('telefone_fixo');
        $telefone_celular = new TEntry('telefone_celular');

        $cidade_desc = new TEntry('cidade_nome');
        $cidade_desc->setEditable(false);
        $cidade_desc->setSize(300);

        $rua->setSize(400);
        $numero->setSize(60);
        $complemento->setSize(531);

        $scroll = new TScroll;
        $scroll->setSize(290, 240);
        
        // define the sizes
        //$id->setSize('50%');
        //$nome->setSize('100%');

        // outros
        $id->setEditable(false);
        $id->setSize(60);
        $cpf->setMask('999.999.999-99');
        $cnpj->setMask('99.999.999/9999-99');
        
        // validations
        $nome->addValidation('Nome',   new TRequiredValidator);
        //$cpf->addValidation('CPF',     new TCPFValidator);
        //$cnpj->addValidation('CNPJ',   new TCNPJValidator);
        $email->addValidation('Email', new TEmailValidator);

        // add a row for the field id
        $row = $table1->addRow();
        $row->addCell(new TLabel('ID:'))->style='width:150px';
        $row->addCell($id);
        
        $row = $table1->addRow();
        $row->addCell(new TLabel('Nome:'))->style='width:150px';
        $row->addCell($nome);

        $table1->addRowSet(new TLabel('Cidade:'), array($cidade, $cidade_desc));

        $row = $table1->addRow();
        $cell = $row->addCell("<font size=4><b>Preencha apenas uma das duas linhas abaixo:<b></font>");
        $cell->colspan = 2;
        $cell->style = "width:100%";

        $table1->addRowSet(new TLabel('CPF:'), array($cpf, new TLabel('RG:'), $rg));

        $row = $table1->addRow();
        $row->addCell(new TLabel('CNPJ:'))->style='width:150px';
        $row->addCell($cnpj);

        $table1->addRowSet("&nbsp;", array());

        $table1->addRowSet(new TLabel('Fixo:'), array($telefone_fixo, new TLabel('Celular:'), $telefone_celular));

        $row = $table1->addRow();
        $row->addCell(new TLabel('Email:'))->style='width:150px';
        $row->addCell($email);

        $table1->addRowSet($label_rua = new TLabel('Rua:'), array($rua, new TLabel('Número:'), $numero));
        $row = $table1->addRow();
        $row->addCell(new TLabel('Complemento'))->style='width:150px';
        $row->addCell($complemento);

        // create an action button (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('fa:floppy-o');
        
        // create an new button (edit with no parameters)
        $new_button=new TButton('new');
        $new_button->setAction(new TAction(array($this, 'onEdit')), _t('New'));
        $new_button->setImage('fa:plus-square green');
        
        $list_button=new TButton('list');
        $list_button->setAction(new TAction(array('ClienteList','onReload')), _t('Back to the listing'));
        $list_button->setImage('fa:table blue');
        
        // define the form fields
        $this->form->setFields(array($id,$nome,$cidade,$cpf,$rg,$cnpj,$email,$rua,$numero,$complemento,$telefone_fixo,$telefone_celular,$save_button,$new_button,$list_button));
        
        $buttons = new THBox;
        $buttons->add($save_button);
        $buttons->add($new_button);
        $buttons->add($list_button);

        $row=$table->addRow();
        $row->class = 'tformaction';
        $row->addCell( $buttons );

        $container = new TTable;
        $container->style = 'width: 80%';
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml', 'ClienteList'));
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
            $object = $this->form->getData('Cliente');
            
            // form validation
            $this->form->validate();
            
            if($object->cnpj && ($object->cpf || $object->rg))
                throw new Exception("Preencha apenas CNPJ ou RG e CPF!");

            if(!$object->cnpj && (!$object->cpf || !$object->rg))
                throw new Exception("Preencha RG e CPF!");

            $object->cpf  = str_replace(array('.','-'), "", $object->cpf);
            $object->cnpj = str_replace(array('.','-','/'), "", $object->cnpj);

            $object->store(); // stores the object
            
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
                $object = new Cliente($key);
                
                if($object->cpf)
                    $object->cpf = Cliente::formatCPF($object->cpf);

                if($object->cnpj)
                    $object->cnpj = Cliente::formatCNPJ($object->cnpj);

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