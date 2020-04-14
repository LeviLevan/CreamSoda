(function ($) {

	"use strict";

	var xhr = null;
	var JetCWSettings = window.JetCWSettings;
	var compareMaxItems = JetCWSettings.compareMaxItems;
	var compareItemsCount = JetCWSettings.compareItemsCount;

	var JetCW = {

		init: function () {

			var self = JetCW;

			$(document)
				.on('click.JetCW', '.jet-compare-button__link[href="#"]', self.addToCompare)
				.on('click.JetCW', '.jet-wishlist-button__link[href="#"]', self.addToWishlist)
				.on('click.JetCW', '.jet-compare-item-remove-button', self.removeFromCompare)
				.on('click.JetCW', '.jet-wishlist-item-remove-button', self.removeFromWishlist)
				.on('jet-cw-load', self.addLoader)
				.on('jet-cw-loaded', self.removeLoader);

		},

		removeFromCompare: function (e) {
			e.preventDefault();

			var $scope = $(this),
				productID = $scope.data('product-id');

			if (xhr) {
				xhr.abort();
			}

			$(document).trigger(
				'jet-cw-load',
				[$scope, productID, 'jet_update_compare_list']
			);

			xhr = JetCW.ajaxRequest($scope, 'jet_update_compare_list', 'remove', productID);

		},

		removeFromWishlist: function (e) {
			e.preventDefault();

			var $scope = $(this),
				productID = $scope.data('product-id');

			if (xhr) {
				xhr.abort();
			}

			$(document).trigger(
				'jet-cw-load',
				[$scope, productID, 'jet_update_wish_list']
			);

			xhr = JetCW.ajaxRequest($scope, 'jet_update_wish_list', 'remove', productID);

		},

		addToWishlist: function (e) {
			e.preventDefault();

			var $scope = $(this),
				productID = $scope.data('product-id');

			if (xhr) {
				xhr.abort();
			}

			$(document).trigger(
				'jet-cw-load',
				[$scope, productID, 'jet_update_wish_list']
			);

			xhr = JetCW.ajaxRequest($scope, 'jet_update_wish_list', 'add', productID);

		},

		addToCompare: function (e) {
			e.preventDefault();

			if (compareItemsCount >= compareMaxItems) {
				JetCW.showMessages('compare_max_items');
				return;
			}

			var $scope = $(this),
				productID = $scope.data('product-id');

			if (xhr) {
				xhr.abort();
			}

			$(document).trigger(
				'jet-cw-load',
				[$scope, productID, 'jet_update_compare_list']
			);

			xhr = JetCW.ajaxRequest($scope, 'jet_update_compare_list', 'add', productID);

		},

		ajaxRequest: function ($scope, action, context, productID) {

			$.ajax({
				url: JetCWSettings.ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: action,
					pid: productID,
					context: context,
					widgets_data: JetCWSettings.widgets,
				},
			}).done(function (response) {
				compareItemsCount = response.compareItemsCount;

				JetCW.renderResult(response);

				$(document).trigger(
					'jet-cw-loaded',
					[$scope, productID, action]
				);

			});

		},

		renderResult: function (response) {
			var content = response.content;

			$.each(content, function (selector, html) {
				$(selector).replaceWith(html);
			});

		},

		addLoader: function (event, $scope, productID, action) {

			if( 'jet_update_compare_list' === action ){
				$('a.jet-compare-button__link[data-product-id="' + productID + '"]').addClass('jet-cw-loading');
				$('div.jet-compare-table__wrapper').addClass('jet-cw-loading');
				$('a.jet-compare-count-button__link').addClass('jet-cw-loading');
			}

			if( 'jet_update_wish_list' === action ){
				$('a.jet-wishlist-button__link[data-product-id="' + productID + '"]').addClass('jet-cw-loading');
				$('a.jet-wishlist-count-button__link').addClass('jet-cw-loading');
				$('div.jet-wishlist__content').addClass('jet-cw-loading');
			}

		},

		removeLoader: function ($scope, productID, action) {

			if( 'jet_update_compare_list' === action ){
				$('a.jet-compare-button__link[data-product-id="' + productID + '"]').removeClass('jet-cw-loading');
				$('div.jet-compare-table__wrapper').removeClass('jet-cw-loading');
				$('a.jet-compare-count-button__link').removeClass('jet-cw-loading');
			}

			if( 'jet_update_wish_list' === action ){
				$('a.jet-wishlist-button__link[data-product-id="' + productID + '"]').removeClass('jet-cw-loading');
				$('a.jet-wishlist-count-button__link').removeClass('jet-cw-loading');
				$('div.jet-wishlist__content').removeClass('jet-cw-loading');
			}

		},

		showMessages: function (message) {
			var compareMessage = $('.jet-compare-message--max-items');

			if ('compare_max_items' === message) {
				compareMessage.addClass('show');

				setTimeout(function () {
					compareMessage.removeClass('show');
				}, 4000);
			}

		}

	};

	JetCW.init();

}(jQuery));