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

        $option_hidden = new THidden('option_hidden');
        $option_hidden->id = 'option_hidden';

        $interest = "";
        if(isset($_GET['s']))
        {
            $option = new TEntry('store_id');
            
            $option->placeholder = 'Store';
            $option_label = new TLabel('Loja');
        }
        else if(isset($_GET['i']))
        {
            $option = new TEntry('interest');
            $option->style = "padding-left: 55px; background-color: #e0e0e0";
            $option->readonly = '1';
            $option_label = new TLabel('Interesse');

            $interest = new TElement('span');
            $interest->class = 'input-group-addon';
            $interest->style = "float:left;width:50px;height:50px;position:absolute;padding:0; cursor: pointer;";
            $interest->add("<span class='glyphicon glyphicon-heart'></span>");
            $interest->onclick = "Adianti.waitMessage = 'Aguarde';
                                  __adianti_post_data('form_login', 'class=BuyForm&method=onInterest&static=1');
                                  return false;";
        }

        $option->class = 'form_field form_field_full';
        $option->id = 'option';
        $option_label->class = 'form_label';

        $people = new TEntry('people_id');
        $people->class = 'form_field form_field_full';
        $people->style = 'background-color: #e0e0e0';
        $people->readonly = '1';
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

        $people->setValue(TSession::getValue('username'));

        //$locker = '<span style="float:left;width:35px;margin-left:45px;height:35px;" class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>';

        $container1 = new TElement('div');
        $container1->add($option_hidden);
        $container1->add($option_label);
        $container1->add($interest);
        $container1->add($option);
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
        $save_button->setAction($action_save = new TAction(array($this, 'onSave')), 'Concluir');
        $save_button->class = 'btn btn-primary';
        $save_button->style = 'font-size:18px;width:90%;padding:10px';
        $action_save->setParameter('s', isset($_GET['s']));
        $action_save->setParameter('i', isset($_GET['i']));

        $row=$table->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $save_button );
        $cell->colspan = 2;
        $cell->style = 'text-align:center';

        $this->form->setFields(array($option, $option_hidden, $people, $date, $min_people, $max_people, $deposite, $save_button));

        // add the form to the page
        parent::add($this->form);
    }

    public static function onChangeNumPeople($params = null)
    {
        var_dump($params);
    }

    public static function onInterest($param = null)
    {
        TTransaction::open('ship');

        $people = new People(TSession::getValue('fb-id'));
        $likes = $people->getLikesToList();
        
        $dialog = new TJQueryDialog;
        $content = new TElement('div');
        if ($likes)
        {
            //$table->clearChildren();
            foreach ($likes as $like)
            {
                //$chk = new TCheckButton('chk_'.$like->id);
                //$chk->setValue(!$like->fl_list); //pq eu ja nao sei.. o componente inverte sozinho

                //$row = $table->addRow();
                //$row->style = 'border: 1px; border-radius = 5px;';
                //$row->addCell($chk);
                //$row->addCell($like->page_name);
                $div = new TElement('div');
                $div->add($like->page_name);
                $div->class = 'btn btn-primary';
                $div->style = 'margin: 2px;';
                $div->onclick = "setInterest('{$like->id}','{$like->page_name}','{$dialog->id}')";
                $content->add($div);
            }
        }

        $dialog->setUseOKButton(FALSE);
        $dialog->setTitle('Escolha o interesse');
        $dialog->setSize(0.8, 0.8);
        $dialog->setModal(TRUE);
        $dialog->{'widget'} = 'T'.'Window';
        //$dialog->add($table);
        $dialog->add($content);
        $dialog->show();

        TTransaction::close();
    }

    /**
     * Autenticates the User
     */
    function onSave($params = null)
    {
        try
        {
            $maps = new Maps;
            //$location = $maps->getGeolocation();

            $data = $this->form->getData('Purshase');

            $this->form->setData($data);

            $non_mandatory = ['store_id', 'option_hidden'];

            $ok = true;
            foreach($data->toArray() as $form_field => $value)
            {
                if(!$value && !in_array($form_field, $non_mandatory))
                {
                    $field = $this->form->getField($form_field);
                    $field->class .= ' form_field_invalid';
                    $field->placeholder = 'Preencha este campo!';

                    $ok = false;
                }
            }

            //FIXME: fazer ou store_id ou option_hidden obrigatorios

            if($ok)
            {
                TTransaction::open('ship');

                $data->date_until = TDate::date2us($data->date_until);
                $data->people_id = TSession::getValue('fb-id');
                $data->status_id = 1; //FIXME: hardcode
                $data->like_id    = $data->option_hidden;
                //$data->maps_address = json_encode($location);

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
