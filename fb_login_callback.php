<?php
require_once 'init.php';
new TSession;

$fb = new MyFacebook;

$accessToken = $fb->getAccessToken();

if (isset($accessToken))
{
    TSession::regenerate();

    $user = $fb->aboutMe($accessToken);

    //$likes = $fb->userLikes($accessToken, $user->getId());
    
    //$programs = $user->getPrograms();
    //$programs['LoginForm'] = TRUE;
    
    TSession::setValue('logged', TRUE);
    TSession::setValue('username', $user->getName());
    TSession::setValue('frontpage', '');
    //TSession::setValue('programs',$programs);
    
    //$frontpage = $user->frontpage;
    TScript::create("location.href = 'index.php?class=OptionScreen'");
    
    //AdiantiCoreApplication::gotoPage('EmptyPage'); // reload
    TSession::setValue('frontpage', 'EmptyPage');
}