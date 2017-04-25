<?php

class Geolocation
{
	public static function setLocation()
	{
		var_dump($_GET);
		TSession::setValue('lat', $_GET['lat']);
		TSession::setValue('lng', $_GET['lng']);
		TSession::setValue('acu', $_GET['acu']);
	}

	public static function getLocation()
	{
		$g = new stdClass;
		$g->lat = TSession::getValue('lat');
		$g->lng = TSession::getValue('lng');
		$g->acu = TSession::getValue('acu');

		return $g;
		/*$params = json_encode(array('considerIp'=>'true'));

			$c = curl_init();

			curl_setopt($c, CURLOPT_URL, $this->urlGeoLocation.$this->APIKEY);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($c, CURLOPT_TIMEOUT, 10);
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($c, CURLOPT_SSLVERSION, 4);
			curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-length: 0'));
			curl_setopt($c, CURLOPT_POST, true);
			//curl_setopt($c, CURLOPT_POSTFIELDS, $params);

			$result = curl_exec($c);
			curl_close($c);

			return json_decode($result);*/
	}
}