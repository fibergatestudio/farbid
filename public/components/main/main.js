(function ($) {

	/**
	 * Scroll paginate
	 */
	if ($('#items-list').length && $('#items-list').hasClass('scroll-ajax-paginate') && $('#paginate-box').length) {
		var $docHeight = $(document).height(),
			$winHeight = $(window).height(),
			$scrollToTop = $(window).scrollTop(),
			$moreLink = $('#paginate-box').find('.uk-more-nodes a').length ? $('#paginate-box').find('.uk-more-nodes a') : null;
		$(document).ready(function () {
			if ((($docHeight - $scrollToTop) <= ($scrollToTop + 500)) && $ajaxLoad === false) {
				if ($moreLink) $moreLink.trigger('click');
			}
		});
		$(window).scroll(function () {
			$docHeight = $(document).height();
			$winHeight = $(window).height();
			$scrollToTop = $(window).scrollTop();
			$moreLink = $('#paginate-box').find('.uk-more-nodes a').length ? $('#paginate-box').find('.uk-more-nodes a') : null;
			if ((($docHeight - $scrollToTop) <= ($scrollToTop + 500)) && $ajaxLoad === false) {
				if ($moreLink) $moreLink.trigger('click');
			}
		});
	}

})(jQuery);