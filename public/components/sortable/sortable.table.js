var OleusSettings = window.OleusSettings;

(function ($) {

	$(document).ready(function () {
		var $sortable = $('.uk-sortable'),
			$sortableForm = $sortable.parents('form'),
			$settingsSortable = {
				handle: '.uk-sortable-handle'
			};
		UIkit.sortable($sortable, $settingsSortable);
		$sortable.on('stop', function () {
			$sortable.find('tr').each(function () {
				var $itemSortable = $(this);
				$itemSortable.find('input.uk-input-sortable').val($itemSortable.index());
				$sortableForm.find('button[type="submit"]').removeClass('uk-hidden');
			});
		})
	});

})(jQuery);