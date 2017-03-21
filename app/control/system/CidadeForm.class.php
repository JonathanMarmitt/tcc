<?php
/**
 * System_userForm Registration
 * @author  <your name here>
 */
class CidadeForm extends TPage
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
        $this->form = new TForm('form_cidade');
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
        $id                  = new TEntry('id');
        $nome                = new TEntry('nome');
        $estado_nome         = new TEntry('estado_nome');
        $estado_uf           = new TEntry('estado_uf');
        
        $scroll = new TScroll;
        $scroll->setSize(290, 240);
        
        // define the sizes
        //$id->setSize('50%');
        //$nome->setSize('100%');

        // outros
        $id->setEditable(false);
        $estado_uf->setMaxLength(2);
        $estado_uf->setSize(40);
        
        // validations
        $nome->addValidation('Nome', new TRequiredValidator);
        $estado_nome->addValidation('Nome', new TRequiredValidator);
        $estado_uf->addValidation('Nome', new TRequiredValidator);

        // add a row for the field id
        $row = $table1->addRow();
        $row->addCell(new TLabel('ID:'))->style='width:150px';
        $row->addCell($id);
        
        $row = $table1->addRow();
        $row->addCell(new TLabel('Nome:'))->style='width:150px';
        $row->addCell($nome);

        $row = $table1->addRow();
        $row->addCell(new TLabel('Estado:'))->style='width:150px';
        $row->addCell($estado_nome);
        $row->addCell(new TLabel('UF:'))->style='width:150px';
        $row->addCell($estado_uf);
        
        // create an action button (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('fa:floppy-o');
        
        // create an new button (edit with no parameters)
        $new_button=new TButton('new');
        $new_button->setAction(new TAction(array($this, 'onEdit')), _t('New'));
        $new_button->setImage('fa:plus-square green');
        
        $list_button=new TButton('list');
        $list_button->setAction(new TAction(array('CidadeList','onReload')), _t('Back to the listing'));
        $list_button->setImage('fa:table blue');
        
        // define the form fields
        $this->form->setFields(array($id,$nome,$estado_nome,$estado_uf,$save_button,$new_button,$list_button));
        
        $buttons = new THBox;
        $buttons->add($save_button);
        $buttons->add($new_button);
        $buttons->add($list_button);

        $row=$table->addRow();
        $row->class = 'tformaction';
        $row->addCell( $buttons );

        $container = new TTable;
        $container->style = 'width: 80%';
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml', 'CidadeList'));
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
            $object = $this->form->getData('Cidade');
            
            // form validation
            $this->form->validate();
            
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
                $object = new Cidade($key);
                
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