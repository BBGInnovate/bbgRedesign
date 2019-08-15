(function($) {
$('document').ready(function() {

// IF NO (MAIN) FEATURED IMAGE, ADD SPACE BELOW NAVBAR
if ($('.feautre-banner').length < 1) {
	$('#site-navigation').css('margin-bottom', '30px');
	$('#site-navigation').append($('<div id="nav-border"></div>'));
	$('#nav-border').css({
		'width' : '95%',
		'height' : '1px',
		'margin' : '15px auto 0 auto',
		'border-bottom' : '1px solid #f1f1f1'
	});
}

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

// PROFILES: CAN'T REMOVE WP_AUTO FILTER FOR JUST THESE SHORTCODES
// THE WP_AUTO FILTER ADDS AN EMPTY PARAGRAPH TO EACH BLOCK, THIS FUNCTION REMOVES THEM
function mgmtProfileSizing() {
	if ($('.profile-clears').length > 0) {
		var memberProfile = $('.profile-clears');
		$.each(memberProfile, function() {
			if ($(this).children().eq(1).is(':empty')) {
				$(this).children().eq(1).remove();
			}
		});
	}
}
mgmtProfileSizing();

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

// MAKE SURE ALL SIDEBAR PARAGRAPHS HAVE CLASS OF P.SANS
if ($('.sidebar-section').length > 0) {
	$.each($('.sidebar-section p'), function() {
		if (!$(this).hasClass('sans')) {
			$(this).addClass('sans');
		}
	});
}


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


// PRESS CLIPPINGS DROPDOWN
var clipsListItems = $('.media-clips-entities-dropdown ul li, .award-dropdown ul li');
clipsListItems.children('ul').hide();
// TOGGLE
var touchNestedList = false;
$.each(clipsListItems, function() {
	$(this).on('click', function() {
		if (($(this).children('ul').css('display') == 'none') && touchNestedList == false) {
			$(this).children('ul').show();
			$(this).children().children('a').children('i').attr('class', 'fas fa-angle-up');
			$(this).css({
				'margin-bottom' : '10px',
				'background-color' : '#f9f9f9'
			});
			$(this).children('ul').on('click', function() {
				touchNestedList = true;
				// SET TIMER TO DISABLE VARIABLE INCASE USER OPENS LINK IN A NEW TAB, THEY CAN STILL CLOSE DROPDOWN
				setTimeout(function() {
					touchNestedList = false;
				}, 2000);
			})
		} else if (($(this).children('h3').siblings('ul').css('display') != 'none') && (touchNestedList == false)) {
			console.log('else');
			$(this).children('ul').hide();
			$(this).children().children('a').children('i').attr('class', 'fas fa-angle-down');
			$(this).removeAttr('style');
		}
	});
});

// SCALE POST-IMAGE TO BE PROPORTIONAL TO 600x400
function scaleArticleImages() {
	var scale = 0.66666667;
	var postImageBox = $('.article-image');

	$.each(postImageBox, function() {
		var curImgDiv = $(this),
			imgDivWidth = curImgDiv.width(),
			imgDivHeight = curImgDiv.height();
		var curImg = curImgDiv.children('a').children('img'),
			imgWidth = curImg.width(),
			imgHeight = curImg.height();
		var curProperHeight = imgDivWidth * scale,
			curProperWidth = curProperHeight / scale;

		if (imgDivHeight != curProperHeight) {
			curImg.css({
				'height' : curProperHeight,
				'width' : curProperWidth
			});
		}
	});
}
scaleArticleImages();


$(window).on('resize', function() {
	scaleRibbonBanner();
	scaleArticleImages();
	setResponsiveHeight();
});

}); // END READY
})(jQuery);