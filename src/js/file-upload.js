jQuery(function ($) {
	var file_frame;
	//var field_id;

	$(document).on('click', 'a.file-add', function (e) {
		e.preventDefault();

		if (file_frame) file_frame.close();

		var field_id = $(this).data('field-id');

		file_frame = wp.media({
			title: $(this).data('uploader-title'),
			button: {
				text: $(this).data('uploader-button-text'),
			},
			multiple: false,
		});

		file_frame.on('select', function () {

			var attachment = file_frame.state().get('selection').first().toJSON();

			if (attachment.type == 'image') {
				$('#' + field_id + '-file-box').find('.file-holder').html(' <input type="hidden" name="' + field_id + '" value="' + attachment.id + '"><span class="file-image-preview file-item"><img src="' + attachment.sizes.thumbnail.url + '"></span><div class="buttons_manage"><a data-field-id="' + field_id + '" class="change-file button button-primary button-medium" href="#" data-uploader-title="Изменить добавленный файл" data-uploader-button-text="Изменить"><i class="fa fa-cog" aria-hidden="true"></i></a><a data-field-id="' + field_id + '" class="remove-file button button-primary button-medium" href="#"><i class="fa fa-trash" aria-hidden="true"></i></a></div>');
			} else {
				$('#' + field_id + '-file-box').find('.file-holder').html(' <input type="hidden" name="' + field_id + '" value="' + attachment.id + '"><div class="file-item"><span class="file-image-preview"><img src="' + attachment.icon + '"></span><div class="file-info"><h4>' + attachment.title + '</h4><strong>Имя файла: </strong><a href="' + attachment.url + '" download>' + attachment.filename + '</a><br><p class="file-size"><strong>Размер файла: </strong>' + attachment.filesizeHumanReadable + '</p></div></div><div class="buttons_manage"><a data-field-id="' + field_id + '" class="change-file button button-primary button-medium" href="#" data-uploader-title="Изменить" data-uploader-button-text="Изменить"><i class="fa fa-cog" aria-hidden="true"></i></a><a data-field-id="' + field_id + '" class="remove-file button button-primary button-medium" href="#"><i class="fa fa-trash" aria-hidden="true"></i></a></div>');
			}

			$('a.' + field_id + '-file-add').parent().addClass('dmz_hidden');

		});

		file_frame.open();

	});

	$(document).on('click', 'a.change-file', function (e) {

		e.preventDefault();

		var field_id = $(this).data('field-id');
		$('.btn-wrap').addClass('dmz_hidden');
		if (file_frame) file_frame.close();

		file_frame = wp.media.frames.file_frame = wp.media({
			title: $(this).data('uploader-title'),
			button: {
				text: $(this).data('uploader-button-text'),
			},
			multiple: false
		});

		file_frame.on('select', function () {
			attachment = file_frame.state().get('selection').first().toJSON();

			if (attachment.type == 'image') {
				$('#' + field_id + '-file-box').find('.file-holder').html(' <input type="hidden" name="' + field_id + '" value="' + attachment.id + '"><span class="file-image-preview file-item"><img src="' + attachment.sizes.thumbnail.url + '"></span><div class="buttons_manage"><a data-field-id="' + field_id + '" class="change-file button button-primary button-medium" href="#" data-uploader-title="Изменить добавленный файл" data-uploader-button-text="Изменить"><i class="fa fa-cog" aria-hidden="true"></i></a><a data-field-id="' + field_id + '" class="remove-file button button-primary button-medium" href="#"><i class="fa fa-trash" aria-hidden="true"></i></a></div>');
			} else {
				$('#' + field_id + '-file-box').find('.file-holder').html(' <input type="hidden" name="' + field_id + '" value="' + attachment.id + '"><div class="file-item"><span class="file-image-preview"><img src="' + attachment.icon + '"></span><div class="file-info"><h4>' + attachment.title + '</h4><strong>Имя файла: </strong><a href="' + attachment.url + '" download>' + attachment.filename + '</a><br><p class="file-size"><strong>Размер файла: </strong>' + attachment.filesizeHumanReadable + '</p></div></div><div class="buttons_manage"><a data-field-id="' + field_id + '" class="change-file button button-primary button-medium" href="#" data-uploader-title="Изменить" data-uploader-button-text="Изменить"><i class="fa fa-cog" aria-hidden="true"></i></a><a data-field-id="' + field_id + '" class="remove-file button button-primary button-medium" href="#"><i class="fa fa-trash" aria-hidden="true"></i></a></div>');
			}
		});

		file_frame.open();

	});

	$(document).on('click', 'a.remove-file', function (e) {
		e.preventDefault();
		var field_id = $(this).data('field-id');
		$(this).parent().remove();
		$('#' + field_id + '-file-box').find('.file-holder').find('input:hidden').val('');
		$('#' + field_id + '-file-box').find('.file-item').remove();
		$('a.' + field_id + '-file-add').parent().removeClass('dmz_hidden');
	});

});