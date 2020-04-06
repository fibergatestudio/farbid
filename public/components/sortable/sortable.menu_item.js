var OleusSettings = window.OleusSettings;

(function ($) {

	$(document).ready(function () {
		var $sortable = $('.uk-sortable'),
			$settingsSortable = {
				handle: '.uk-sortable-handle'
			};
		UIkit.sortable($sortable, $settingsSortable);
		$sortable.on('stop', function () {
			$sortable.find('.uk-item').each(function () {
				var $itemSortable = $(this);
				$itemSortable.find('input.uk-input-sortable').val($itemSortable.index());
			});
		})
	});

})(jQuery);