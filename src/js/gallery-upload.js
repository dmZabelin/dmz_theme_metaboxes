jQuery(function ($) {

	var file_frame;

	$(document).on('click', 'a.gallery-add', function (e) {

		e.preventDefault();

		var gallery_list = $(this).data('list');

		if (file_frame) file_frame.close();

		file_frame = wp.media.frames.file_frame = wp.media({
			title: $(this).data('uploader-title'),
			button: {
				text: $(this).data('uploader-button-text'),
			},
			multiple: true
		});

		file_frame.on('select', function () {
			var listIndex = $('#' + gallery_list + '-metabox-list li').index($('#' + gallery_list + '-metabox-list li:last')),
				selection = file_frame.state().get('selection');

			selection.map(function (attachment, i) {
				attachment = attachment.toJSON(),
					index = listIndex + (i + 1);
				$('#' + gallery_list + '-metabox-list').append('<li class="image_holder"><input type="hidden" name="' + gallery_list + '[' + index + ']" value="' + attachment.id + '"><img class="image-preview" src="' + attachment.sizes.thumbnail.url + '"><div class="buttons_manage"><a data-list="' + gallery_list + '" class="change-image button button-primary button-medium" href="#" data-uploader-title="Change image" data-uploader-button-text="Change image"><i class="fa fa-cog" aria-hidden="true"></i></a> <a data-list="' + gallery_list + '" class="remove-image button button-primary button-medium" href="#"><i class="fa fa-trash" aria-hidden="true"></i></a></div></li>');
			});
		});

		makeSortable(gallery_list);

		file_frame.open();
	});

	$(document).on('click', 'a.change-image', function (e) {

		e.preventDefault();

		var that = $(this);

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

			that.parent().parent().find('input:hidden').attr('value', attachment.id);
			that.parent().parent().find('img.image-preview').attr('src', attachment.sizes.thumbnail.url);
		});

		file_frame.open();

	});

	function resetIndex(gallery_list) {
		$('#' + gallery_list + '-metabox-list li').each(function (i) {
			$(this).find('input:hidden').attr('name', gallery_list + '[' + i + ']');
		});

	}

	function makeSortable(gallery_list) {
		$('#' + gallery_list + '-metabox-list').sortable({
			opacity: 0.6,
			stop: function () {
				resetIndex();
			}
		});
	}

	$(document).on('click', 'a.remove-image', function (e) {
		e.preventDefault();
		var gallery_list = $(this).data('list');
		$(this).parent().parent('li').animate({ opacity: 0 }, 200, function () {
			$(this).remove();
			resetIndex(gallery_list);
		});
	});
});