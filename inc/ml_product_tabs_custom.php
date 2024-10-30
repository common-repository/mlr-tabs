<?php
add_action ('init', 'mlpt_create_post_type' );

function mlpt_create_post_type() {

	$labels = array(
    'name'                  =>   __( 'Tabs', 'mlpt' ),
    'singular_name'         =>   __( 'Tab', 'mlpt' ),
    'add_new_item'          =>   __( 'Add New Tab', 'mlpt' ),
    'all_items'             =>   __( 'All Tabs', 'mlpt' ),
    'edit_item'             =>   __( 'Edit Tab', 'mlpt' ),
    'new_item'              =>   __( 'New Tab', 'mlpt' ),
    'view_item'             =>   __( 'View Tab', 'mlpt' ),
    'not_found'             =>   __( 'No Tabs Found', 'mlpt' ),
    'not_found_in_trash'    =>   __( 'No Tabs Found in Trash', 'mlpt' )
	);
 
	$supports = array(
		'title',
		'Tabs'
	);
 
	$args = array(
		'label'         =>   __( 'Tabs', 'mlpt' ),
		'labels'        =>   $labels,
		'description'   =>   __( 'Info tabs to be added to pages', 'mlpt' ),
		'capability_type' => 'post',
		'public'        =>   true,
		'publicly_queryable' => false,
		'show_in_menu'  =>   true,
		'show_in_nav_menus' => false,
		'exclude_from_search' => true,
		'menu_icon'     =>   'dashicons-editor-ul',
		'register_meta_box_cb' => 'mlpt_dynamic_custom_box',
		'has_archive'   =>   false,
		'rewrite'       =>   true,
		'supports'      =>   $supports
	);
 
register_post_type( 'mlpt_tabs', $args );	
}


add_action( 'add_meta_boxes', 'mlpt_dynamic_custom_box' );
add_action( 'save_post', 'mlpt_dynamic_save_postdata' );


function mlpt_dynamic_custom_box() {
    add_meta_box(
        'mlpt_dynamic_meta',
        __( 'Tabs', 'mlpt' ),
        'mlpt_dynamic_custom',
        'mlpt_tabs',
		'normal',
		'low'
	);
	add_meta_box(
        'mlpt_general_meta',
        __( 'General Options for these Tabs', 'mlpt' ),
        'mlpt_general_custom',
        'mlpt_tabs',
		'side',
		'low'
	);
	add_meta_box(
        'mlpt_color_meta',
        __( 'Colors for these tabs', 'mlpt' ),
        'mlpt_color_custom',
        'mlpt_tabs',
		'normal',
		'low'
	);
	add_meta_box(
        'mlpt_action_meta',
        __( 'Add to action hook', 'mlpt' ),
        'mlpt_action_custom',
        'mlpt_tabs',
		'side',
		'low'
	);
	add_meta_box(
        'mlpt_shortcode_meta',
        __( 'Shortcode for these Tabs', 'mlpt' ),
        'mlpt_shortcode_custom',
        'mlpt_tabs',
		'normal',
		'high'
	);
}


function mlpt_shortcode_custom( $post ) {
	wp_nonce_field( 'mlpt_meta_box_nonce', 'meta_box_nonce' );
?>
<div id="mlpt_shortcode_display">
<?php
	$unique = uniqid('mlpt_');
	get_post_meta( $post->ID, '_mlpt_shortcode_output', true ) == '' ? add_post_meta($post->ID, '_mlpt_shortcode_output', $unique, true) : '';
	$shortcode = '[mlpt tabs=\''.get_post_meta( $post->ID, '_mlpt_shortcode_output', true ).'\']';
	echo '<input readonly type="text" name="mlpt_shortcode_output" id="mlpt_shortcode_output" value="'.$shortcode.'" />';
?>
</div>
<?php
}

