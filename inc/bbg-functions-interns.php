<?php 
	function outputInterns() {
		$qParams = array(
			'post_type' => array('post'),
			'posts_per_page' => 5,
			'category__and' =>  array(
									get_cat_id("Intern Testimonial")
								),
			'orderby', 'date',
			'order', 'DESC'
		);
		$custom_query = new WP_Query($qParams);
		$s = "";
		while ($custom_query->have_posts())  {
			$custom_query->the_post();
			$id = get_the_ID();
			$internName = get_the_title();
			$permalink = get_the_permalink();
			$internFirstName = get_post_meta($id, 'intern_name', true);
			$internLastName = get_post_meta($id, 'intern_name', true);
			$internOffice = get_post_meta($id, 'occupation', true);
			$internSchool = get_post_meta($id, 'intern_school', true);
			$internDate = get_post_meta($id, 'internDate', true);

			$profilePhotoID = get_post_meta($id, 'profile_photo', true);
			$profilePhoto = "";
			if ($profilePhotoID) {
				$profilePhoto = wp_get_attachment_image_src($profilePhotoID , 'mugshot');
				$profilePhoto = $profilePhoto[0];
			}
			$s .= '<article class="full-width-block">';
			$s .= 	'<a href="' . $permalink . '">' . $internOffice . '</a>';
			if ($profilePhoto != "") {
				$s .= '<a href="' . $permalink . '">';
				$s .= '<img class="bbg__mugshot"  src="' . $profilePhoto . '"  />';
				$s .= '</a>';
			}
			$s .= '<p>' . get_the_excerpt() . ' <a href=' . $permalink . '>READ MORE</a></p>';
			if ($internSchool != "") {
				$s .= '<strong>—' . $internName . ',</strong> ' . $internDate . '<br/>' . $internSchool;
			} else {
				$s .= '<strong>—' . $internName . '</strong><br/>' .$internDate;
			}
			$s .= '</article>';
		}

		return $s;
	}
	function intern_list_shortcode() {
		return outputInterns();
	}
	add_shortcode('intern_list', 'intern_list_shortcode');

?>