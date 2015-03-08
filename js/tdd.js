jQuery(document).ready(function($) {

	window.restore_send_to_editor = window.send_to_editor;
	$('.nbs-tdd-media-button').click(function() {  // needs to be a unique name, if registered twice e.g. in a theme, bugs occur
			
		var fieldString = '#' + $(this).attr('rel');
		
	 	tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true&width=640');
		window.send_to_editor = function(html) {
			 imgurl = $('img',html).attr('src');
			 $(fieldString).val(imgurl);
			 tb_remove();
			 window.send_to_editor = window.restore_send_to_editor;
		}; 
		return false;
	});


});