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
 
        $html1 = new THtmlRenderer('app/resources/login.html');
        $html1->enableSection('main', array());
        $html1->show();
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
