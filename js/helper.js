/* Helper Functions
----------------------------------------------------------*/
function changeInputFileBehavior() {
	jQuery('input:file').change(function() {
		var file = jQuery(this).val();
		var filename = file.replace(/^.*[\\\/]/, '');
		jQuery('.attach').show().html('<span class="file"><p>' + filename + '</p><button type="button" class="close">&times;</button></span>');
	});
}

function updateTime() {
	jQuery('.message time').timeago().removeAttr('title');
	setTimeout(updateTime, 60 * 1000);
}

function closeFile() {
	jQuery('.attach').hide();
	//jQuery('input:file').val('');
	//jQuery('input:file').val('').length = 0;
}

function changeHeightChatBlock() {
	var outerHeightWrapHeader = 100;
	var outerHeightControls = jQuery('#wrap').is('.wrap-rooms') ? 65 : 42;
	var outerHeightWell = 79;
	jQuery('.panel-body').height(window.innerHeight - outerHeightWrapHeader);
	jQuery('.messages, .col-content').height(window.innerHeight - outerHeightWrapHeader - outerHeightControls);
	jQuery('.users').height(window.innerHeight - outerHeightWrapHeader - outerHeightControls - outerHeightWell);
}

function chooseOpponent(currentLogin) {
	return currentLogin == 'Quick' ? 'Blox' : 'Quick';
}

function trim(str) {
	if (str.charAt(0) == ' ')
		str = trim(str.substring(1, str.length));
	if (str.charAt(str.length-1) == ' ')
		str = trim(str.substring(0, str.length-1));
	return str;
}

function alertErrors(err) {
	alert(JSON.stringify($.parseJSON(err.detail).errors));
}
function getTime(date) {
    var date = new Date(date);
	    var da = date.getDate();
	    var mon = date.getMonth() + 1; 
	    var yr = date.getFullYear();  
        hours = date.getHours() < 10 ? '0' + date.getHours() : date.getHours(),
        minutes = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();

    return da+'/'+mon+'/'+yr+'-'+hours + ':' + minutes;
}