function mlpt_color_custom( $post ) {
	wp_nonce_field( 'mlpt_meta_box_nonce', 'meta_box_nonce' );
?>
<p>Customise colours</p>	
<?php
$mlpt_colors = get_post_meta( $post->ID, '_mlpt_colors', false );
echo '<div class="mlpt-color-container"><table class="mlpt-color-table">';
echo '<tr><td>Icon color</td><td><input type="text" class="mlpt-color-picker" name="_mlpt_colors[icon]" value="'.$mlpt_colors[0]['icon'].'" /></td></tr>';
echo '<tr><td>Tab background color</td><td><input type="text" class="mlpt-color-picker" name="_mlpt_colors[background]" value="'.$mlpt_colors[0]['background'].'" /></td></tr>';
echo '<tr><td>Tab font color</td><td><input type="text" class="mlpt-color-picker" name="_mlpt_colors[font]" value="'.$mlpt_colors[0]['font'].'" /></td></tr>';
echo '<tr><td>Tab hover background color</td><td><input type="text" class="mlpt-color-picker" name="_mlpt_colors[hover_background]" value="'.$mlpt_colors[0]['hover_background'].'" /></td></tr>';
echo '<tr><td>Tab hover font color</td><td><input type="text" class="mlpt-color-picker" name="_mlpt_colors[hover_font]" value="'.$mlpt_colors[0]['hover_font'].'" /></td></tr>';
echo '<tr><td>Div color</td><td><input type="text" class="mlpt-color-picker" name="_mlpt_colors[div]" value="'.$mlpt_colors[0]['div'].'" /></td></tr>';
echo '<tr><td>Top of Div color</td><td><input type="text" class="mlpt-color-picker" name="_mlpt_colors[top_div]" value="'.$mlpt_colors[0]['top_div'].'" /></td></tr>';
echo '<tr><td>Div font color</td><td><input type="text" class="mlpt-color-picker" name="_mlpt_colors[div_font]" value="'.$mlpt_colors[0]['div_font'].'" /></td></tr>';
echo '<tr><td>Div link color</td><td><input type="text" class="mlpt-color-picker" name="_mlpt_colors[div_link]" value="'.$mlpt_colors[0]['div_link'].'" /></td></tr>';
echo '<tr><td>Div link hover color</td><td><input type="text" class="mlpt-color-picker" name="_mlpt_colors[div_link_hover]" value="'.$mlpt_colors[0]['div_link_hover'].'" /></td></tr>';
echo '</table></div>';
}


function mlpt_action_custom( $post ) {
	wp_nonce_field( 'mlpt_meta_box_nonce', 'meta_box_nonce' );
?>
<div id="mlpt_action_meta">
<h4>Add these tabs to specific action</h4>
<?php
	$mlpt_action = get_post_meta( $post->ID, '_mlpt_action_text', true );
	echo '<input type="text" class="widefat" name="_mlpt_action_text" id="mlpt_action_text" value="'.$mlpt_action.'" />';
?>
</div>
<div id="mlpt_include_exclude">
<h4>Include or exclude page/post IDs <em>separate by comma</em></h4>
<?php
	$mlpt_exclude = get_post_meta( $post->ID, '_mlpt_exclude_text', true );
	$mlpt_include = get_post_meta( $post->ID, '_mlpt_include_text', true );
	echo 'Exclude: <input type="text" class="widefat" name="_mlpt_exclude_text" id="_mlpt_exclude_text" value="'.$mlpt_exclude.'" />';
	echo 'Include: <input type="text" class="widefat" name="_mlpt_include_text" id="_mlpt_include_text" value="'.$mlpt_include.'" />';
?>
</div>
<?php
}

function return_media_list($string) {
	$array = explode(',',$string);
	$media_array = array();
	foreach($array as $media) {
		$type = get_post_mime_type($media);	
		$sort = get_mime_type($type);
		$media_array[] = $type;
	}
	$list = implode(', ', $media_array);
	return $list;
}

function find_mime_image($string) {
	$mime = false;
	$array = explode(',', $string);
	foreach($array as $mime_type) {
		$type = get_post_mime_type($mime_type);	
		$sort = get_mime_type($type);
		if($sort == 'image') {
			$mime = true; 
		}
	}
	return $mime;
}

