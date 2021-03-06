(function($) {// let wordpress use $
$('document').ready(function() {

var container, button, menu, links, subMenus;
container = document.getElementById('site-navigation');
if (!container) {
	return;
}

button = document.getElementById('bbg__menu-toggle');
if ('undefined' === typeof button) {
	return;
}

menu = container.getElementsByTagName('ul')[0];

// Hide menu toggle button if menu is empty and return early.
if ('undefined' === typeof menu) {
	button.style.display = 'none';
	return;
}

menu.setAttribute( 'aria-expanded', 'false' );
if (-1 === menu.className.indexOf('nav-menu')) {
	menu.className += ' nav-menu';
}

button.onclick = function() {
	if (-1 !== container.className.indexOf('toggled')) {
		container.className = container.className.replace(' toggled', '');
		button.setAttribute( 'aria-expanded', 'false');
		menu.setAttribute( 'aria-expanded', 'false');
	} else {
		container.className += ' toggled';
		button.setAttribute('aria-expanded', 'true');
		menu.setAttribute('aria-expanded', 'true');
	}
};

// Get all the link elements within the menu.
links    = menu.getElementsByTagName('a');
subMenus = menu.getElementsByTagName('ul');

// Set menu items with submenus to aria-haspopup="true".
for (var i = 0, len = subMenus.length; i < len; i++) {
	var s = subMenus[i];
	s.parentNode.setAttribute('aria-haspopup', 'true');
}

//* BEGIN BBG CUSTOM SECOND LEVEL NAVIGATION CODE
function levelTwoNav() {
	//allow jQuery click events to bubble up - fixes body nav click issue on iOS
	//TODO: check if Android works by default
	/iP/i.test(navigator.userAgent) && jQuery('*').css('cursor', 'pointer');

	/* enable the carat with the keyboard */
	jQuery('li.menu-item-has-children input[type="image"]').keydown(function(e) {
		/**** enter key on caret toggles the menu at all viewports.  this should only fire on a desktop ****/
		if (e.keyCode == 13) {
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
	while (-1 === self.className.indexOf('nav-menu')) {

		// On li elements toggle the class .focus.
		if ('li' === self.tagName.toLowerCase()) {
			if ( -1 !== self.className.indexOf('focus')) {
				self.className = self.className.replace(' focus', '');
			} else {
				self.className += ' focus';
			}
		}
		self = self.parentElement;
	}
}

// DROPDOWN NAV HOVER
// Create backdrop for sub nav, height determined by tallest sub nav column
// $('.subnav-back').hide();
setTimeout(function() {
	$('.bbg-banner').css('z-index', 0);
}, 700);

// DROPS DOWN THE DESKTOP MENU
// This runs on a class that is dynamically assigned to desktop widths and removed on mobile widths
var subnavH;
var navPad = false;
subnavH = $('.nav-menu').outerHeight() - $('.bbg__top-nav__link-text').height();
function setMegaNav() {
	if ($(window).width() >= 875) {
		$('.sub-menu').hide();
		$('.nav-menu').addClass('desktop-navigation');
		setTimeout(function() {
			$('.desktop-navigation').mouseenter(function() {
				clearTimeout($(this).data('timeoutId'));
				$('.menu-usagm-container').css('padding-bottom', '1.5rem');
				navPad = true;
				$('.sub-menu').slideDown(200);
			}).mouseleave(function() {
				timeoutId = setTimeout(function() {
					$('.sub-menu').slideUp(200);
					$('.menu-usagm-container').css('padding-bottom', '0');
					navPad = false;
				}, 500);
				$('.desktop-navigation').data('timeoutId', timeoutId);
			});
		}, 500);
	}
	else {
		$('.nav-menu').removeClass('desktop-navigation');
		// KEEP SUB-NAV VISIBLE WHILE IN MOBILE WIDTHS
		$('.sub-menu').show();
		$('.nav-menu').off('mouseleave');
		$('.menu-usagm-container').css('padding-bottom', '0');
	}
}

setMegaNav();
$(window).on('resize', function() {
	setMegaNav();
});


// SUB NAV MENU HOVER CONNECTORS
// Highlight parent nav item when hovering on child
$('.sub-menu li').hover(function() {
	$(this).parent().parent().children('a').css('color', '#999999');
}, function() {
	$(this).parent().parent().children('a').css('color', 'inherit');
});
/*
 *  This breaks css hover functionality after first run for each <ul>
 *  Must reassign hover functionality via js
 */
$('.menu-item-has-children').hover(function() {
	$(this).children('a').css('color', '#999999');
}, function() {
	$(this).children('a').css('color', '#323a45');
});

}); // END READY
})(jQuery);

function showProperNav() {
	if (jQuery(window).width() >= 875) {
		jQuery('.sub-menu').hide();
	} else {
		jQuery('.sub-menu').show();
	}
}
showProperNav();