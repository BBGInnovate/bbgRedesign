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
		if (window.innerWidth >= 900) {

			function showSubMenu(menuItem) {
				menuItem.addClass('subnav-open');
				menuItem.children('.nav-icon').show();
				menuItem.children('.nav-icon').addClass('fa-angle-up');
				menuItem.children('ul.sub-menu').css('display', 'block');
				menuItem.children('.nav-icon').addClass('displayed-dropdown');
			}
			function hideSubMenu(menuItem) {
				menuItem.removeClass('subnav-open');
				menuItem.children('.nav-icon').removeClass('fa-angle-up');
				menuItem.children('.nav-icon').addClass('fa-angle-down');
				menuItem.children('.nav-icon').hide();
				menuItem.children('ul.sub-menu').css('display', 'none');
				menuItem.children('.nav-icon').removeClass('displayed-dropdown');
			}
			var navHasChild = $('ul#primary-menu li.menu-item-has-children');
			$.each(navHasChild, function() {
				$(this).prepend($('<i class="nav-icon fas fa-angle-down"></i>'));
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

			// SHOW SUB MENU IF ON CHILD PAGE
			var urlPath = window.location.pathname;
			urlPath = urlPath.replace('"', '');
			urlPath = urlPath.slice(1);
			urlPath = urlPath.slice(0, -1);
			urlPath = urlPath.split("/");

			if (urlPath != "") {
				$.each(navHasChild, function() {
					if ($(this).children('a').attr('href').indexOf(urlPath[0]) != -1) {
						showSubMenu($(this));
						// BREAK OUT OF LOOP ONCE CONDITION IS MET
						return false;
					}
				})
			}

			navIcon.on('click', function(e) {
				var icon = $(this);
				var curNavItem = $(this).parents('.menu-item-has-children');
				if ($('.subnav-open').length > 0) {
					navHasChild.not(curNavItem).each(function() {
						hideSubMenu($(this));
					});
				}
				if (curNavItem.hasClass('subnav-open')) {
					hideSubMenu(curNavItem);
				}
				else {
					showSubMenu(curNavItem);
				}
			});
		}
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
			searchField.focus();
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