function mlpt_general_custom( $post ) {
	wp_nonce_field( 'mlpt_meta_box_nonce', 'meta_box_nonce' );
	$args = array( 'post' => $post->ID );
		wp_enqueue_media( $args );
?>

<div id="mlpt_type_meta">
	<div class="mlpt_check_concert choose_mlpt">
		<h4>Type of tabs</h4>
		<?php
			$mlpt_props_start = get_post_meta( $post->ID, '_mlpt_props_start', true );
			$mlpt_action_type = get_post_meta( $post->ID, '_mlpt_action_type', true );
			echo '<input type="radio" name="_mlpt_action_type" value="normal"' . checked( $mlpt_action_type, 'normal', false ) . ' />Normal';
			echo '<input style="margin-left:20px;" type="radio" name="_mlpt_action_type" value="concertina"' . checked( $mlpt_action_type, 'concertina',  false ) . ' />Concertina';
		?>
	</div>
	<div class="mlpt_start_concert">
		<div class="start_mlpt">
			<h4>Start</h4>
			<?php	
				echo '<input type="radio" name="_mlpt_props_start" value="open"' . checked( $mlpt_props_start, 'open', false ) . ' />Open';
				echo '<input style="margin-left:20px;" type="radio" name="_mlpt_props_start" value="closed"' . checked( $mlpt_props_start, 'closed',  false ) . ' />Closed<br />';
			?>
		</div>
		<div class="collapse_mlpt">
			<?php
				$mlpt_prevent = get_post_meta( $post->ID, '_mlpt_prevent', true );			
				echo '<label for="_mlpt_prevent">Prevent complete collapse i.e. leave one tab open at all times</label><input type="checkbox" name="_mlpt_prevent" id="_mlpt_prevent" value="1"' . checked( 1, $mlpt_prevent, false ) . ' />';
			?>
		</div>
		<div class="height_mlpt">
			<h4>Height Parameters</h4>
			<?php	
				$mlpt_highest = get_post_meta( $post->ID, '_mlpt_highest', true );
				$mlpt_limit = get_post_meta( $post->ID, '_mlpt_limit_height', true );
				echo '<span>Make all tabs height of highest tab</span><input type="checkbox" name="_mlpt_highest" id="_mlpt_highest" value="1"' . checked( 1, $mlpt_highest, false ) . ' /><br />';
				echo '<span>Limit height of tabs (scrollbar will be employed)</span><input type="number" min="100" max="1000" name="_mlpt_limit_height" id="_mlpt_limit_height" value="'.$mlpt_limit.'" />';
			?>
		</div>
	</div>
</div>

<?php
}


function mlpt_dynamic_custom( $post ) {
	
?>	
<div id="mlpt_product_meta">

<p class="mlpt_allowed_html"><span>Allowed HTML tags and attributes in <b>Info</b> box</span><br /><span>You can use all html tags as you would in a normal post except <em>&lt;script&gt;</em> and <em>&lt;iframe&gt;</em> tags.</span></p>
<p class="mlpt_allowed_html">Shortcodes are also allowed but shortcode performance may vary as the divs may be hidden on page load.</p>
<p><span class="mlpt_icon_title">Tab Icon List</span><br /><span id="mlpt_icon_display"><?php echo mlpt_icon_list(); ?></span><span id="mlpt_icon_box"><i class="mlpt-icon-ambulance"></i></span></p>
<p id="mlpt_icon_p"><em>Select and copy to Tab Icon box</em><br /><input id="mlpt_readonly" type="text" readonly value="mlpt-icon-ambulance" /></p>
<?php	
	$mlpt_custom = get_post_meta( $post->ID, '_mlpt_custom', true ); 
	$mlpt_custom_active = get_post_meta( $post->ID, '_mlpt_custom_active', true ); 
	
    $mlptc = 0;
    if ( count( $mlpt_custom ) > 0 && is_array( $mlpt_custom ) ) {
        foreach( $mlpt_custom as $mlpt_cus ) {
            if ( isset( $mlpt_cus['title'] ) || isset( $mlpt_cus['info'] ) || isset( $mlpt_cus['icon'] ) || isset( $mlpt_cus['media'] ) ) {
				
				echo '<div class="mlpt_tab_setting_div">
						<h2 data-id="'.$mlptc.'">
							<input type="radio" name="_mlpt_custom_active" value="'.($mlptc+1).'"' . checked( $mlpt_custom_active, ($mlptc+1), false ) . ' />
							<span class="mlpt_tab_title">Tab <span class="mlpt_tab_num">'.($mlptc+1).'</span> - <span class="mlpt_tab_aTitle">'.$mlpt_cus['title'].'</span></span> 
							<span class="dashicons dashicons-plus"></span>
						</h2>
						<div data-id="'.$mlptc.'" class="mlpt_tab_setting">
						<div class="mlpt_tab_setting_inner">
						<span class="mlpt_editor_title">Tab Title</span> 
						<input type="text" class="mlpt_custom_active_title" name="_mlpt_custom['.$mlptc.'][title]" value="'.$mlpt_cus['title'].'" /><br /><span class="mlpt_editor_title">Tab Icon</span> 
						<input type="text" name="_mlpt_custom['.$mlptc.'][icon]" value="'.$mlpt_cus['icon'].'" /><br /><span class="mlpt_editor_title">Info</span> 
						<textarea rows="5" cols="4" name="_mlpt_custom['.$mlptc.'][info]">'.$mlpt_cus['info'].'</textarea>
						</div>
						<span class="mlpt-remove">Remove Tab</span>
						</div>
					</div>';
                $mlptc += 1;
            }
        }
    }
?>
<span id="mlpt_add_here" data-count="<?php echo $mlptc; ?>"></span>
<p class="mlpt_add_meta"><?php _e('Add New Tab'); ?></p>

</div>
<?php
}

