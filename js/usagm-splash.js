(function($) {
$('document').ready(function() {

if ($('#usagm-splash-wrapper').length != 0) {
	var logoSrc = $('.logo-board img.logo').attr('src');
	var imgFromPath = logoSrc.split("/");
	var imgFile = imgFromPath[imgFromPath.length - 1];
	var bbgLogoFileName = imgFromPath[imgFromPath.length - 1];
	var usagmLogoFileName = "usagm-splash-logo.png";
	var usagmLogoPath = logoSrc.replace(imgFromPath[imgFromPath.length - 1], usagmLogoFileName);
	var bbgLogoFilepath = logoSrc.replace(imgFromPath[imgFromPath.length - 1], bbgLogoFileName);

	function getImgFiles() {
		var logoSrc = $('.logo-board img.logo').attr('src');
		var imgFromPath = logoSrc.split("/");
		var imgFileName = imgFromPath[imgFromPath.length - 1];
		return imgFileName;
	}

	function slideScroll() {
		var bbgCopyBottom = $('#bbg-copy').offset().top + $('#bbg-copy').outerHeight();
		var usagmCopyBottom = $('#usagm-copy').offset().top + $('#usagm-copy').outerHeight();
		var shifter;
		if ($(window).scrollTop() < $('#bbg-copy').offset().top) {
			shifter = bbgCopyBottom;
		}
		else {
			shifter = usagmCopyBottom;
		}
		$('html, body').animate({
			scrollTop: shifter
		}, 700);
	}

	// MOVE DOWN ARROW ON HOVER
	var logoPageScroller = $('.splash-down');
	logoPageScroller.hover(function() {
		$(this).animate({'padding-top': '5px'});
	}, function() {
		$(this).animate({'padding-top': '0'});
	});
	logoPageScroller.on('click', function() {
		slideScroll();
	});

	// PARALLAX BBG LOGO
	var topPos = Number($('#bbg-logo-container').offset().top);
	var maxPos = topPos + $('#bbg-logo-container').height();
	var minPos = topPos;
	var posGroup = (maxPos - minPos) * 100;
	var newTopPos;

	function scrollLogoTopPos(elem) {
		var topPos = Number(elem.offset().top);
		var bodyPc = ($(this).scrollTop() / $('body').height());
		newTopPos = ((bodyPc * posGroup) / 100) + minPos;
		elem.css('top', newTopPos);
		// UPDATE VALUES
		topPos = elem.offset().top;
	}
	scrollLogoTopPos($('#bbg-logo-container'));
console.log('scroll: 0');
	$(window).on('scroll', function() {
		scrollLogoTopPos($('#bbg-logo-container'));
	});

	// LIGHTBOX
	var lb = $('.lightbox');
	var lbLinks = $('.lightbox-link');
	var lbLinksRefs = lbLinks.attr('href');
	var lbBg = $('<div class="lb-bg"></div>');
	var lbVideoBox = $('<div id="videoBox"></div>');
	var closeBu = $('<div class="close"><i class="far fa-times-circle"></i></div>');
	var closer = $('.close');

	lbVideoBox.append(closeBu);
	lbBg.append(lbVideoBox);

	var screenTop = 0;
	var screenHeight = 0;
	function setLightboxParams() {
		screenTop = $('html').scrollTop();
		screenHeight = $(window).height();
		lbBg.css({
			'top' : screenTop
		});
	}
	setLightboxParams();

	var refArr = [];
	lbLinks.each(function() {
		refArr.push($(this).attr('href'));
	});

	var i, curImg;

	lbLinks.on('click', function(e) {
		var curId = $(this).attr('id');
		var selImg = $(this);
		var selImgRef = selImg.attr('href');

		lbBg.show();
		closeBu.show();
		lbVideoBox.append(closeBu);
		$('body').prepend(lbBg);
		if (curId == "what-we-do") {
			lbVideoBox.append('<iframe width="560" height="315" src="https://www.youtube.com/embed/_j94Vc-8zyg?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
		}
		else if (curId == "who-we-are") {
			lbVideoBox.append('<iframe width="560" height="315" src="https://www.youtube.com/embed/z4XWcruGhNk?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
		}
		else if (curId == "ceo-message") {
			lbVideoBox.append('<iframe width="560" height="315" src="https://www.youtube.com/embed/eTNV0cnb6No?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
		}
	});

	// CONTROLS
	lbBg.on('click', function() {
		lbBg.hide();
		closeBu.hide();
		lbVideoBox.children().remove();
	});
	closeBu.click(function() {
		lbBg.hide();
		closeBu.hide();
		lbVideoBox.children().remove();
	});

	$(window).on('scroll', function() {
		setLightboxParams();
	});

} // END #usagm-splash-wrapper CHECK

}); // END READY
})(jQuery);