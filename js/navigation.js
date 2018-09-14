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

$('.subnav-back').hide();
var subnavH;
// DROPDOWN NAV HOVER
function setMegaNav() {
	if ($(window).width() >= 875) {
		subnavH = $('.nav-menu').outerHeight() - $('.bbg__top-nav__link-text').height();
		if ($('.subnav-back').length == 0) {
			$('.bbg__main-navigation').append('<div class="subnav-back"></div>');
		}

		$('.nav-menu').mouseenter(function() {
			clearTimeout($(this).data('timeoutId'));
			$('.subnav-back').slideDown(200);
			$('.sub-menu').slideDown(200);
		}).mouseleave(function() {
			timeoutId = setTimeout(function() {
				$('.sub-menu').slideUp(200);
				$('.subnav-back').slideUp(200);
			}, 500);
			$('.nav-menu').data('timeoutId', timeoutId);;
		});
		$('.subnav-back').css('height', subnavH);
		$('.sub-menu, .subnav-back').hide();
	}
}
setMegaNav();

// SUB MENU NAV HOVER CONNECTORS
$('.sub-menu li').hover(function() {
	$(this).parent().parent().children('a').css('color', '#999999');
}, function() {
	$(this).parent().parent().children('a').css('color', 'inherit');
});
// THE ABOVE FUNCTION BREAKS CSS HOVER AFTER RUN ONCE FOR EACH UL
// MUST REASSIGN HOVER FUNCTIONALITY VIA JS
$('.menu-item-has-children').hover(function() {
	$(this).children('a').css('color', '#999999');
}, function() {
	$(this).children('a').css('color', '#323a45');
});

$(window).on('resize', function() {
	setMegaNav();
});

}); // END READY
})(jQuery);