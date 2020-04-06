(function ($) {
	$(document).ready(function () {
		tinymce.init({
			selector: 'textarea.uk-tinymce',
			themes: 'inlite',
			file_browser_callback_types: "file image media",
			plugins: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste"
			],
			toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code | fullscreen",
			link_class_list: [
				{title: 'None', value: ''},
				{title: 'Lightbox', value: 'lightbox'}
			],
			language: window.Laravel.locale,
			image_advtab: true,
			document_base_url: window.Laravel.base_url,
			relative_urls: false,
			remove_script_host: false,
			init_instance_callback: function (editor) {
				var textarea = $('#' + editor.id),
					btnBox = $('<div class="uk-margin-small-top uk-text-right"><button class="uk-button uk-button-small uk-button-danger" data-editor="' + editor.id + '" data-status="1">' + window.Laravel.tinymce.hide_editor + '</button></div>');
				textarea.after(btnBox);
				btnBox.on('click', 'button', function (event) {
					event.preventDefault();
					if ($(this).data('status')) {
						tinymce.EditorManager.execCommand('mceRemoveControl', true, editor.id);
						tinymce.EditorManager.execCommand('mceRemoveEditor', true, editor.id);
						$(this).text(window.Laravel.tinymce.show_editor)
							.data('status', 0)
							.removeClass('uk-button-danger')
							.addClass('uk-button-primary');
					} else {
						$(this).remove();
						tinymce.EditorManager.execCommand('mceAddControl', true, editor.id);
						tinymce.EditorManager.execCommand('mceAddEditor', true, editor.id);
					}
				});
			}
		});
	});
})(jQuery);