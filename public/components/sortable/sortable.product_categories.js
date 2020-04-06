var OleusSettings = window.OleusSettings;

(function ($) {

	$(document).ready(function () {
		var $sortable = $('#list-product-categories .sortable-categories-list'),
			$sortableGroup = $('#list-product-categories'),
			$applyButtonCard = $('#list-product-categories .apply-button'),
			$applyButton = $applyButtonCard.find('a.uk-button'),
			$settingsSortable = {
				group: 'sortable-group'
			};
		UIkit.sortable($sortable, $settingsSortable);
		$sortable.on('stop', function () {
			var $selectedCategory = [];
			$applyButtonCard.removeClass('uk-hidden');
			$sortableGroup.find('.uk-item').each(function () {
				var $itemSortable = $(this),
					$itemParents = $itemSortable.parents('.sortable-categories-list'),
					$item = $itemSortable.find('input.uk-input-selected');
				if ($itemParents.hasClass('applicable')) {
					$item.val(1);
					$selectedCategory.push($item.data('id'));
				} else {
					$item.val(0);
				}
			});
			if ($selectedCategory.length) {
				$applyButton.data('categories', $selectedCategory.join('+'));
			} else {
				$applyButton.data('categories', '');
			}
		})
	});

})(jQuery);