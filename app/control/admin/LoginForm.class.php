<?php
/**
 * LoginForm Registration
 * @author  <your name here>
 */
class LoginForm extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct($param)
    {
        parent::__construct();
 
        //require_once('app/templates/theme1/libraries.html');

        //TPage::include_css('app/resources/styles.css');
        $html1 = new THtmlRenderer('app/resources/login.html');
        $html1->enableSection('main', array());
        $html1->show();

        /*$table = new TTable;
        $table->width = '100%';
        // creates the form
        $this->form = new TForm('form_login');
        $this->form->class = 'tform';
        $this->form->style = 'max-width: 450px; margin:auto; margin-top:120px;';

        $intro = new TElement('span');
        $intro->add('Bem vindo a ferramenta que irá te fazer economizar com o frete. Como? Simples! Compras conjuntas!');

        $this->form->add($intro);

        // add the notebook inside the form
        $this->form->add($table);

        $save_button=new TButton('save');
        // define the button action
        $save_button->setAction(new TAction(array($this, 'onLoginFacebook')), 'Continue Com Facebook');
        $save_button->class = 'btn btn-primary';
        $save_button->style = 'font-size:18px;width:90%;padding:10px';

        $row=$table->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $save_button );
        $cell->colspan = 2;
        $cell->style = 'text-align:center';

        $this->form->setFields(array($save_button));

        // add the form to the page
        parent::add($this->form);*/
    }

    /**
     * Autenticates the User
     */
    function onLoginFacebook()
    {
        try
        {
            $fb = new MyFacebook();
            $loginUrl = $fb->login();

            TScript::create("location.href = '{$loginUrl}'");
            //echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';

            /*TTransaction::open('permission');
            $data = $this->form->getData('StdClass');
            $this->form->validate();
            $user = SystemUser::autenticate( $data->login, $data->password );
            if ($user)
            {
                TSession::regenerate();
                $programs = $user->getPrograms();
                $programs['LoginForm'] = TRUE;
                
                TSession::setValue('logged', TRUE);
                TSession::setValue('login', $data->login);
                TSession::setValue('username', $user->name);
                TSession::setValue('frontpage', '');
                TSession::setValue('programs',$programs);
                
                $frontpage = $user->frontpage;
                SystemAccessLog::registerLogin();
                if ($frontpage instanceof SystemProgram AND $frontpage->controller)
                {
                    TApplication::gotoPage($frontpage->controller); // reload
                    TSession::setValue('frontpage', $frontpage->controller);
                }
                else
                {
                    TApplication::gotoPage('EmptyPage'); // reload
                    TSession::setValue('frontpage', 'EmptyPage');
                }
            }
            TTransaction::close();*/
        }
        catch (Exception $e)
        {
            new TMessage('error',$e->getMessage());
            TSession::setValue('logged', FALSE);
            TTransaction::rollback();
        }
    }

    /**
     * Logout
     */
    public static function onLogout()
    {
        SystemAccessLog::registerLogout();
        TSession::freeSession();
        TApplication::gotoPage('LoginForm', '');
    }
}
