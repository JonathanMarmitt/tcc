function setInterest(like_id, like_description, dialog_id)
{
	$('#option_hidden').val(like_id)
	$('#option').val(like_description)
	$('#'+dialog_id).dialog('close')
}