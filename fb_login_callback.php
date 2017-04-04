<?php
require_once 'init.php';
new TSession;

$fb = new MyFacebook;

$accessToken = $fb->getAccessToken();

if(isset($accessToken))
{
    TSession::regenerate();

    $user = $fb->aboutMe($accessToken);
    
    $likes = $fb->userLikes($accessToken);

    TTransaction::open('ship');

    try
    {
        $people = new People();
        $people->id = $user->getId();
        $people->name = $user->getName();
        $people->store();

        foreach($likes as $like)
        {
            $people_like               = new PeopleLike();
            $people_like->id           = $like->getProperty('id');
            $people_like->people_id    = $people->id;
            $people_like->page_name    = $like->getProperty('name');
            $people_like->category     = $like->getProperty('category');
            $people_like->page_picture = $like->getProperty('picture')->getProperty('url');
            $people_like->store();
        }
    }
    catch(Exception $e)
    {
        new TMessage('error', $e->getMessage());
        return false;
    }

    TTransaction::close();

    $programs = ['Dashboard' =>'Dashboard',
                 'InteressesForm'=>'Meus Interesses',
                 'Config'    =>'Configurações da Conta',
                 'Pessoas'   =>'Pessoas em Comum',
                 'OptionScreen' => 'Comprar'];

    $programs['LoginForm'] = TRUE;
    
    TSession::setValue('logged', TRUE);
    TSession::setValue('username', $user->getName());
    TSession::setValue('fb-id', $user->getId());
    TSession::setValue('frontpage', '');
    TSession::setValue('programs',$programs);
    
    //$frontpage = $user->frontpage;
    TScript::create("location.href = 'index.php?class=OptionScreen'");
    
    //AdiantiCoreApplication::gotoPage('EmptyPage'); // reload
    TSession::setValue('frontpage', 'EmptyPage');
}