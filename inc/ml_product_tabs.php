<?php
if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
	
	class mlpt_shortcode_tabs {
		public function __construct($instance, $array, $post_id) {
			/* begin or end */
			$this->instance = $instance;
			$this->mlpt_array = $array;
			$this->tabs_id = $post_id;
		}
		
		public function get_mime_type($type) {
			switch ($type) {
				case 'image/jpeg':
				case 'image/jpg':
				case 'image/jpe':
				case 'image/png':
				case 'image/gif':
				case 'image/bmp':
				case 'image/tif':
				case 'image/tiff':
				case 'image/ico':
					return 'image'; break;
				case 'video/mpeg':
				case 'video/mp4': 
				case 'video/quicktime':
				case 'video/avi':
				case 'video/divx':
				case 'video/x-flv': 
				case 'video/ogg':
				case 'video/webm': 
				case 'video/x-matroska':
				case 'video/x-ms-wmv':
					return 'video'; break;
				case 'text/csv':
				case 'text/plain': 
				case 'text/xml':
					return 'document'; break;
				case 'audio/mpeg':
				case 'audio/wav': 
				case 'audio/ogg':
					return 'audio'; break;
				default:
					return 'image';
			}
		}
		
		public function return_media($id, $use) {
			$html = '';
			$i = 0;
			$attachments = explode(',',$id);
							
			foreach($attachments as $attachment) {
				$type = get_post_mime_type($attachment);
				$this_mime = $this->get_mime_type($type);
				if($this_mime == 'image') {
					$thumb = $attachment;
					if($use == 'icon') {
						$image = vt_resize( $thumb, '', 80, 80, true );
						$html .= '<img class="mlpt_img_icon" src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" style="width:'.$image['width'].'px; height="'.$image['height'].'px;" />';
					}
					if($use == 'full_size') {
						$image = wp_get_attachment_image_src( $thumb, 'full' );
						$html .= '<img class="mlpt_img_full" src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" />';
					}				
				}
				if($this_mime == 'audio') {
					$src = wp_get_attachment_url( $attachment );
					$html .= '<audio id="mlpt_audio_'.$i.'" class="mlpt_audio_element" controls>
						<source src="'.$src.'" type="'.$type.'">
						Your browser does not support the audio element.
						</audio>';
				}
				if($this_mime == 'video') {
					$src = wp_get_attachment_url( $attachment );
					$html .= '<video id="mlpt_video_'.$i.'" class="mlpt_video_element" controls>
							<source src="'.$src.'" type="'.$type.'">
							Your browser does not support the video tag.
							</video>';
				}
				$i++;
			}
			return $html;
		}
		
		public function output_array_concert($start, $num) {
			$a = $this->mlpt_array;
			$color_array = get_post_meta( $this->tabs_id, '_mlpt_colors', false );
			$mlpt_highest = get_post_meta( $this->tabs_id, '_mlpt_highest', true );
			$mlpt_limit = get_post_meta( $this->tabs_id, '_mlpt_limit_height', true );
			$mlpt_prev = get_post_meta( $this->tabs_id, '_mlpt_prevent', true ) != '' ? get_post_meta( $this->tabs_id, '_mlpt_prevent', true ) : '';
			$i = 0;
			/* Start output */
			$html = '<div id="mlpt_shortcode_info_'.$this->instance.'" class="mlpt_shortcode_info mlpt-'.$this->tabs_id.'" data-tabs="'.$this->tabs_id.'" data-highest="'.$mlpt_highest.'" data-limit="'.$mlpt_limit.'" data-prev="'.$mlpt_prev.'">';
			if(is_array($color_array) && count($color_array > 0)) {
				$html .= '<style type="text/css">';
				$html .= '.mlpt-'.$this->tabs_id.' ul li > a {background:'.$color_array[0]['background'].' !important; color:'.$color_array[0]['font'].' !important;}';
				$html .= '.mlpt-'.$this->tabs_id.' ul li a.conc-active {background:'.$color_array[0]['hover_background'].' !important; color:'.$color_array[0]['hover_font'].' !important;}';
				$html .= '.mlpt-'.$this->tabs_id.' ul li > a:hover {background:'.$color_array[0]['hover_background'].' !important; color:'.$color_array[0]['hover_font'].' !important;}';
				$html .= '.mlpt-'.$this->tabs_id.' ul li > a i {color:'.$color_array[0]['icon'].' !important;}';
				$html .= '.mlpt-'.$this->tabs_id.' .mlpt-inner-div {background:'.$color_array[0]['div'].' !important; color:'.$color_array[0]['div_font'].' !important;}';
				$html .= '.mlpt-'.$this->tabs_id.' .mlpt-inner-div a {color:'.$color_array[0]['div_link'].' !important;}';
				$html .= '.mlpt-'.$this->tabs_id.' .mlpt-inner-div a:hover {color:'.$color_array[0]['div_link_hover'].' !important;}';
				$html .= '.mlpt-'.$this->tabs_id.' .mlpt-slider-vert.ui-slider-vertical .ui-slider-handle {background:'.$color_array[0]['hover_background'].' !important;}';
				$html .= '</style>';
			}
			$html .= '<ul id="mlpt_shortcode_conc_'.$this->instance.'" data-instance="'.$this->instance.'" class="mlpt_shortcode_conc">';
					foreach($a as $key => $value) {
						$num = $num < count($a) ? $num : $count($a);
						$class = ( $key == ($num - 1) && $start == 'open' ) ? 'conc-active' : '';
						$html .= '<li class="li_'.$class.'"><a href="#" class="mlpt-header '.$class.'"><i class="'.$value['icon'].'"></i>'.$value['title'].'</a>';
						
						$html .= '<div id="mlpt_inner_'.$this->instance.'_div_'.$i.'" class="mlpt-inner-div '.$class.'" data-num="'.$i.'" data-set="'.$this->instance.'">';
						$html .= '<div id="mlpt_inner_limit_'.$i.'" class="mlpt-inner-limit">';
						$html .= '<p class="info_div_info">'.$value['info'].'</p>';
						$html .= '</div>';
						$html .= '</div>';
						$html .= '</li>';
						$i++;
					}
			$html .= '</ul>';
			$html .= '</div>';
			
			return apply_filters( 'the_content', $html );
		}
		
		public function output_array($start, $num) {
		
			$a = $this->mlpt_array;
			$color_array = get_post_meta( $this->tabs_id, '_mlpt_colors', false );
			$i = 0;
			/* Start output */
			$html = '<div id="mlpt_shortcode_info_'.$this->instance.'" class="mlpt_shortcode_info mlpt-'.$this->tabs_id.'" data-tabs="'.$this->tabs_id.'">';
			if(is_array($color_array) && count($color_array > 0)) {
				$html .= '<style type="text/css">';
				$html .= '.mlpt-'.$this->tabs_id.' ul li > a {background:'.$color_array[0]['background'].' !important; color:'.$color_array[0]['font'].' !important;}';
				$html .= '.mlpt-'.$this->tabs_id.' ul li a.active {background:'.$color_array[0]['hover_background'].' !important; color:'.$color_array[0]['hover_font'].' !important;}';
				$html .= '.mlpt-'.$this->tabs_id.' ul li > a:hover {background:'.$color_array[0]['hover_background'].' !important; color:'.$color_array[0]['hover_font'].' !important;}';
				$html .= '.mlpt-'.$this->tabs_id.' ul li > a i {color:'.$color_array[0]['icon'].' !important;}';
				$html .= '.mlpt-'.$this->tabs_id.' ul.mlpt_shortcode_tabs {border-right:1px solid '.$color_array[0]['top_div'].' !important;}';
				$html .= '.mlpt-'.$this->tabs_id.' #mlpt_shortcode_info_divs_'.$this->instance.' {background:'.$color_array[0]['div'].' !important; color:'.$color_array[0]['div_font'].' !important; border-top: 5px solid '.$color_array[0]['top_div'].';}';
				$html .= '.mlpt-'.$this->tabs_id.' #mlpt_shortcode_info_divs_'.$this->instance.' a {color:'.$color_array[0]['div_link'].' !important;}';
				$html .= '.mlpt-'.$this->tabs_id.' #mlpt_shortcode_info_divs_'.$this->instance.' a:hover {color:'.$color_array[0]['div_link_hover'].' !important;}';
				$html .= '</style>';
			}
			$html .= '<ul id="mlpt_shortcode_tabs_'.$this->instance.'" data-instance="'.$this->instance.'" class="mlpt_shortcode_tabs">';
					foreach($a as $key => $value) {
						$class = ( $key == ($num - 1) ) ? 'active' : '';
						$html .= '<li class="li_'.$class.'"><a href="#" class="'.$class.'"><i class="'.$value['icon'].'"></i>'.$value['title'].'</a></li>';
						$i++;
					}
			$html .= '</ul>';
			$i = 0;
			$html .= '<div id="mlpt_shortcode_info_divs_'.$this->instance.'" class="mlpt_shortcode_info_divs">';
					foreach($a as $key => $value) {
						$class = ( $key == ($num - 1) ) ? 'div_active' : '';
						$html .= '<div class="mlpt-inner-div '.$class.'">';
						$html .= '<p class="info_div_info">'.$value['info'].'</p></div>';
						$i++;
					}
			$html .= '</div>';
			$html .= '</div>';
			return apply_filters( 'the_content', $html );
		}
	}
	
	class mlpt_action_tabs {
		
		public static $instance = 0;
		
		public function __construct($array, $id) {
			/* begin or end */
			$this->id = $id;
			$this->mlpt_array = $array;
		}
		
		public function mlpt_add_action($action) {
			add_action( $action, array( $this, 'mlpt_action_post' ), 10 );
		}
		
		public function mlpt_action_post() {
			global $wp_query;
			$post_id = $wp_query->post->ID;
			
			$mlpt_exclude = get_post_meta( $this->id, '_mlpt_exclude_text', true ) != '' ? explode(',', get_post_meta( $this->id, '_mlpt_exclude_text', true )) : '';
			$mlpt_include = get_post_meta( $this->id, '_mlpt_include_text', true ) != '' ? explode(',', get_post_meta( $this->id, '_mlpt_include_text', true )) : '';
			$mlpt_type = get_post_meta( $this->id, '_mlpt_action_type', true ) != '' ? get_post_meta( $this->id, '_mlpt_action_type', true ) : 'normal';
			if(($mlpt_exclude != '' && in_array($post_id, $mlpt_exclude)) || ($mlpt_include != '' && !in_array($post_id, $mlpt_include))) {
			}
			else {
				$mlpt_type != 'normal' ? $this->output_array_concert() : $this->output_array();
			}
		}
		
		public function get_mime_type($type) {
			switch ($type) {
				case 'image/jpeg':
				case 'image/jpg':
				case 'image/jpe':
				case 'image/png':
				case 'image/gif':
				case 'image/bmp':
				case 'image/tif':
				case 'image/tiff':
				case 'image/ico':
					return 'image'; break;
				case 'video/mpeg':
				case 'video/mp4': 
				case 'video/quicktime':
				case 'video/avi':
				case 'video/divx':
				case 'video/x-flv': 
				case 'video/ogg':
				case 'video/webm': 
				case 'video/x-matroska':
				case 'video/x-ms-wmv':
					return 'video'; break;
				case 'text/csv':
				case 'text/plain': 
				case 'text/xml':
					return 'document'; break;
				case 'audio/mpeg':
				case 'audio/wav': 
				case 'audio/ogg':
					return 'audio'; break;
				default:
					return 'image';
			}
		}
		
		public function return_media($id, $use) {
			$attachments = explode(',',$id);
			$html = '';
			foreach($attachments as $attachment) {
				$type = get_post_mime_type($attachment);
				$this_mime = $this->get_mime_type($type);
				if($this_mime == 'image') {
					$thumb = $attachment;
					if($use == 'icon') {
						$image = vt_resize( $thumb, '', 80, 80, true );
						$html .= '<img class="mlpt_img_icon" src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" style="width:'.$image['width'].'px; height="'.$image['height'].'px;" />';
					}
					if($use == 'full_size') {
						$image = wp_get_attachment_image_src( $thumb, 'full' );
						$html .= '<img class="mlpt_img_full" src="'.$image[0].'" />';
					}				
				}
				if($this_mime == 'audio') {
					$url = wp_get_attachment_url($attachment);
					$html .= '<audio class="mlpt_single_audio_element" controls>
								<source src="'.$url.'" type="'.$type.'">
								Your browser does not support the audio element.
							</audio>';
				}
				if($this_mime == 'video') {
					$url = wp_get_attachment_url($attachment);
					$html .= '<video class="mlpt_single_video_element" controls>
								<source src="'.$url.'" type="'.$type.'">
								Your browser does not support the video tag.
							</video>';
				}
			}
			return $html;
		}
		
		public function output_array_concert() {
			self::$instance++;
			$a = $this->mlpt_array;
			$color_array = get_post_meta( $this->id, '_mlpt_colors', false );
			$start = get_post_meta( $this->id, '_mlpt_props_start', true ) != '' ? get_post_meta( $this->id, '_mlpt_props_start', true ) : 'open';
			$num = get_post_meta( $this->id, '_mlpt_props_num', true ) != '' ? get_post_meta( $this->id, '_mlpt_props_num', true ) : 1;
			$mlpt_highest = get_post_meta( $this->id, '_mlpt_highest', true ) != '' ? get_post_meta( $this->id, '_mlpt_highest', true ) : '';
			$mlpt_limit = get_post_meta( $this->id, '_mlpt_limit_height', true ) != '' ? get_post_meta( $this->id, '_mlpt_limit_height', true ) : '';
			$mlpt_prev = get_post_meta( $this->id, '_mlpt_prevent', true ) != '' ? get_post_meta( $this->id, '_mlpt_prevent', true ) : '';
			$i = 0;
			/* Start output */
			$html = '<div id="mlpt_action_info_'.self::$instance.'" class="mlpt_action_info mlpt-action-'.$this->id.'" data-tabs="'.$this->id.'" data-highest="'.$mlpt_highest.'" data-limit="'.$mlpt_limit.'" data-prev="'.$mlpt_prev.'">';
			if(is_array($color_array) && count($color_array > 0)) {
				$html .= '<style type="text/css">';
				$html .= '.mlpt-action-'.$this->id.' ul li > a {background:'.$color_array[0]['background'].' !important; color:'.$color_array[0]['font'].' !important;}';
				$html .= '.mlpt-action-'.$this->id.' ul li a.conc-active {background:'.$color_array[0]['hover_background'].' !important; color:'.$color_array[0]['hover_font'].' !important;}';
				$html .= '.mlpt-action-'.$this->id.' ul li > a:hover {background:'.$color_array[0]['hover_background'].' !important; color:'.$color_array[0]['hover_font'].' !important;}';
				$html .= '.mlpt-action-'.$this->id.' ul li > a i {color:'.$color_array[0]['icon'].' !important;}';
				$html .= '.mlpt-action-'.$this->id.' .mlpt-inner-div {background:'.$color_array[0]['div'].' !important; color:'.$color_array[0]['div_font'].' !important;}';
				$html .= '.mlpt-action-'.$this->id.' .mlpt-inner-div a {color:'.$color_array[0]['div_link'].' !important;}';
				$html .= '.mlpt-action-'.$this->id.' .mlpt-inner-div a:hover {color:'.$color_array[0]['div_link_hover'].' !important;}';
				$html .= '</style>';
			}
			$html .= '<ul id="mlpt_action_conc_'.self::$instance.'" data-instance="'.self::$instance.'" class="mlpt_action_conc">';
			
					foreach($a as $key => $value) {
						$class = ( $key == ($num - 1) && $start == 'open' ) ? 'conc-active' : '';
						$html .= '<li class="li_'.$class.'"><a href="#" class="mlpt-header '.$class.'"><i class="'.$value['icon'].'"></i>'.$value['title'].'</a>';
						
						$html .= '<div id="mlpt_inner_'.self::$instance.'_div_'.$i.'" class="mlpt-inner-div '.$class.'" data-num="'.$i.'" data-set="'.self::$instance.'">';
						$html .= '<div id="mlpt_inner_limit_'.$i.'" class="mlpt-inner-limit">';
						$html .= '<p class="info_div_info">'.$value['info'].'</p>';
						$html .= '</div>';
						$html .= '</div>';
						$html .= '</li>';
						$i++;
					}
			$html .= '</ul>';
			$html .= '</div>';
			
			echo apply_filters( 'the_content', $html );
		}
		
		public function output_array() {
			self::$instance++;
			$a = $this->mlpt_array;
			$color_array = get_post_meta( $this->id, '_mlpt_colors', false );
			$num = get_post_meta( $this->id, '_mlpt_custom_active', true ) && get_post_meta( $this->id, '_mlpt_custom_active', true ) != '' ? get_post_meta( $this->id, '_mlpt_custom_active', true ) : 1;
			$i = 0;
			/* Start output */
			$html = '<div id="mlpt_action_info_'.self::$instance.'" class="mlpt_action_info mlpt-action-'.$this->id.'" data-tabs="'.$this->id.'">';
			if(is_array($color_array) && count($color_array > 0)) {
				$html .= '<style type="text/css">';
				$html .= '.mlpt-action-'.$this->id.' ul li a {background:'.$color_array[0]['background'].' !important; color:'.$color_array[0]['font'].' !important;}';
				$html .= '.mlpt-action-'.$this->id.' ul li a.active {background:'.$color_array[0]['hover_background'].' !important; color:'.$color_array[0]['hover_font'].' !important;}';
				$html .= '.mlpt-action-'.$this->id.' ul li a:hover {background:'.$color_array[0]['hover_background'].' !important; color:'.$color_array[0]['hover_font'].' !important;}';
				$html .= '.mlpt-action-'.$this->id.' ul li a i {color:'.$color_array[0]['icon'].' !important;}';
				$html .= '.mlpt-action-'.$this->id.' ul.mlpt_action_tabs {border-right:1px solid '.$color_array[0]['top_div'].' !important;}';
				$html .= '.mlpt-action-'.$this->id.' #mlpt_action_info_divs_'.self::$instance.' {background:'.$color_array[0]['div'].' !important; color:'.$color_array[0]['div_font'].' !important; border-top: 5px solid '.$color_array[0]['top_div'].';}';
				$html .= '.mlpt-action-'.$this->id.' #mlpt_action_info_divs_'.self::$instance.' a {color:'.$color_array[0]['div_link'].' !important;}';
				$html .= '.mlpt-action-'.$this->id.' #mlpt_action_info_divs_'.self::$instance.' a:hover {color:'.$color_array[0]['div_link_hover'].' !important;}';
				$html .= '</style>';
			}
			$html .= '<ul id="mlpt_action_tabs_'.self::$instance.'" data-instance="'.self::$instance.'" class="mlpt_action_tabs">';
					$class;
					foreach($a as $key => $value) {
						$class = ( $key == ($num - 1) ) ? 'active' : '';
						$html .= '<li class="li_'.$class.'"><a href="#" class="'.$class.'"><i class="'.$value['icon'].'"></i>'.$value['title'].'</a></li>';
						$i++;
					}
			$html .= '</ul>';
			$i = 0;
			$html .= '<div id="mlpt_action_info_divs_'.self::$instance.'" class="mlpt_action_info_divs">';
					foreach($a as $key => $value) {
						$class = ( $key == 0 ) ? 'div_active' : '';
						$html .= '<div class="mlpt-inner-div '.$class.'">';
						$html .= '<p class="info_div_info">'.$value['info'].'</p>';
						$html .= '</div>';
						$i++;
					}
			$html .= '</div>';
			$html .= '</div>';
			echo apply_filters( 'the_content', $html );
		}
	}
	
	mlpt_action_list();
	
}	

