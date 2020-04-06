CKEDITOR.editorConfig = function (config) {
	config.extraPlugins = 'filebrowser';
	config.toolbar = [
		{
			name: 'tools',
			items: ['Maximize', 'ShowBlocks', 'Source']
		},
		{
			name: 'basicstyles',
			groups: ['basicstyles'],
			items: ['Bold', 'Italic', 'Underline', 'Strike']
		},
		{
			name: 'paragraph',
			groups: ['list', 'indent', 'blocks', 'bidi'],
			items: ['NumberedList', 'BulletedList', '-', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock',]
		},
		{
			name: 'colors',
			items: ['TextColor', 'BGColor']
		}
	];
};