function mlpt_dynamic_save_postdata( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'mlpt_meta_box_nonce' ) ) return;
	
	if(isset($_POST['_mlpt_custom'])) {
		$mlpt_custom = $_POST['_mlpt_custom'];
		$array = array();
		$all = array();
		foreach($mlpt_custom as $mlpt_cus) {
			if($mlpt_cus['info'] != '') {
				$mlpt_info = wp_kses_post($mlpt_cus['info']);
				$mlpt_cus['info'] = $mlpt_info;
			}
			$array['title'] = isset($mlpt_cus['title']) ? sanitize_text_field( $mlpt_cus['title'] ) : '';
			$array['icon'] = isset($mlpt_cus['icon']) ? sanitize_text_field( $mlpt_cus['icon'] ) : '';
			$array['info'] = isset($mlpt_cus['info']) ? $mlpt_cus['info'] : '';
			$all[] = $array;
			$array = array();
		}
		update_post_meta( $post_id, '_mlpt_custom', $all);
	}
	else {
		delete_post_meta( $post_id, '_mlpt_custom' );
	}
	
	if(isset($_POST['_mlpt_custom_active'])) {
		update_post_meta( $post_id, '_mlpt_custom_active', $_POST['_mlpt_custom_active']);
	}
	else {
		delete_post_meta( $post_id, '_mlpt_custom_active' );
	}
	
	if(isset($_POST['_mlpt_colors'])) {
		$mlpt_colors = $_POST['_mlpt_colors'];
		update_post_meta( $post_id, '_mlpt_colors', $mlpt_colors );
	}
	else {
		delete_post_meta( $post_id, '_mlpt_colors' );
	}
	
	if(isset($_POST['_mlpt_action_text'])) {
		update_post_meta( $post_id, '_mlpt_action_text', sanitize_text_field($_POST['_mlpt_action_text']));
	}
	else {
		delete_post_meta( $post_id, '_mlpt_action_text' );
	}
	
	if(isset($_POST['_mlpt_exclude_text'])) {
		update_post_meta( $post_id, '_mlpt_exclude_text', sanitize_text_field($_POST['_mlpt_exclude_text']));
	}
	else {
		delete_post_meta( $post_id, '_mlpt_exclude_text' );
	}
	if(isset($_POST['_mlpt_include_text'])) {
		update_post_meta( $post_id, '_mlpt_include_text', sanitize_text_field($_POST['_mlpt_include_text']));
	}
	else {
		delete_post_meta( $post_id, '_mlpt_include_text' );
	}
	if(isset($_POST['_mlpt_action_type'])) {
		update_post_meta( $post_id, '_mlpt_action_type', $_POST['_mlpt_action_type']);
	}
	else {
		delete_post_meta( $post_id, '_mlpt_action_type' );
	}
	if(isset($_POST['_mlpt_props_start'])) {
		update_post_meta( $post_id, '_mlpt_props_start', $_POST['_mlpt_props_start']);
	}
	else {
		delete_post_meta( $post_id, '_mlpt_props_start' );
	}
	
	if(isset($_POST['_mlpt_highest'])) {
		update_post_meta( $post_id, '_mlpt_highest', $_POST['_mlpt_highest']);
	}
	else {
		delete_post_meta( $post_id, '_mlpt_highest' );
	}
	if(isset($_POST['_mlpt_limit_height'])) {
		update_post_meta( $post_id, '_mlpt_limit_height', $_POST['_mlpt_limit_height']);
	}
	else {
		delete_post_meta( $post_id, '_mlpt_limit_height' );
	}
	
	if(isset($_POST['_mlpt_prevent'])) {
		update_post_meta( $post_id, '_mlpt_prevent', $_POST['_mlpt_prevent']);
	}
	else {
		delete_post_meta( $post_id, '_mlpt_prevent' );
	}
}

add_filter('widget_text','do_shortcode');


function get_mime_type($type) {
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

?>