<?php

class GlobalMediaMattersProviders {
    protected $gmmBlogOffset = 0;
    protected $gmmMediaOffset = 0;

    protected $gmmBlogPosts = array();
    protected $gmmMediaPosts = array();

    public function __construct($gmmBlogOffset, $gmmMediaOffset) {
        $this->gmmBlogOffset = $gmmBlogOffset;
        $this->gmmMediaOffset = $gmmMediaOffset;
    }

    function getEntries($numRequested) {
        $entries = array();

        $maxHeap = new class extends SplMaxHeap {
            public function compare($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            }
        };

        $blogPost = $this->popGmmBlog();
        if (!empty($blogPost)) {
            $maxHeap->insert($blogPost);
        }

        $mediaPost = $this->popGmmMedia();
        if (!empty($mediaPost)) {
            $maxHeap->insert($mediaPost);
        }

        for ($i = 0; $i < $numRequested && !$maxHeap->isEmpty(); $i++) {
            $item = $maxHeap->extract();

            $entry = array();
            $entry['type'] = $item['type'];
            $entry['data'] = $item['data'];

            switch ($item['type']) {

                case 'blog':
                    $this->gmmBlogOffset = $item['offset'] + 1;
                    $blogPost = $this->popGmmBlog();
                    if (!empty($blogPost)) {
                        $maxHeap->insert($blogPost);
                    }
                    break;

                case 'youtube_video':
                case 'internal_video':
                case 'image':
                    $this->gmmMediaOffset = $item['offset'] + 1;
                    $mediaPost = $this->popGmmMedia();

                    if (!empty($mediaPost)) {
                        $maxHeap->insert($mediaPost);
                    }
                    break;
            }

            $entries[] = $entry;
        }

        $result['entries'] = $entries;
        $result['gmmBlogOffset'] = $this->gmmBlogOffset;
        $result['gmmMediaOffset'] = $this->gmmMediaOffset;

        return $result;
    }

    function popGmmBlog() {
        if (count($this->gmmBlogPosts) == 0) {

            global $post;

            $qParamsGmmBlog = array(
                'post_type' => array('post'),
                'posts_per_page' => 10,
                'offset' => $this->gmmBlogOffset,
                'order' => 'DESC',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'category',
                        'field' => 'slug',
                        'terms' => array('global-media-matters'),
                    )
                )
            );

            $gmmBlogArray = array();

            $gmmBlogQuery = new WP_Query($qParamsGmmBlog);

            $currentOffset = $this->gmmBlogOffset;

            if ($gmmBlogQuery->have_posts()) {
                while ($gmmBlogQuery->have_posts()) {
                    $gmmBlogQuery->the_post();

                    $gmmBlogItem = array();
                    $gmmBlogItem['title'] = get_the_title();
                    $gmmBlogItem['url'] = get_the_permalink();
                    $gmmBlogItem['date'] = get_the_date();
                    $gmmBlogItem['image'] = get_the_post_thumbnail(get_the_ID(), 'medium-thumb');
                    $gmmBlogItem['excerpt'] = get_the_excerpt();

                    $gmmBlogArray[] = array('date' => get_the_date(), 'type' => 'blog', 'offset' => $currentOffset++, 'data' => $gmmBlogItem);
                }
            }

            $this->gmmBlogPosts = $gmmBlogArray;
        }

        if (count($this->gmmBlogPosts) > 0) {
            return array_shift($this->gmmBlogPosts);
        } else { // No more post found
            return '';
        }
    }

    function popGmmMedia() {
        if (count($this->gmmMediaPosts) == 0) {

            global $post;

            $qParamsGmmMedia = array(
                'post_type' => array('gmm_media'),
                'posts_per_page' => 10,
                'offset' => $this->gmmMediaOffset,
                'order' => 'DESC'
            );

            $gmmMediaArray = array();

            $gmmMediaQuery = new WP_Query($qParamsGmmMedia);

            $currentOffset = $this->gmmMediaOffset;

            if ($gmmMediaQuery->have_posts()) {
                while ($gmmMediaQuery->have_posts()) {
                    $gmmMediaQuery->the_post();

                    $gmmMediaItem = array();
                    $gmmMediaItem['title'] = get_the_title();
                    $gmmMediaItem['date'] = get_the_date();

                    if (have_rows('gmm_media')) {
                        the_row();
                        switch (get_row_layout()) {

                            case 'youtube_video':
                                $youtubeUrl = get_sub_field('url');
                                $youtubeUrl = str_replace('watch?v=', 'embed/', $youtubeUrl);
                                $gmmMediaItem['url'] = $youtubeUrl;

                                $gmmMediaArray[] = array('date' => get_the_date(), 'type' => 'youtube_video', 'offset' => $currentOffset++, 'data' => $gmmMediaItem);
                                break;

                            case 'internal_video':
                                $videoFile = get_sub_field('file');
                                $gmmMediaItem['videoUrl'] = $videoFile['url'];
                                $videoFileParts = explode('.', $gmmMediaItem['videoUrl']);
                                $filetype = end($videoFileParts);
                                $gmmMediaItem['filetype'] = $filetype;
                                $gmmMediaItem['url'] = get_sub_field('link');

                                $gmmMediaArray[] = array('date' => get_the_date(), 'type' => 'internal_video', 'offset' => $currentOffset++, 'data' => $gmmMediaItem);
                                break;

                            case 'image':
                                $image = get_sub_field('image');
                                $imageObj = wp_get_attachment_image_src($image['id'], 'medium-thumb');
                                $gmmMediaItem['imageUrl'] = $imageObj[0];
                                $gmmMediaItem['url'] = get_sub_field('link');
                                $gmmMediaItem['text'] = get_sub_field('text');

                                $gmmMediaArray[] = array('date' => get_the_date(), 'type' => 'image', 'offset' => $currentOffset++, 'data' => $gmmMediaItem);
                                break;
                        }
                    }
                }
            }

            $this->gmmMediaPosts = $gmmMediaArray;
        }

        if (count($this->gmmMediaPosts) > 0) {
            return array_shift($this->gmmMediaPosts);
        } else { // No more post found
            return '';
        }
    }
}