/*************************************************
=================== SHORTCODES ===================
*************************************************/

function mlpt_shortcode($atts, $content=null){
	static $instance = 0;
		$instance++;
		
		$tabs = $atts['tabs'];
		$args = array(
			'post_type'		=> 'mlpt_tabs',
			'meta_key'      => '_mlpt_shortcode_output',
			'meta_value'    => $tabs,
		);
		
		$tab_post = get_posts( $args );
		$tabs_id = $tab_post[0]->ID;
		$shortcode_info = get_post_meta( $tabs_id, '_mlpt_custom', true );
		$mlpt_custom_active = get_post_meta( $tabs_id, '_mlpt_custom_active', true ) && get_post_meta( $tabs_id, '_mlpt_custom_active', true ) != '' ? get_post_meta( $tabs_id, '_mlpt_custom_active', true ) : false;
		$mlpt_props_start = get_post_meta( $tabs_id, '_mlpt_props_start', true ) && get_post_meta( $tabs_id, '_mlpt_props_start', true ) != '' ? get_post_meta( $tabs_id, '_mlpt_props_start', true ) : false;
		$mlpt_action_type = get_post_meta( $tabs_id, '_mlpt_action_type', true ) && get_post_meta( $tabs_id, '_mlpt_action_type', true ) != '' ? get_post_meta( $tabs_id, '_mlpt_action_type', true ) : false;
		
		$mlpt_shortcode_product_tabs = new mlpt_shortcode_tabs( $instance, $shortcode_info, $tabs_id );
	
	extract( shortcode_atts( array (
		'tabs' 	  => '',
		'display' => '', /* normal/concertina */
		'start' => '', /* open/closed */
		'num' => '' /* a number */
    ), $atts ) );

	if($num != '') {
		$active = $num;
	}
	elseif($num == '' && $mlpt_custom_active) {
		$active = $mlpt_custom_active;
	}
	else {
		$active = 1;
	}
	
	if($start == 'closed' || $start == 'open') {
		$c_start = $start;
	}
	else if(($start == '' || ($start != 'closed' && $start != 'open')) && $mlpt_props_start) {
		$c_start = $mlpt_props_start;
	}
	else {
		$c_start = 'open';
	}
	
	if($display == 'normal' || $display == 'concertina' ) {
		if($display == 'normal') {
			return $mlpt_shortcode_product_tabs->output_array($c_start, $active);
		}
		else {
			return $mlpt_shortcode_product_tabs->output_array_concert($c_start, $active);
		}
	}
	else if(($display == '' || ($display != 'normal' && $display != 'concertina' )) && $mlpt_action_type) {
		if($mlpt_action_type == 'concertina') {
			return $mlpt_shortcode_product_tabs->output_array_concert($c_start, $active);
		}
		else {
			return $mlpt_shortcode_product_tabs->output_array($c_start, $active);
		}
	}
	else {
		return $mlpt_shortcode_product_tabs->output_array($c_start, $active);
	}
}

