(function($) {
    $('document').ready(function() {

        var numOfEntries = 10;
        var gmmBlogOffset = 0;
        var gmmMediaOffset = 0;
        let buttonLoadMoreSelector = '.button__load-more';

        $(buttonLoadMoreSelector).on('click', function() {

            $(this).prop('disabled', true);
            $(this).css('background-color', '#666');

            $.ajax({
                method: 'POST',
                url: ajax_object.ajax_url,
                type: 'JSON',
                data: {
                    numOfEntries: numOfEntries,
                    gmmBlogOffset: gmmBlogOffset,
                    gmmMediaOffset: gmmMediaOffset,
                    action: 'get_more_gmm_entries'
                },
                success: function(response) {

                    if (response.data.entries.length == 0) {
                        return;
                    }

                    $(buttonLoadMoreSelector).prop('disabled', false);
                    $(buttonLoadMoreSelector).css('background-color', '#0071bc');

                    if ('data' in response && 'entries' in response.data) {
                        let entries = response.data.entries;

                        gmmBlogOffset = response.data.gmmBlogOffset;
                        gmmMediaOffset = response.data.gmmMediaOffset;

                        let newEntries = '';

                        entries.forEach(function(entry) {
                            switch (entry.type) {
                                case 'blog':
                                    newEntries += createBlogMarkup(entry.data);
                                    break;

                                case 'youtube_video':
                                    newEntries += createYoutubeVideoMarkup(entry.data);
                                    break;

                                case 'internal_video':
                                    newEntries += createInternalVideoMarkup(entry.data);
                                    break;

                                case 'image':
                                    newEntries += createImageMarkup(entry.data);
                                    break;
                            }
                        });

                        $('.gmm__grid').append(newEntries);

                        setUpVideoPreview('.grid-item');

                        $('.video__internal').on('loadedmetadata', (event) => {
                            redrawBricks();
                        });

                        $('.grid-item').animate({
                            opacity: 1
                        }, 750);

                        setTimeout(function() {
                            redrawBricks();
                        }, 200);
                    }
                }
            });
        });

        function redrawBricks() {
            var elem = document.querySelector('.gmm__grid');
            var msnry = new Masonry(elem, {
                itemSelector: '.grid-item',
                columnWidth: '.grid-item',
                gutter: '.gutter-sizer',
                percentPosition: true
            });
        }

        function createBlogMarkup(data) {
            let markup = '';
            markup += '<div class="grid-item item-blog hidden">';
            markup += '    <div class="grid-item__top">';
            markup += '        <a href="' + data.url + '">';
            markup +=              data.image;
            markup += '        </a>';
            markup += '    </div>';
            markup += '<div class="grid-item__bottom">';
            markup += '    <h4>' + data.date + '</h4>';
            markup += '    <h3>';
            markup += '        <a href="' + data.url + '">';
            markup +=              data.title;
            markup += '        </a>';
            markup += '    </h3>';
            markup += '    <p>';
            markup +=         data.excerpt;
            markup += '    </p>';
            markup += '    </div>';
            markup += '</div>';

            return markup;
        }

        function createYoutubeVideoMarkup(data) {
            let markup = '';
            markup += '<div class="grid-item item-blog hidden">';
            markup += '    <div class="grid-item__top">';
            markup += '        <div class="gmm__media--video-youtube">';
            markup += '            <iframe src="' + data.url + '" frameborder="0" allow="autoplay; encrypted-media;" allowfullscreen>';
            markup += '            </iframe>';
            markup += '        </div>';
            markup += '    </div>';
            markup += '    <div class="grid-item__bottom">';
            markup += '        <h4>' + data.date + '</h4>';
            markup += '        <h3>' + data.title + '</h3>';
            markup += '    </div>';
            markup += '</div>';

            return markup;
        }

        function createInternalVideoMarkup(data) {
            let markup = '';
            markup += '<div class="grid-item item-blog hidden">';
            markup += '    <div class="grid-item__top">';
            markup += '        <div class="gmm__media--video-internal">';
            markup += '            <video class="video__internal" muted autoplay playsinline>';
            markup += '                <source src="' + data.videoUrl + '" type="video/' + data.filetype + '"/>';
            markup += '                Sorry, your browser doesn\'t support embedded videos.';
            markup += '            </video>';
            markup += '        </div>';
            markup += '    </div>';
            markup += '    <div class="grid-item__bottom">';
            markup += '        <h4>' + data.date + '</h4>';
            markup += '        <h3>';
            if ('url' in data && data.url.length != 0) {
                markup += '        <a href="' + data.url + '">';
                markup +=              data.title;
                markup += '        </a>';
            } else {
                markup +=          data.title;
            }

            markup += '        </h3>';
            markup += '    </div>';
            markup += '</div>';

            return markup;
        }

        function createImageMarkup(data) {
            let markup = '';
            markup += '<div class="grid-item item-blog hidden">';
            markup += '    <div class="grid-item__top">';
            markup += '        <a href="' + data.url + '">';
            markup +=             data.image;
            markup += '        </a>';
            markup += '    </div>';
            markup += '    <div class="grid-item__bottom">';
            markup += '        <h4>' + data.date + '</h4>';
            markup += '        <h3>';
            markup += '            <a href="' + data.url + '">';
            markup +=                  data.title;
            markup += '            </a>';
            markup += '        </h3>';
            markup += '        <p>';
            markup +=             data.text;
            markup += '        </p>';
            markup += '    </div>';
            markup += '</div>';

            return markup;
        }

        function setUpVideoPreview(parentClass) {
            let selector = parentClass + ' video';
            $(selector).on('loadeddata', function() {
                this.play();
            });

            $(selector).mouseover(function() {
                this.play();
            });

            $(selector).mouseout(function() {
                this.pause();
            });

            $(selector).on('timeupdate', function() {
                let timeToPlay = 3;
                if (this.currentTime > timeToPlay) {
                    this.currentTime = 0;
                    if (!$(this).is(':hover')) {
                        this.pause();
                    }
                }
            });
        }

        // Load first few items on page load
        $(buttonLoadMoreSelector).trigger('click');

    }); // END READY
})(jQuery);