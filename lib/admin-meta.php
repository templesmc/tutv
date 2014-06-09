<?php

/**
 * Define settings for meta-boxes to displayed on post edit pages
 */
$prefix = '';

$meta_boxes = array(
	array(
		'id' => 'featured',
		'title' => 'Feature this Post',
		'page' => array('post'),
		'context' => 'side',
		'priority' => 'low',
		'fields' => array(
			array(
				'name' => 'Featured',
				'id' => $prefix . 'featured',
				'type' => 'checkbox'
			)
		)
	),
	array(
		'id' => 'featured-video',
		'title' => 'Featured Video',
		'page' => array('episodes', 'clips'),
		'context' => 'side',
		'priority' => 'low',
		'fields' => array(
			array(
				'name' => 'Feature this Video',
				'id' => $prefix . 'featured-video',
				'type' => 'checkbox'
			)
		)
	)
);


/**
 * Add defined meta-boxes to admin screen
 *
 */
function tutv_add_meta_box() {
	global $meta_boxes;
	foreach( $meta_boxes as $meta_box ) {
		foreach( $meta_box['page'] as $post_type ) {
			add_meta_box( $meta_box['id'], $meta_box['title'], 'tutv_show_box', $post_type, $meta_box['context'], $meta_box['priority'], $meta_box );
		}
	}

}
add_action('admin_menu', 'tutv_add_meta_box');

/**
 * Callback function to show fields in meta-boxes
 *
 */
function tutv_show_box($post, $metabox) {
	$meta_box = $metabox['args'];

	// Use nonce for verification
	echo '<input type="hidden" name="tutv_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

	echo '<table class="form-table">';

	foreach ($meta_box['fields'] as $field) {
		// get current post metadata
		$meta = wp_get_object_terms($post->ID, $field['id']);
		$meta = $meta[0];
		$meta = $meta->term_id;
		if (empty($meta)) $meta = false;

		echo '<tr>',
				'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
				'<td>';
		switch ( $field['type'] ) {
			case 'text':
				echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
					'<br />', $field['desc'];
				break;
			case 'textarea':
				echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
					'<br />', $field['desc'];
				break;
			case 'select':
				echo '<select name="', $field['id'], '" id="', $field['id'], '">';
				foreach ($field['options'] as $option) {
					echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				echo '</select>';
				break;
			case 'radio':
				foreach ($field['options'] as $option) {
					echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
				}
				break;
			case 'checkbox':
				echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
				break;
		}
		echo 	'<td>',
			'</tr>';
	}

	echo '</table>';
}

/**
 * Save data set in custom meta-boxes
 *
 */
function tutv_save_box_data( $post_id ) {
	global $meta_boxes;

	// verify nonce
	$nonce = ( isset( $_POST['tutv_meta_box_nonce'] ) ) ? $_POST['tutv_meta_box_nonce'] : false;
	if ( !wp_verify_nonce( $nonce, basename(__FILE__) ) ) {
		return $post_id;
	}

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}

	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}

	foreach( $meta_boxes as $meta_box ) {

		// set featured taxonomy value
		foreach ($meta_box['fields'] as $field) {
			$old = wp_get_object_terms($post_id, $field['id']);
			$old = $old[0];
			$old = $old->term_id;
			if (empty($old)) $old = false;

			$new = $_POST[$field['id']];

			if ($new && $new != $old) {
				wp_set_post_terms($post_id, $new, $field['id'], false);
			} elseif ('' == $new && $old) {
				wp_set_post_terms($post_id, '', $field['id'], false);
			}
		}
	}
}
add_action('save_post', 'tutv_save_box_data', 3);