add_shortcode('mlpt', 'mlpt_shortcode');


/*************************************************
================== ACTION LIST ===================
*************************************************/

function mlpt_get_tabs($id) {
	$mlpt_tabs = get_post_meta( $id, '_mlpt_custom', true );
	return $mlpt_tabs;
}

function mlpt_action_list() {
	
	$args = array(
		'posts_per_page' => -1,
		'post_type'		=> 'mlpt_tabs',
		'meta_key' => '_mlpt_action_text',
		'meta_value'   => '',
		'meta_compare' => '!='
	);
		
	$tab_posts = get_posts( $args );
	
	if(!empty($tab_posts)) {
		foreach($tab_posts as $tab_post) {
			
			$id = $tab_post->ID;
			$mlpt_action = get_post_meta( $id, '_mlpt_action_text', true ) != '' ? get_post_meta( $id, '_mlpt_action_text', true ) : '';
			
			if($mlpt_action != '') {
				$action_array = explode(',', $mlpt_action);

				$get_tabs = mlpt_get_tabs($id);
				$new = new mlpt_action_tabs( $get_tabs, $id );
				$new->mlpt_add_action( $action_array[0] );
			}
		}
	}
}


/*************************************************
============== GENERATE ICON LIST ================
*************************************************/

function mlpt_icon_list() {
	$icons = array(
		'mlpt-icon-home',
		'mlpt-icon-cloud',
		'mlpt-icon-umbrella',
		'mlpt-icon-right-hand',
		'mlpt-icon-right-dir',
		'mlpt-icon-heart-empty-1',
		'mlpt-icon-heart-1',
		'mlpt-icon-music',
		'mlpt-icon-attention',
		'mlpt-icon-flash',
		'mlpt-icon-flight-1',
		'mlpt-icon-cab',
		'mlpt-icon-taxi',
		'mlpt-icon-truck',
		'mlpt-icon-bus',
		'mlpt-icon-mail-1',
		'mlpt-icon-info-circled',
		'mlpt-icon-eye',
		'mlpt-icon-tag',
		'mlpt-icon-camera-alt',
		'mlpt-icon-emo-coffee',
		'mlpt-icon-location',
		'mlpt-icon-basket',
		'mlpt-icon-road',
		'mlpt-icon-magnet',
		'mlpt-icon-mobile',
		'mlpt-icon-emo-beer',
		'mlpt-icon-phone-squared',
		'mlpt-icon-twitter',
		'mlpt-icon-facebook',
		'mlpt-icon-certificate',
		'mlpt-icon-beaker',
		'mlpt-icon-menu-1',
		'mlpt-icon-magic',
		'mlpt-icon-gplus',
		'mlpt-icon-money',
		'mlpt-icon-mail-alt',
		'mlpt-icon-linkedin',
		'mlpt-icon-lightbulb',
		'mlpt-icon-suitcase',
		'mlpt-icon-bell-alt',
		'mlpt-icon-angle-right',
		'mlpt-icon-quote-right',
		'mlpt-icon-gamepad',
		'mlpt-icon-info',
		'mlpt-icon-attention-alt',
		'mlpt-icon-puzzle',
		'mlpt-icon-mic',
		'mlpt-icon-rocket',
		'mlpt-icon-bullseye',
		'mlpt-icon-youtube',
		'mlpt-icon-instagram-1',
		'mlpt-icon-tumblr',
		'mlpt-icon-moon',
		'mlpt-icon-bug',
		'mlpt-icon-wheelchair',
		'mlpt-icon-spoon',
		'mlpt-icon-cube',
		'mlpt-icon-recycle',
		'mlpt-icon-coffee',
		'mlpt-icon-tree',
		'mlpt-icon-file-image',
		'mlpt-icon-paper-plane-empty',
		'mlpt-icon-binoculars',
		'mlpt-icon-cc-visa',
		'mlpt-icon-cc-mastercard',
		'mlpt-icon-cc-paypal-1',
		'mlpt-icon-motorcycle',
		'mlpt-icon-heartbeat',
		'mlpt-icon-pinterest',
		'mlpt-icon-flickr',
		'mlpt-icon-vimeo',
		'mlpt-icon-picture',
		'mlpt-icon-globe-1',
		'mlpt-icon-leaf',
		'mlpt-icon-glass',
		'mlpt-icon-gift',
		'mlpt-icon-headphones',
		'mlpt-icon-video',
		'mlpt-icon-award',
		'mlpt-icon-user-1',
		'mlpt-icon-credit-card',
		'mlpt-icon-briefcase',
		'mlpt-icon-calendar',
		'mlpt-icon-pin',
		'mlpt-icon-attach',
		'mlpt-icon-book',
		'mlpt-icon-phone',
		'mlpt-icon-megaphone',
		'mlpt-icon-download',
		'mlpt-icon-camera',
		'mlpt-icon-key',
		'mlpt-icon-bell',
		'mlpt-icon-fire',
		'mlpt-icon-clock-1',
		'mlpt-icon-food',
		'mlpt-icon-ambulance',
		'mlpt-icon-medkit',
		'mlpt-icon-beer',
		'mlpt-icon-h-sigh',
		'mlpt-icon-angle-right',
		'mlpt-icon-desktop',
		'mlpt-icon-laptop',
		'mlpt-icon-help',
		'mlpt-icon-shield',
		'mlpt-icon-extinguisher',
		'mlpt-icon-anchor',
		'mlpt-icon-bank',
		'mlpt-icon-graduation-cap',
		'mlpt-icon-language',
		'mlpt-icon-paw',
		);
	$output = '<select class="mlnp_icon_list">';
	sort($icons);
	foreach($icons as $icon) {
		$output .= '<option value="'.$icon.'">'.$icon.'</option>';
	}
	$output .= '</select>';
	
	return $output;
}

