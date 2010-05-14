function resizeModal(width, height)
{
	var initSettingsSize = {
		width: width,
		height: height,
		windowResizing: true
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