<?php
/**
 * System_userForm Registration
 * @author  <your name here>
 */
class DocumentoForm extends TPage
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
        $this->form = new TForm('form_documento');
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
        
        $table->addRowSet( new TLabel("Documentos"), '', '','' )->class = 'tformtitle';
        $table->addRowSet($hbox1);
        //$table->addRowSet($hbox2);
        
        $hbox1->add($table1);
        //$hbox1->add($table2);
        // add the table inside the form
        $this->form->add($table);
        
        // create the form fields
        $id             = new TEntry('id');
        $renavam        = new TEntry('renavam');
        $ano            = new TEntry('ano');
        $placa          = new TEntry('placa');
        $placa_ant      = new TEntry('placa_ant_uf');
        $chassi         = new TEntry('chassi');
        $especie        = new TEntry('especie');
        $combustivel    = new TEntry('combustivel');
        $marca_modelo   = new TEntry('marca_modelo');
        $ano_fabricacao = new TEntry('ano_fabricacao');
        $cap_pot_cil    = new TEntry('cap_pot_cil');
        $categoria      = new TEntry('categoria');
        $cor            = new TEntry('cor');
        $premio_total   = new TEntry('premio_total');
        $data_documento = new TDate('data_documento');
        $observacao     = new TText('observacao');

        $nome = new TEntry('nome');
        $nome->setEditable(false);
        $nome->setSize("100%");
        $cpf_cnpj = new TEntry("cpf_cnpj");
        $cpf_cnpj->setEditable(false);

        $veiculo = new TDBSeekButton('veiculo_id', 'despachante', 'form_documento', 'Veiculo', 'nome', 'veiculo_id', 'veiculo_nome');
        $veiculo->setValue(TSession::getValue('documento_veiculo_id'));
        $veiculo->setSize(60);
        $veiculo->id = "veiculo_id";
        
        //$veiculo->setAction(new TAction(array($this, 'onSelect')));

        $veiculo_nome = new TEntry('veiculo_nome');
        $veiculo_nome->setEditable(false);
        $veiculo_nome->setSize(300);

        $scroll = new TScroll;
        $scroll->setSize(290, 240);

        // outros
        $id->setEditable(false);
        $id->setSize(70);
        $cor->setSize(384);
        $renavam->setMask("99999999999999999");
        $marca_modelo->setSize(384);
        $placa->setMask("AAA-9999");
        $placa->setSize(70);
        $ano->setSize(50);
        $ano->setMask("9999");
        $ano_fabricacao->setSize(50);
        $premio_total->setNumericMask(2, ",", ".");
        $ano_fabricacao->setMask("9999");
        $cap_pot_cil->setSize(150);
        $cor->setSize(100);
        $observacao->setSize(384);
        $data_documento->setSize(80);
        $data_documento->setMask("dd/mm/yyyy");

        // validations
        $placa->addValidation('Placa', new TRequiredValidator);
        $cor->addValidation('Cor', new TRequiredValidator);
        $marca_modelo->addValidation('Marca / Modelo', new TRequiredValidator);
        $veiculo->addValidation('Veiculo', new TRequiredValidator);
        
        // add a row for the field id
        $row = $table1->addRow();
        $row->addCell(new TLabel('ID:'))->style='width:150px';
        $row->addCell($id);
        
        $table1->addRowSet(new TLabel('Veículo:'), array($veiculo, $veiculo_nome));

        $row = $table1->addRow();
        $row->addCell(new TLabel('&nbsp;'))->style='width:150px';        

        $table1->addRowSet(new TLabel('Cód. Renavam:'), array($renavam, new TLabel("Ano Exercício"), $ano));

        $row = $table1->addRow();
        $row->addCell(new TLabel('Nome:'))->style='width:150px';
        $row->addCell($nome);

        $table1->addRowSet(new TLabel('CPF/CNPJ:'), array($cpf_cnpj, new TLabel("Placa"), $placa));

        $table1->addRowSet(new TLabel('Placa Ant / UF:'), array($placa_ant, new TLabel("Chassi"), $chassi));

        $table1->addRowSet(new TLabel('Espécie'), array($especie, new TLabel("Combustível"), $combustivel));        

        $table1->addRowSet(new TLabel('Marca / Modelo'), array($marca_modelo, new TLabel("Ano Fab."), $ano_fabricacao));        

        $table1->addRowSet(new TLabel('Cap / Pot / Cil'), array($cap_pot_cil, new TLabel("Categoria"), $categoria, new TLabel("Cor Predominante"), $cor));        

        $row = $table1->addRow();
        $row->addCell(new TLabel('Prêmio Tar. Total:'))->style='width:150px';
        $row->addCell($premio_total);

        $row = $table1->addRow();
        $row->addCell(new TLabel('Observações:'))->style='width:150px';
        $row->addCell($observacao);

        $row = $table1->addRow();
        $row->addCell(new TLabel('Data:'))->style='width:150px';
        $row->addCell($data_documento);        
        
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
        $this->form->setFields(array($id,$veiculo,$renavam,$ano,$placa,$placa_ant,$chassi,$especie,$combustivel,$marca_modelo,$ano_fabricacao,$cap_pot_cil,$categoria,$cor,$premio_total,$data_documento,$observacao,$nome,$cpf_cnpj,$save_button,$new_button,$list_button));
        
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

    public function onSelect($params)
    {
        TTransaction::open('despachante');

        if(Veiculo::find($params['veiculo_id']))
        {
            $veiculo = new Veiculo($params['veiculo_id']);

            $cliente = new Cliente($veiculo->cliente_id);

            $cpf_cnpj = $this->form->getField('ano');
            $cpf_cnpj->value = "asd";
        }

        TTransaction::close();
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
            $object = $this->form->getData('Documento');
            
            // form validation
            $this->form->validate();
            
            //busca se ja existe um documento para o mesmo veiculo e ano
            $repository = new TRepository('Documento');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('veiculo_id','=',$object->veiculo_id));
            $criteria->add(new TFilter('ano','=',$object->ano));
            if($object->id)
                $criteria->add(new TFilter('id','<>',$object->id));
            $documentos = $repository->load($criteria);

            if($documentos)
                throw new Exception("Este veículo já possui documento para o ano ".$object->ano);

            $object->placa = strtoupper($object->placa);

            $object->premio_total = str_replace(".","", $object->premio_total);
            $object->premio_total = str_replace(",",".",$object->premio_total);
            
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
                $object = new Documento($key);

                $veiculo = new Veiculo($object->veiculo_id);
                $cliente = new Cliente($veiculo->cliente_id);

                $object->cpf_cnpj = $cliente->getDocumento();
                $object->nome = $cliente->nome;
                $object->premio_total = number_format($object->premio_total, 2, ",", ".");

                // fill the form with the active record data
                $this->form->setData($object);
                
                // close the transaction
                TTransaction::close();
            }
            elseif(isset($param['veiculo_id']))
            {
                TTransaction::open('despachante');

                $veiculo = new Veiculo($param['veiculo_id']);
                $cliente = new Cliente($veiculo->cliente_id);

                $object = new stdClass;
                $object->veiculo_id = $veiculo->id;
                $object->cpf_cnpj = $cliente->getDocumento();
                $object->nome = $cliente->nome;

                $object->placa = $veiculo->placa;
                $object->marca_modelo = $veiculo->nome." / ".$veiculo->marca;
                $object->cor = $veiculo->cor;

                $this->form->setData($object);

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