/*
 * Resize images dynamically using wp built in functions
 * Victor Teixeira
 *
 * php 5.2+
 *
 * Exemplo de uso:
 *
 * <?php
 * $thumb = get_post_thumbnail_id();
 * $image = vt_resize( $thumb, '', 140, 110, true );
 * ?>
 * <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
 *
 * @param int $attach_id
 * @param string $img_url
 * @param int $width
 * @param int $height
 * @param bool $crop
 * @return array
 */
if ( ! function_exists( 'vt_resize' ) ) {
	function vt_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {

		// Cast $width and $height to integer
		$width = intval( $width );
		$height = intval( $height );

		// this is an attachment, so we have the ID
		if ( $attach_id ) {
			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			$file_path = get_attached_file( $attach_id );
		// this is not an attachment, let's use the image url
		} else if ( $img_url ) {
			$file_path = parse_url( urldecode(esc_url( $img_url )) );
			$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];

			//$file_path = ltrim( $file_path['path'], '/' );
			//$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];

			$orig_size = getimagesize( $file_path );

			$image_src[0] = $img_url;
			$image_src[1] = $orig_size[0];
			$image_src[2] = $orig_size[1];
		}

		$file_info = pathinfo( $file_path );

		// check if file exists
		if ( !isset( $file_info['dirname'] ) && !isset( $file_info['filename'] ) && !isset( $file_info['extension'] )  )
			return;
		
		$base_file = $file_info['dirname'].'/'.$file_info['filename'].'.'.$file_info['extension'];
		if ( !file_exists($base_file) )
			return;

		$extension = '.'. $file_info['extension'];

		// the image path without the extension
		$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];

		$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

		// checking if the file size is larger than the target size
		// if it is smaller or the same size, stop right here and return
		if ( $image_src[1] > $width ) {
			// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
			if ( file_exists( $cropped_img_path ) ) {
				$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );

				$vt_image = array (
					'url' => $cropped_img_url,
					'width' => $width,
					'height' => $height
				);
				return $vt_image;
			}

			// $crop = false or no height set
			if ( $crop == false OR !$height ) {
				// calculate the size proportionally
				$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
				$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;

				// checking if the file already exists
				if ( file_exists( $resized_img_path ) ) {
					$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

					$vt_image = array (
						'url' => $resized_img_url,
						'width' => $proportional_size[0],
						'height' => $proportional_size[1]
					);
					return $vt_image;
				}
			}

			// check if image width is smaller than set width
			$img_size = getimagesize( $file_path );
			if ( $img_size[0] <= $width ) $width = $img_size[0];
			
			// Check if GD Library installed
			if ( ! function_exists ( 'imagecreatetruecolor' ) ) {
			    echo 'GD Library Error: imagecreatetruecolor does not exist - please contact your web host and ask them to install the GD library';
			    return;
			}

			// no cache files - let's finally resize it
			if ( function_exists( 'wp_get_image_editor' ) ) {
				$image = wp_get_image_editor( $file_path );
				if ( ! is_wp_error( $image ) ) {
					$image->resize( $width, $height, $crop );
					$save_data = $image->save();
					if ( isset( $save_data['path'] ) ) $new_img_path = $save_data['path'];
				}
			} else {
				$new_img_path = image_resize( $file_path, $width, $height, $crop );
			}		
			
			$new_img_size = getimagesize( $new_img_path );
			$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

			// resized output
			$vt_image = array (
				'url' => $new_img,
				'width' => $new_img_size[0],
				'height' => $new_img_size[1]
			);

			return $vt_image;
		}

		// default output - without resizing
		$vt_image = array (
			'url' => $image_src[0],
			'width' => $width,
			'height' => $height
		);

		return $vt_image;
	}
}


