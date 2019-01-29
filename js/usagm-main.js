(function($) {
$('document').ready(function() {

// IF NO (MAIN) FEATURED IMAGE, ADD SPACE BELOW NAVBAR
if (!($('#main').prev().hasClass('page-featured-media') || $('#main').prev().hasClass('feautre-banner'))) {
	$('#main').css('padding-top', '6rem');
}

function newHomeMainSpace() {
	if ((top.location.pathname === '/new-homepage-test/')) {
		if ($(window).width() > 685) {
			$('#main').css('padding-top', '6rem');
		}
		else {
			$('#main').css('padding-top', '0');
		}
	}
}
newHomeMainSpace();

$(window).on('resize', function() {
	newHomeMainSpace();
})

// KEEPS FEATURED MEDIA SCALED AT HD PROPORTIONS
function featuredMediaHD() {
	var hd_scale = 1.77778;
	var containerW = $('.page-featured-media').width();
	var dynHeight = containerW / hd_scale;
	if ($('.page-featured-media').children('bbg-banner')) {
		if (($('iframe').length > 0) && ($('iframe').attr('src').indexOf('youtube') != 0)) {
			$('.bbg-banner').width('100%');
			containerW = $('iframe.bbg-banner').width();
		}
	} else {
		$('.bbg-banner').width(containerW);
	}	
	var dynHeight = containerW / hd_scale;
	$('.bbg-banner').height(dynHeight);
}
featuredMediaHD();
$(window).on('resize', function() {
	featuredMediaHD();
});

// KEEPS BACKGROUND IMAGES A CONSISTENT SIZE
function sizeBGimages() {
	var img_scale = 1.77778;
	$.each($('.hd_scale'), function() {
		var containerW = $(this).width();
		var dynHeight = containerW / img_scale;
		$(this).height(dynHeight);
	});
}
sizeBGimages();
$(window).on('resize', function() {
	sizeBGimages();
});

function control_ribbon_height() {
	var ribbonHeight = $('.bbg__ribbon .main-content-container').outerHeight();
	$('.bbg__ribbon .side-content-container div').height(ribbonHeight)
}
if (($('.bbg__ribbon').length > 0) && $(window).width() > 1200) {
	control_ribbon_height();
}

// KEEPS PROFILE LIST DIVS FROM CHANGING SIDES ON RESIZE
// This can probably be taken care of by setting clears in the css for which ever grid this uses
function mgmtProfileSizing() {
	if ($('.mgmt-profile').length > 0) {
		var seniorProfile = $('.mgmt-profile');
		var profileHeights = new Array();

		seniorProfile.css('height', 'auto');
		$.each(seniorProfile, function() {
			profileHeights.push($(this).height())
		});
		var tallestProfileHeight = Math.max.apply(Math, profileHeights);
		seniorProfile.css('height', tallestProfileHeight);
	}
}
mgmtProfileSizing();
$(window).on('resize', function() {
	mgmtProfileSizing();
});

if ($('.page-content').first().tagName == 'P') {
	$('.page-content p').first().attr('class', 'lead-in');
}
$('.page-content').first().on('click', function() {
	if(this.tagName == 'p'){
		alert("It's a p!");
	}
})

// GRID THIRDS
if ($('.grid-third').length > 0) {
	$.each($('.outer-container'), function() {
		var grid_count = $('.grid-third').length;
	});
}

// SIDEBAR ENTITY
function setResponsiveHeight() {
	if ($('.sidebar-entities').length > 0) {
		var entityHeight = $('.entity-image-side').height();
		$('.entity-text-side').css('height', entityHeight);
	}
}
setTimeout(function() {
	setResponsiveHeight();
}, 200);

$(window).on('resize', function() {
	setResponsiveHeight();
})

// MAKE SURE ALL SIDEBAR PARAGRAPHS HAVE CLASS OF ASIDE
if ($('.sidebar-section').length > 0) {
	$.each($('.sidebar-section p'), function() {
		if (!$(this).hasClass('aside')) {
			$(this).addClass('aside');
		}
	});
}


// TEST NEW HOME
var windowWidth = 0;
function resizeEntityBoxes() {
	windowWidth = $(window).width();
	var entityBoxW = $('.network-entity-chunk').width();
	if (windowWidth < 585) {
		$('.network-entity-chunk').height('85px');
		$('.inner-entity').css({
			'height': '85px',
			'width': entityBoxW
		});
		$('.entity-title.entity-voa').text('VOA');
		$('.entity-title.entity-rferl').text('RFE/RL');
		$('.entity-title.entity-ocb').text('OCB');
		$('.entity-title.entity-rfa').text('RFA');
		$('.entity-title.entity-mbn').text('MBN');
	}
	else {
		$('.network-entity-chunk').height(entityBoxW);
		$('.inner-entity').css({
			'height': entityBoxW,
			'width': entityBoxW
		});
		$('.entity-title.entity-voa').text('Voice of America');
		$('.entity-title.entity-rferl').text('Radio Free Europe/Radio Liberty');
		$('.entity-title.entity-ocb').text('Office of Cuba Broadcasting');
		$('.entity-title.entity-rfa').text('Radio Free Asia');
		$('.entity-title.entity-mbn').text('Middle East Broadcasting Network');
	}
}
resizeEntityBoxes();


// FIVE GRID BOX MODULE: Hover
if ($('.grid-box-chunk').length > 0) {
	$('.grid-box-text').hide();
	var overlay = $('div class="grid-box-overlay"><div>');
	overlay.css({
		'width' : $('.grid-box-chunk').width(),
		'height' : '100%'
	});
	$.each($('.grid-box-chunk'), function() {
		$(this).hover(function() {
			$(this).append(overlay);
			$(this).children('.grid-box-text').show();
			overlay.css({
				'background-color' : 'rgba(102, 102, 102, 0.8)',
				'width' : '100%'
			});
		}, function() {
			$('.grid-box-overlay').remove();
			$(this).children('.grid-box-text').hide();
			overlay.css('background-color', 'transparent');
		})
	});
}
// FIVE GRID BOX MODULE: Size
function setFiveGridBox() {
	windowWidth = $(window).width();
	var entityBoxW = $('.grid-box-chunk').width();
	if (windowWidth < 585) {
		$('.grid-box-chunk').height('85px');
	}
	else {
		$('.grid-box-chunk').height(entityBoxW);
	}
}
setFiveGridBox();

function resizePostImage() {
	// SCALE EACH POST IMATE HEIGHT TO 35% OF WIDTH
	var scalePcx = 0.35;
	var dynamicHeight = 0;
	var postImageWidth = 0;
	var postImage = $('#new-home-test .post-image');

	$.each(postImage, function() {
		postImageWidth = $(this).parent().width();
		dynamicHeight = postImageWidth * scalePcx;
		dynamicHeight = postImageWidth - dynamicHeight;
		$(this).height(dynamicHeight);
	});
}
resizePostImage();

function scaleRibbonBanner() {
	setTimeout(function() {
		var ribbonCopyH = $('.bbg__ribbon .main-content-container').outerHeight();
		$('.bbg__ribbon .ribbon-image').css('height', ribbonCopyH);
		if ($(window).width() < 900) {
			$('.bbg__ribbon .side-content-container').css({
				'width' : '100%',
				'margin-left' : '0'
			});
		}
		else {
			$('.bbg__ribbon .side-content-container').css({
				'width' : 'calc(33.33333% - 26.66667px)',
				'margin-left' : '20px'
			});
		}
	}, 200);
}
scaleRibbonBanner();

function scaleCornerHero() {
	var cornerHeroCopy = $('#new-home-test .corner-hero-copy');
	var cornerHeroCopyH = cornerHeroCopy.outerHeight();
	$('#new-home-test .corner-hero .corner-hero-image').height(cornerHeroCopyH);
}
scaleCornerHero();

$(window).on('resize', function() {
	resizeEntityBoxes();
	setFiveGridBox();
	resizePostImage();
	scaleRibbonBanner();
	scaleCornerHero();
});


// PRESS CLIPPINGS DROPDOWN
var clipsListParent = $('.media-clips-entities-dropdown');
var clipsListItems = $('.media-clips-entities-dropdown ul li');
clipsListItems.children('ul').hide();
// TOGGLE
console.log('3');
var touchNestedList = false;
$.each(clipsListItems, function() {
	$(this).on('click', function() {
		if (($(this).children('h6').siblings('ul').css('display') == 'none') && touchNestedList == false) {
			$(this).children('h6').siblings('ul').show();
			$(this).children('h6').children('i').attr('class', 'fas fa-angle-up');
			$(this).css({
				'margin-bottom' : '10px',
				'background-color' : '#f9f9f9'
			});
			$(this).children('ul').on('click', function() {
				touchNestedList = true;
				// SET TIMER TO DISPABLE VARIABLE INCASE USER OPENS LINK IN A NEW TAB, THEY CAN STILL CLOSE DROPDOWN
				setTimeout(function() {
					touchNestedList = false;
				}, 2000);
			})
		} else if (($(this).children('h6').siblings('ul').css('display') != 'none') && (touchNestedList == false)) {
			$(this).children('h6').siblings('ul').hide();
			$(this).children('h6').children('i').attr('class', 'fas fa-angle-down');
			$(this).removeAttr('style');
		}
		// KEEP LIST OPEN IF CLICKING CHILDREN
		// if ($(this).children('ul').on('click', function(e) {
		// 	console.log($(this));
		// }));
	});
})

}); // END READY
})(jQuery);