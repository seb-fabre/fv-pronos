function resizeModal()
{
	var initSettingsSize = {
		width: false,
		height: false,
		windowResizing: false
	};
	$.nyroModalSettings(initSettingsSize);
}

function showSpinner()
{
	$('#global_spinner').remove();
	$('body').append($('<div id="global_spinner">'));
	$('#global_spinner').html('<img src="' + spinnerUrl + '" style="left:50%; position:fixed; top:50%; z-index: 10000;"/>');
}

function hideSpinner()
{
	$('#global_spinner').remove();
}

$().ready(function(){
	$.fn.nyroModal.settings.minHeight = 100;
});

function showError(message)
{
	if (!$('form .panel-body'))
	{
		alert(message);
		return;
	}
	
	if ($('#popup_message'))
		$('#popup_message').remove();
	
	$('form .panel-body').prepend($('<p id="popup_message" class="alert alert-danger">' + message + '</p>'));
}