/**************************************************
============== Add Help Tabs ======================
**************************************************/

function mlpt_contextual_help( $contextual_help, $screen_id, $screen ) {
    if ( 'mlpt_tabs' == $screen_id ) {
	
	$screen->add_help_tab( array(
        'id'      => 'mlpt_shortcode-help',
        'title'   => __('Shortcode','mlpt'),
        'content' => '', 
        'callback' => 'mlpt_shortcode_help'
    ));
	
	$screen->add_help_tab( array(
        'id'      => 'mlpt_general-help',
        'title'   => __('Options','mlpt'),
        'content' => '', 
        'callback' => 'mlpt_styles_help'
    ));
	
	get_current_screen()->set_help_sidebar(
        '<p><strong>' . __('For a demo:','ML') . '</strong></p>
        <p><a href="http://lillistone.me/2016/04/tab-demo/" title="MLPT Tabs" target="_blank">'.__('Tabs demo page','ML').'</a></p>'
    );

    } elseif ( 'edit-mlpt_tabs' == $screen_id ) {

    }
    return $contextual_help;
}

add_action( 'contextual_help', 'mlpt_contextual_help', 10, 3 );

function mlpt_shortcode_help() {
	
		echo '<h2>'.__('Shortcode and Add Action','ML').'</h2>';
		echo '<b>'.__('The standard shortcode','ML').'</b><br />';
		echo __('The shortcode top right will display the normal tabs. To display the concertina tabs add: display="concertina" to the shortcode.<br />You can use the shortcode anywhere to display these tabs.','ML').'<br />';
		echo '<b>'.__('Add to Action Hook','ML').'</b><br />';
		echo __('Type the name of one action hook to hook these tabs to.','ML').'<br />';
		echo __('Reference: <a href="https://developer.wordpress.org/reference/functions/add_action/" target="_blank">add_action function</a>','ML').'<br />';
		echo '<b>'.__('Add your own custom action hook and hook the tabs to it.','ML').'</b><br />';
		echo __('Reference: <a href="https://developer.wordpress.org/reference/functions/do_action/" target="_blank">do_action function</a>','ML').'<br />';		
}

function mlpt_styles_help() {
		echo '<h3>'.__('Styles','ML').'</h3>';
		echo '<b>'.__('Tabs Styles Info','ML').'</b><br />';
		echo __('Customise the styles for each separate set of tabs.','ML').'<br />';
		echo __('The tabs will resize responsively and will fit the width of the parent container. You can change the maximum width in the css files.','ML').'<br />';
}
	
?>