function setInterest(like_id, like_description, dialog_id)
{
	$('#option_hidden').val(like_id)
	$('#option').val(like_description)
	$('#'+dialog_id).dialog('close')
}

function onPurshase(purshase_id)
{
	__adianti_ajax_exec('class=PurshaseControl&method=onPurshase&purshase_id='+purshase_id,'alert',true)
}

function onCancelPurshase(purshase_id)
{
	__adianti_ajax_exec('class=PurshaseControl&method=onCancelPurshase&purshase_id='+purshase_id,'alert',true)
}

function isGeolocationAvailable()
{
	if ("geolocation" in navigator)
	{
  		return true;	
	}
	else
	{
	  	alert("I'm sorry, but geolocation services are not supported by your browser.");
	}
}

function setGeolocation()
{
	if(isGeolocationAvailable())
	{
		var options = {
	  		enableHighAccuracy: true,
	  		timeout: 5000,
	  		maximumAge: 0
		};

		function success(pos) {
		  var crd = pos.coords;

		  console.log('Sua posição atual é:');
		  console.log('Latitude : ' + crd.latitude);
		  console.log('Longitude: ' + crd.longitude);
		  console.log('Mais ou menos ' + crd.accuracy + ' metros.');

		  __adianti_ajax_exec('class=Geolocation&method=setLocation&static=1&lat='+crd.latitude+'&lng='+crd.longitude+'&acu='+crd.accuracy,'alert',true)
		  //return {'lat': crd.latitude, 'lng': crd.longitude, 'acu': crd.accuracy};
		};

		function error(err) {
		  	switch(err.code)
		    {
		    	case 1:
		    		showDivLocation();
		    	break;
		    }
		  console.warn('ERROR(' + err.code + '): ' + err.message);
		};

		navigator.geolocation.getCurrentPosition(success, error, options);
	}
}

function showDivLocation()
{
	alert('Mostrar div de pedir permissao para geolocation')
}