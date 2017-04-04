<?php
class InteressesForm extends TPage
{
    private $table;
    private $form;

    public function __construct()
    {
        parent::__construct();
        
        $this->form = new TForm('form_login');

        $table = $this->table = new TTable();
        $table->class = 'table table-striped';
        $row = $table->addRow();
        $cell = $row->addCell('<b>Listar nos interesses?</b>');
        $cell->width = '200px';

        $cell = $row->addCell('<b>Página</b>');

        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), 'Salvar preferências');
        $save_button->class = 'btn btn-primary';
        
        $this->form->add($table);

        $this->form->setFields(array($save_button));
        
        $box = new TVBox();
        $box->add($this->form);
        $box->add($save_button);

        parent::add($box);
    }
    
    /**
     * Load the data into the datagrid
     */
    function onReload()
    {
        TTransaction::open('ship');

        $people = new People(TSession::getValue('fb-id'));
        $likes = $people->getLikes();
        
        if ($likes)
        {
            $this->table->clearChildren();
            foreach ($likes as $like)
            {
                $chk = new TCheckButton('chk_'.$like->id);
                $chk->setValue(!$like->fl_list); //pq eu ja nao sei.. o componente inverte sozinho

                $row = $this->table->addRow();
                $row->addCell($chk);
                $row->addCell($like->page_name);
            }
        }

        TTransaction::close();
    }
    
    /**
     * shows the page
     */
    function show()
    {
        $this->onReload();
        parent::show();

        TScript::create("$(\"[name*='chk_']\").bootstrapSwitch(
            {size: 'mini',
             onText: 'Sim',
             offText: 'Não',
             'data-inverse' : false});");
    }

    function onSave($params)
    {
        try
        {
            TTransaction::open('ship');

            $people = new People(TSession::getValue('fb-id'));
            $people->setLikesFalse();

            foreach($_POST as $interesse => $value)
            {
                $array = explode('chk_', $interesse);
                
                $int = new PeopleLike($array[1]);
                $int->fl_list = ($value == 'on' ? 't' : 'f');
                $int->store();
            }

            TTransaction::close();

            new TMessage('info', 'registros editados!', new TAction(array($this, 'onReload')));
        }
        catch(Exception $e)
        {
            new TMEssage('error', $e->getMessage());
            return false;
        }
    }
}