function setInterest(like_id, like_description, dialog_id)
{
	$('#option_hidden').val(like_id)
	$('#option').val(like_description)
	$('#'+dialog_id).dialog('close')
}

function onPurshase(purshase_id)
{
	fields = 'Link do produto: <input class="tfield" name="link" id="link" type="text" style="width:380px;"><br>';
	fields += 'Preço: <input class="tfield" name="price" id="price" type="number" style="width:100px;">';

	bootbox.dialog({
      	title: "Participar da compra",
      	message: '<div>'+
                '<span class="fa fa-fa fa-question-circle fa-5x blue" style="float:left"></span>'+
                '<span display="block" style="margin-left:20px;float:left">'+fields+'</span>'+
                '</div>',
      	buttons: {
        	ok: {
          		label: "OK",
          		className: "btn-default",
          		callback: function() {
					$('#link').css('border', '1px solid rgb(204, 204, 204)')
					$('#price').css('border', '1px solid rgb(204, 204, 204)')
		            
		            if($('#link').val() == "")
		            {
						$('#link').css('border', '1px solid rgb(255, 0, 0)').attr('placeholder', 'Obrigatório');
						return false;
					}
					else if($('#price').val() == "")
					{
						$('#price').css('border', '1px solid rgb(255, 0, 0)').attr('placeholder', 'Obrigatório');
						return false;	
					}
					else
					{
						link_val = $('#link').val()
						price_val = $('#price').val()
						
						__adianti_ajax_exec('class=PurshaseControl&method=onPurshase&purshase_id='+purshase_id+'&link='+link_val+'&price='+price_val,'alert',true)
		            	
		            	this.close();
		            }
          		}
        	},
        cancel: {
          	label: "Cancel",
          	className: "btn-default",
          	callback: function() {
            	this.close();
          	}
        }
      }
    });
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

function changemaps(field)
{
	$('#maps-script').remove()
	
	if($('#maps-script')[0] == undefined)
		__adianti_ajax_exec('class=StoreBuy&method=refresh&store_id=1&val='+$(field).val(),true)
	else
		console.log($('#maps-script')[0])
}

function calcDistance(lat1,lng1,lat2,lng2)
{
	coordA = new google.maps.LatLng(lat1, lng1);
    coordB = new google.maps.LatLng(lat2, lng2);

    return google.maps.geometry.spherical.computeDistanceBetween(coordA, coordB);
}