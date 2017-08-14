<?php
require_once 'init.php';

class TApplication extends AdiantiCoreApplication
{
    static public function run($debug = FALSE)
    {
        new TSession;
        if ($_REQUEST)
        {
            $ini  = parse_ini_file('application.ini');
            $class = isset($_REQUEST['class']) ? $_REQUEST['class'] : '';
            $public = in_array($class, $ini['public_classes']);

            if (TSession::getValue('logged')) // logged
            {
                $programs = (array) TSession::getValue('programs'); // programs with permission
                $programs = array_merge($programs, array('Adianti\Base\TStandardSeek' => TRUE, 'LoginForm' => TRUE, 'AdiantiMultiSearchService' => TRUE, 'AdiantiUploaderService' => TRUE, 'EmptyPage' => TRUE, 'MessageList'=>TRUE, 'SearchBox' => TRUE)); // default programs
                
                /*if( isset($programs[$class]) )
                {
                    parent::run($debug);
                }
                else
                {
                    new TMessage('error', _t('Permission denied') );
                }*/
                if( TSession::getValue('logged') )
                    parent::run($debug);
                else
                    AdiantiCoreApplication::loadPage('LoginForm');
            }
            else if ($class == 'LoginForm' or $public)
            {
                parent::run($debug);
            }
            else
            {
                new TMessage('error', _t('Permission denied'), new TAction(array('LoginForm','onLogout')) );
            }
        }
    }
}

TApplication::run(TRUE);
