<?php
class MyFacebook
{
	private $fb;

	function __construct()
	{
		$this->fb = new Facebook\Facebook([
              'app_id' => '187549301747003', // Replace {app-id} with your app id
              'app_secret' => '5160fde5ca07947447544278eccca20d',
              'default_graph_version' => 'v2.2',
              ]);
	}

	public function login()
	{
        $helper = $this->fb->getRedirectLoginHelper();

        $permissions = ['email', 'user_likes', 'user_about_me']; // Optional permissions
        
        return $helper->getLoginUrl('http://localhost/ship/fb_login_callback.php', $permissions);
	}

	public function getAccessToken()
	{
		$helper = $this->fb->getRedirectLoginHelper();
		try
		{
		    $accessToken = $helper->getAccessToken();
		}
		catch(Facebook\Exceptions\FacebookResponseException $e) {
		    // When Graph returns an error
		    new TMessage('error', 'Graph returned an error: ' . $e->getMessage());
		    return false;
		}
		catch(Facebook\Exceptions\FacebookSDKException $e) {
		    // When validation fails or other local issues
		    new TMessage('error', 'Facebook SDK returned an error: ' . $e->getMessage());
		    return false;
		}

		TSession::setValue('fb-accesToken', $accessToken);

		return $accessToken;
	}

	public function aboutMe($accessToken)
	{
		try
	    {
	        $response = $this->fb->get('/me?fields=id,name', $accessToken);
	    }
	    catch(Facebook\Exceptions\FacebookResponseException $e) {
	        new TMessage('error', 'Graph returned an error: ' . $e->getMessage());
	        return false;
	    }
	    catch(Facebook\Exceptions\FacebookSDKException $e) {
	        new TMessage('error', 'Facebook SDK returned an error: ' . $e->getMessage());
	        return false;
	    }

	    return $response->getGraphUser();
	}

	public function getMyId($accessToken)
	{
		$me = $this->aboutMe($accessToken);

		return $me['id'];
	}

	public function userLikes($accessToken, $userID)
	{
		try
	    {
	        $response = $this->fb->get("/{$userID}/likes", $accessToken);
	    }
	    catch(Facebook\Exceptions\FacebookResponseException $e) {
	        new TMessage('error', 'Graph returned an error: ' . $e->getMessage());
	        return false;
	    }
	    catch(Facebook\Exceptions\FacebookSDKException $e) {
	        new TMessage('error', 'Facebook SDK returned an error: ' . $e->getMessage());
	        return false;
	    }

	    $likes = array();
	    foreach($response->getDecodedBody()['data'] as $page)
	    {
	        try
	        {
	            $r = $this->fb->get("/".$page['id']."?fields=id,name,category", $accessToken);
	        }
	        catch(Facebook\Exceptions\FacebookResponseException $e) {
	            new TMessage('error', 'Graph returned an error: ' . $e->getMessage());
	            return false;
	        }
	        catch(Facebook\Exceptions\FacebookSDKException $e) {
	            new TMessage('error', 'Facebook SDK returned an error: ' . $e->getMessage());
	            return false;
	        }

	        $likes[] = $r->getGraphObject();
	    }

	    return $likes;
	}

	public function isLoged()
	{
		return TSession::getValue('logged') && TSession::getValue('fb-accesToken');
	}
}