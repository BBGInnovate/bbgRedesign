/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens and enables tab
 * support for dropdown menus.
 */
(function($) {
	var container, button, menu, links, subMenus;

	container = document.getElementById( 'site-navigation' );
	if ( ! container ) {
		return;
	}

	//button = container.getElementsByTagName( 'button' )[0];
	button = document.getElementById( 'bbg__menu-toggle' );
	if ( 'undefined' === typeof button ) {
		return;
	}

	menu = container.getElementsByTagName( 'ul' )[0];
	//menu = container.getElementsByClassName( 'menu-all-pages-container' )[0];
	
	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	menu.setAttribute( 'aria-expanded', 'false' );
	if ( -1 === menu.className.indexOf( 'nav-menu' ) ) {
		menu.className += ' nav-menu';
	}

	button.onclick = function() {
		if ( -1 !== container.className.indexOf( 'toggled' ) ) {
			container.className = container.className.replace( ' toggled', '' );
			button.setAttribute( 'aria-expanded', 'false' );
			menu.setAttribute( 'aria-expanded', 'false' );
		} else {
			container.className += ' toggled';
			button.setAttribute( 'aria-expanded', 'true' );
			menu.setAttribute( 'aria-expanded', 'true' );
		}
	};

	// Get all the link elements within the menu.
	links    = menu.getElementsByTagName( 'a' );
	subMenus = menu.getElementsByTagName( 'ul' );

	// Set menu items with submenus to aria-haspopup="true".
	for ( var i = 0, len = subMenus.length; i < len; i++ ) {
		var s = subMenus[i];
		s.parentNode.setAttribute( 'aria-haspopup', 'true' );
	}

	/**
	 * BEGIN BBG CUSTOM SECOND LEVEL NAVIGATION CODE
	 */
	function levelTwoNav() {
		
		//allow jQuery click events to bubble up - fixes body nav click issue on iOS
		//TODO: check if Android works by default
		/iP/i.test(navigator.userAgent) && jQuery('*').css('cursor', 'pointer');

		// jQuery("li.menu-item-has-children input.bbg__main-navigation__toggler").on('click', function(e) {
		// 	if (window.innerWidth >= 900) {
		// 		if (!jQuery(this).parent().hasClass('subnav-open')) {
		// 			jQuery("li.menu-item-has-children").removeClass('subnav-open');
		// 			jQuery("ul.sub-menu").css('display', 'none');
		// 			jQuery(this).parent().addClass('subnav-open');
		// 			jQuery(this).parent().find("ul.sub-menu").css('display', 'block');
		// 		}
		// 		else {
		// 			jQuery(this).parent().find("ul.sub-menu").css('display', 'block');
		// 			jQuery(this).parent().removeClass('subnav-open');
		// 		}
		// 		e.stopPropagation();
		// 		e.preventDefault();
		// 	}
		// });

		/* enable the carat with the keyboard */
		jQuery("li.menu-item-has-children input[type='image']").keydown(function(e) {
			/**** enter key on caret toggles the menu at all viewports.  this should only fire on a desktop ****/
			if(e.keyCode == 13) {
				window.enterPressHover = true;
				if (jQuery(this).parent().find("ul.sub-menu").is(':visible')) {
					jQuery(this).parent().find("ul.sub-menu").hide();	
				} else {
					jQuery("ul.sub-menu").hide();
					jQuery(this).parent().find("ul.sub-menu").css('display','block');	
				}
				e.stopPropagation();
				e.preventDefault();
			} else {
				/* tabbing key on caret going backwards hides all */
				if (window.innerWidth >= 900) {
					if (e.which == 9 && e.shiftKey) {
						jQuery('ul.sub-menu').hide();
					}
				}
			}
		});

		// DROPDOWN NAV HOVER
		var navHasChild = $('li.menu-item-has-children');
		$.each(navHasChild, function() {
			$(this).prepend($('<span class="nav-icon dashicons dashicons-arrow-down-alt2"></span>'));
			$('.nav-icon').hide();
		});

		var navIcon = $('.nav-icon');
		navHasChild.hover(function() {
			$(this).children('.nav-icon').show();
		}, function() {
			if (!$(this).hasClass('subnav-open')) {
				$(this).children('.nav-icon').hide();
			}
		});

		navHasChild.on('click', function() {
			if ($('.subnav-open').length > 0) {
				navHasChild.not(this).each(function() {
					$(this).removeClass('subnav-open');
					$(this).children('.nav-icon').removeClass('dashicons-arrow-up-alt2');
					$(this).children('.nav-icon').addClass('dashicons-arrow-down-alt2');
					$(this).children('.nav-icon').hide();
					$(this).children('ul.sub-menu').css('display', 'none');
					$(this).children('.nav-icon').removeClass('displayed-dropdown');
				});
			}
			if ($(this).hasClass('subnav-open')) {
				$(this).removeClass('subnav-open');
				$(this).children('.nav-icon').removeClass('dashicons-arrow-up-alt2');
				$(this).children('.nav-icon').addClass('dashicons-arrow-down-alt2');
				$(this).children('ul.sub-menu').css('display', 'none');
				$(this).children('.nav-icon').removeClass('displayed-dropdown');
			}
			else {
				$(this).addClass('subnav-open');
				$(this).children('.nav-icon').removeClass('dashicons-arrow-down-alt2');
				$(this).children('.nav-icon').addClass('dashicons-arrow-up-alt2');
				$(this).children('ul.sub-menu').css('display', 'block');
				$(this).children('.nav-icon').addClass('displayed-dropdown');
			}
		});
	}
	levelTwoNav();
	/**
	 * END BBG CUSTOM SECOND LEVEL NAVIGATION CODE
	 */


	// Each time a menu link is focused or blurred, toggle focus.
	for ( i = 0, len = links.length; i < len; i++ ) {
		links[i].addEventListener( 'focus', toggleFocus, true );
		links[i].addEventListener( 'blur', toggleFocus, true );
	}

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus() {
		var self = this;

		// Move up through the ancestors of the current link until we hit .nav-menu.
		while ( -1 === self.className.indexOf( 'nav-menu' ) ) {

			// On li elements toggle the class .focus.
			if ( 'li' === self.tagName.toLowerCase() ) {
				if ( -1 !== self.className.indexOf( 'focus' ) ) {
					self.className = self.className.replace( ' focus', '' );
				} else {
					self.className += ' focus';
				}
			}

			self = self.parentElement;
		}
	}

	// NAV SEARCH
	var searchField = $('#search-field-small');
	var searchBu = $('#nav-search-bu');
	var closeSearchIcon = $('.search-icon-close');
	searchField.width('0px');
	searchField.hide();
	closeSearchIcon.hide();
	$('#top-nav-search-form').on('click', function(e) {
		if (searchField.hasClass('search-open')) {
			searchField.removeClass('search-open');
			return true;
		}
		else {
			searchField.addClass('search-open');
			closeSearchIcon.show();
			searchField.show().animate({'width':'50rem'}, 100);
			e.preventDefault();
		}
	});
	searchField.keypress(function(e) {
		if (e.which == 13) {
			$('#top-nav-search-form').submit();
			return false;
		}
	});
	closeSearchIcon.on('click', function() {
		console.log('x');
		searchField.hide();
		$(this).hide();
	});

})(jQuery);
