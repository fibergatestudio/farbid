CKEDITOR.editorConfig = function (config) {
	config.extraPlugins = 'filebrowser';
	config.toolbar = [
		{
			name: 'tools',
			items: ['Maximize', 'ShowBlocks']
		},
		{
			name: 'document',
			groups: ['mode', 'document', 'doctools'],
			items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates']
		},
		{
			name: 'basicstyles',
			groups: ['basicstyles', 'cleanup'],
			items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat']
		},
		{
			name: 'paragraph',
			groups: ['list', 'indent', 'blocks', 'align', 'bidi'],
			items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl']
		},
		{name: 'links', items: ['Link', 'Unlink', 'Anchor']},
		{
			name: 'insert',
			items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak']
		},
		{
			name: 'styles',
			items: ['Format', 'Font', 'FontSize']
		},
		{
			name: 'colors',
			items: ['TextColor', 'BGColor']
		}
	];
	config.removeButtons = 'Underline,Subscript,Superscript,about';
	config.format_tags = 'p;h1;h2;h3;pre';
	config.removeDialogTabs = 'image:advanced;link:advanced';

	config.filebrowserBrowseUrl = '/components/ckeditor_sdk/vendor/kcfinder/browse.php?type=files';
	config.filebrowserImageBrowseUrl = '/components/ckeditor_sdk/vendor/kcfinder/browse.php?type=images';
	config.filebrowserFlashBrowseUrl = '/components/ckeditor_sdk/vendor/kcfinder/browse.php?type=flash';
	config.filebrowserUploadUrl = '/components/ckeditor_sdk/vendor/kcfinder/upload.php?type=files';
	config.filebrowserImageUploadUrl = '/components/ckeditor_sdk/vendor/kcfinder/upload.php?type=images';
	config.filebrowserFlashUploadUrl = '/components/ckeditor_sdk/vendor/kcfinder/upload.php?type=flash';
};
