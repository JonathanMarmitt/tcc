<?php
class Maps
{
	# MAPS reference: https://developers.google.com/maps/documentation/javascript/3.exp/reference
	# GEOLOCATION reference: https://developers.google.com/maps/documentation/geolocation/intro?hl=pt_BR
	private $APIKEY = "AIzaSyCrx7-YBJzau8P3uhhYc3iHhiJKDyMFtIo";
	private $urlGeoLocation = "https://www.googleapis.com/geolocation/v1/geolocate?key=";

	## actual user
	private $lat;
	private $lng;
	private $accuracy;

	## marks
	private $marks;

	public $height;
	public $width;

	#limit
	private $limit;

	function __construct()
	{
		
			$location = Geolocation::getLocation();

			$this->lat = $location->lat;
			$this->lng = $location->lng;
			$this->accuracy = $location->acu;

			//$this->addMark($this->lat, $this->lng, "Tu mesmo bixo!");

			//$this->width = '800px';
			$this->height = '400px';

			$this->html_maps = new THtmlRenderer('app/resources/views/google-maps.html');
		
	}

	public function setSize($height, $width)
	{
		$this->height = $height;
		$this->width = $width;
	}

	public function addMarkYouAreHere()
	{
		$this->addMark($this->lat, $this->lng, null, "Tu mesmo bixo!");
	}

	public function addMark($lat, $lng, $purshase_id = null, $description = null)
	{
		$m = new stdClass;
		$m->lat = $lat;
		$m->lng = $lng;

		if($purshase_id)
		{	
			##FIXME: remover essa logica daqui?
			$purshase = new Purshase($purshase_id);
			$store = new Store($purshase->store_id);
			$people = new People($purshase->people_id);

			$m->people = $people;
			$m->store = $store;
			$m->purshase = $purshase;
			$m->description = "";
		}
		else if($description)
		{
			$m->people = null;
			$m->store = null;
			$m->purshase = null;
			$m->description = $description;
		}

		$this->marks[] = $m;
	}

	public function setLimit($l)
	{
		$this->limit = $l;
	}

	/*public function getGeolocation()
	{
		try
		{
			$params = json_encode(array('considerIp'=>'true'));

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

			return json_decode($result);


		}
		catch(Exception $e)
		{
			new TMessage('error', $e->getMessage());
			return false;
		}
	}*/

	private function getInfoWindowHtml($purshase = null, $people = null, $store = null, $description = null)
	{	
		if($purshase)
		{
			$date_until = TDate::date2br($purshase->date_until);

			$html = <<<HTML
				<div><b>Comprador</b>: $people->name</div>
				<div><b>Loja</b>: $store->description</div>
				<div><b>Disponível até</b>: $date_until</div>
				<hr>
				<div>
HTML;
			if($purshase_with = $purshase->hasPeople(TSession::getValue('fb-id')))
				$html .= "<button class='btn btn-warning' onclick='onCancelPurshase({$purshase->id})'>Cancelar</button>";
			else
				$html .= "<button class='btn btn-success' onclick='onPurshase({$purshase->id})'>Participar</button>";

			$html .= <<<HTML
				</div>
HTML;
		}
		else if($description)
		{
			$precisao = TSession::getValue('acu');
			$html = <<<HTML
			<div>$description</div>
			<div>Precisão: {$precisao} metros</div>
HTML;
		}
	
		return str_replace("\n", "", $html);
	}

	private function scripts()
	{
		$script = <<<HTML
		<script async defer
          src='https://maps.googleapis.com/maps/api/js?key={$this->APIKEY}&callback=initMap&v=3&libraries=geometry'>
        </script>
HTML;

        $script .= <<<HTML
         		<script type="text/javascript">
			    var map;
			    function initMap()
			    {
			      	map = new google.maps.Map(document.getElementById('map'), {
			        	center: {lat: $this->lat, lng: $this->lng},
			        	zoom: 15,
			        	scrollwheel: false,
			        	streetViewControl: false
		      	});
			    //deixando o infowindow aqui, apenas um é aberto no mapa, jogando ele dentro do for abre sempre um novo
			    var infowindow = new google.maps.InfoWindow();

HTML;
				if($this->marks)
				{
					foreach($this->marks as $mark)
					{
						$html = $this->getInfoWindowHtml($mark->purshase, $mark->people, $mark->store, $mark->description);

						$script .= <<<HTML
						
						distance = calcDistance($this->lat,$this->lng,$mark->lat,$mark->lng);

						if(distance < $this->limit)
						{
							var marker = new google.maps.Marker({
								map: map,
								position : {lat: $mark->lat, lng: $mark->lng},
								opacity: 0.7
							});
											
							var content = "{$html}"
							content += "<div>Distancia:"+distance+"</div>"
							
							google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){ 
							    return function() {
							        infowindow.setContent(content);
							        infowindow.open(map,marker);
							    };
							})(marker,content,infowindow));
						}
HTML;
					}
				}

		$script .= <<<HTML
				}
			</script>
HTML;

        return $script;
	}

	function show()
	{
		$this->addMarkYouAreHere();

		echo $this->scripts();

		$replaces = array();
		// trocar tamanho pq vai ser responsivo, add no css
		$replaces['height'] = $this->height;
		//$replaces['width'] = $this->width;
		$replaces['lat'] = $this->lat;
		$replaces['lng'] = $this->lng;

        $this->html_maps->enableSection('main', $replaces);
        $this->html_maps->show();
	}
}