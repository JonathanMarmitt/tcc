<?php
/**
 * LoginForm Registration
 * @author  <your name here>
 */
class BuyForm extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct($param)
    {
        parent::__construct();

        $table = new TTable;
        $table->style = "border: 0 !important;";
        $table->width = '100%';
        // creates the form
        $this->form = new TForm('form_login');
        $this->form->class = 'tform';
        $this->form->style = 'max-width: 450px;';

        $intro = new TElement('span');
        $intro->add('Bem vindo a ferramenta que irá te fazer economizar com o frete. Como? Simples! Compras conjuntas!');

        $this->form->add($intro);
        $this->form->add($table);

        $store = new TEntry('store_id');
        $store->class = 'form_field form_field_full';
        $store_label = new TLabel('Loja');
        $store_label->class = 'form_label';

        $people = new TEntry('people_id');
        $people->class = 'form_field form_field_full';
        $people_label = new TLabel('Usuário');
        $people_label->class = 'form_label';
        
        $date = new TDate('date_until');
        $date->class = 'form_field';
        $date->setSize('87%');
        $date->setMask('dd/mm/yyyy');
        $date_label = new TLabel('Data até');
        $date_label->class = 'form_label';

        $min_people = new TSlider('min_people');
        $min_people->setRange(1,10,1);
        $min_people->setSize('100%');
        //$min_people->setChangeAction(new TAction($this, 'onChangeNumPeople'));
        $min_people_label = new TLabel('Mín. Pessoas');
        $min_people_label->class = 'form_label';

        $max_people = new TSlider('max_people');
        $max_people->setRange(1,10,1);
        $max_people->setSize('100%');
        //$max_people->class = 'form_field';
        $max_people_label = new TLabel('Max. Pessoas');
        $max_people_label->class = 'form_label';

        $deposite = new TText('deposite_information');
        //$deposite->class = 'form_field';
        $deposite->setSize('100%', '100');
        $deposite->style = "font-size: 20px; border-radius: 5px !important;";
        $deposite_label = new TLabel('Informações para depósito');
        $deposite_label->class = 'form_label';

        //$row=$table->addRow();
        //$row->addCell( new TLabel('Compra') )->colspan = 2;
        //$row->class='tformtitle';

        $store->placeholder = 'Store';

        $people->setValue(TSession::getValue('username'));

        //$user = '<span style="float:left;width:35px;margin-left:45px;height:35px;" class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>';
        //$locker = '<span style="float:left;width:35px;margin-left:45px;height:35px;" class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>';

        $container1 = new TElement('div');
        $container1->add($store_label);
        $container1->add($store);
        $container1->add($people_label);
        $container1->add($people);
        $container1->add($date_label);
        $container1->add($date);
        $container1->add($min_people_label);
        $container1->add($min_people);
        $container1->add($max_people_label);
        $container1->add($max_people);
        $container1->add($deposite_label);
        $container1->add($deposite);

        $row=$table->addRow();
        $row->addCell($container1);//->colspan = 2;

        $save_button=new TButton('save');
        // define the button action
        $save_button->setAction(new TAction(array($this, 'onSave')), 'Salvar');
        $save_button->class = 'btn btn-primary';
        $save_button->style = 'font-size:18px;width:90%;padding:10px';

        $row=$table->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $save_button );
        $cell->colspan = 2;
        $cell->style = 'text-align:center';

        $this->form->setFields(array($store, $people, $date, $min_people, $max_people, $deposite, $save_button));

        // add the form to the page
        parent::add($this->form);
    }

    public static function onChangeNumPeople($params = null)
    {
        var_dump($params);
    }

    /**
     * Autenticates the User
     */
    function onSave($params = null)
    {
        try
        {
            $data = $this->form->getData('Purshase');

            $this->form->setData($data);

            $ok = true;
            foreach($data->toArray() as $form_field => $value)
            {
                if(!$value)
                {
                    $field = $this->form->getField($form_field);
                    $field->class .= ' form_field_invalid';
                    $field->placeholder = 'Preencha este campo!';

                    $ok = false;
                }
            }

            if($ok)
            {
                TTransaction::open('ship');

                $data->date_until = date('Y-m-d', strtotime($data->date_until));
                $data->people_id = TSession::getValue('fb-id');
                $data->status_id = 1; //FIXME: hardcode

                $data->store();

                TTransaction::close();
                
                new TMessage('info', 'Compra inserida com sucesso!');
            }
        }
        catch (Exception $e)
        {
            new TMessage('error',$e->getMessage());
            TTransaction::rollback();
        }
    }
}
