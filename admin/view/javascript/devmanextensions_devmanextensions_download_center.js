function open_manual_notification(message, class_custom, icon_class)
{
	if(opencart_version >= 2)
	{
		if(class_custom == 'warning') class_custom = 'danger';
		$('div#content > div.container-fluid div.alert').remove();
		$('div#content > div.container-fluid').prepend('<div class="alert alert-'+class_custom+'"><i class="fa fa-'+icon_class+'-circle"></i> '+message+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
		$('html, body').animate({
	        scrollTop: $("div#content > div.container-fluid > div.alert").offset().top-15
	    }, 800);
	}
	else
	{
		$('div#content div.warning, div#content div.success').remove();
		$('div#content').children('div.breadcrumb').after('<div class="'+class_custom+'">'+message+'</div>');
		$('html, body').animate({
	        scrollTop: $("div#content div."+class_custom).offset().top-15
	    }, 800);
	}
}

function ajax_loading_open() {
  jQuery('body').prepend('<div class="ajax_loading"><i class="fa fa-refresh fa-spin"></i></div>');
}

function ajax_loading_close() {
  jQuery('body div.ajax_loading').fadeOut('fast', function(){
    jQuery(this).remove();
  });
}


function ajax_get_downloads(download_id)
{
	$.ajax({
		url: link_ajax_get_downloads,
		data: {download_id : download_id},
		type: "POST",
		dataType: 'json',
		beforeSend:function()
		{
			ajax_loading_open();
		},
		success: function(data) {
			ajax_loading_close();
			if(!data.error)
			{
				$('div.download_form_container').addClass("downloads_found");
				$('div.download_form_container').html(data.html);
			}
			else
			{
				
				open_manual_notification(data.message, 'warning', 'exclamation');
			}
		},
		error: function(data) {
			ajax_loading_close();
			alert('Error getting downloads.');
		},
	});
}