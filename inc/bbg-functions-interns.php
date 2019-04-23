<?php 
	function outputInterns() {
		$intern_post_query = array(
			'post_type' => array('post'),
			'posts_per_page' => 5,
			'category__and' =>  array(
									get_cat_id('Intern Testimonial')
								),
			'orderby', 'date',
			'order', 'DESC'
		);
		$intern_post_query = new WP_Query($intern_post_query);
		$intern_block = '';
		while ($intern_post_query->have_posts())  {
			$intern_post_query->the_post();
			$id = get_the_ID();
			$internName = get_the_title();
			$permalink = get_the_permalink();
			$internFirstName = get_post_meta($id, 'intern_name', true);
			$internLastName = get_post_meta($id, 'intern_name', true);
			$internOffice = get_post_meta($id, 'occupation', true);
			$internSchool = get_post_meta($id, 'intern_school', true);
			$internDate = get_post_meta($id, 'internDate', true);

			$profilePhotoID = get_post_meta($id, 'profile_photo', true);
			$profilePhoto = '';

			if ($profilePhotoID) {
				$profilePhoto = wp_get_attachment_image_src($profilePhotoID , 'mugshot');
				$profilePhoto = $profilePhoto[0];
			}
			$intern_block .= '<div class="past-intern-block">';
			$intern_block .= 	'<a href="' . $permalink . '">' . $internOffice . '</a>';
			if ($profilePhoto != '') {
				$intern_block .= '<a href="' . $permalink . '">';
				$intern_block .= 	'<img class="bbg__mugshot"  src="' . $profilePhoto . '"  alt="' . $internName . ' Profile Photo">';
				$intern_block .= '</a>';
			}
			$intern_block .= '<p>' . get_the_excerpt() . ' <a class="read-more" href=' . $permalink . '>Read More</a></p>';
			if ($internSchool != '') {
				$intern_block .= '<strong>—' . $internName . ',</strong> ' . $internDate . '<br/>' . $internSchool;
			} else {
				$intern_block .= '<strong>—' . $internName . '</strong><br/>' .$internDate;
			}
			$intern_block .= '</div>';
		}
		return $intern_block;
	}

	function intern_list_shortcode() {
		return outputInterns();
	}
	add_shortcode('intern_list', 'intern_list_shortcode');

?>