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