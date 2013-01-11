<?php

/**
 * Add shows metadata
 *
 */
function tutv_edit_shows_meta( $term, $taxonomy ) {
	global 	$production_types;
	
	if ( !function_exists( 'get_term_meta' ) )
		return;
		
    $active_production_type = get_term_meta( $term->term_id, 'production_type', true);

    // Check/Set the default value
    if (!$active_production_type)
        $active_production_type = '';
        
    $active_weight = get_term_meta( $term->term_id, 'weight', true);
	$active_weight = ($active_weight) ? $active_weight : 0;
	
	$is_hidden = get_term_meta( $term->term_id, 'is_hidden', true);
	$is_hidden = ($is_hidden) ? true : false; 
    
    $active_genre = get_term_meta( $term->term_id, 'genre', true);
    if (!$active_genre)
        $active_genre = '';
	
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="production_type">Show Production Type</label></th>
        <td>
            <select name="production_type" id="production_type">
                <?php
                foreach( $production_types as $production_value => $production_info ) {
                	echo "<option value='$production_value' ";
                	if ( $active_production_type == $production_value )
                		echo 'selected="selected"';
                	echo ">{$production_info['name']}</option>";
                } 
                ?>
            </select>
            <p class="description">Shows will be organized and promoted based on their production source.</p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="weight">Show Weight</label></th>
        <td>
            <input type="text" name="weight" id="weight" size="4" style="width:auto;" value="<?php echo $active_weight; ?>"></input>
            <p class="description">Weight is used to order shows on the shows page.</p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="genre">Show Default Genre</label></th>
        <td>
        	<select name="genre" id="genre">
        		<option value=''>Select a Genre</option>
                <?php
                $genres = get_terms('genre','hide_empty=0');
                foreach( $genres as $genre ) {
                	echo "<option value='{$genre->slug}' ";
                	if ( $genre->slug == $active_genre )
                		echo 'selected="selected"';
                	echo ">{$genre->name}</option>";
                } 
                ?>
            </select>
            <p class="description">Default genre to give content with this show. Although content with this show can be given any genre, this show may be grouped by the given genre.</p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="is_hidden">Hide this Show</label></th>
        <td>
        	<input type="checkbox" name="is_hidden" value="true" <?php if($is_hidden) echo 'checked="checked"'; ?> >
            <p class="description">A hidden show will not prefix a show name and will sometimes be hidden from the interface.</p>
        </td>
    </tr>
    <?php
}
add_action( 'shows_edit_form_fields', 'tutv_edit_shows_meta', 10, 2);

/**
 * Save shows metadata
 *
 */
function tutv_save_shows_meta($term_id, $tt_id) {
    if ( !$term_id || !function_exists( 'update_term_meta' ) ) 
    	return;
    
    if ( isset( $_POST['production_type'] ) )
        update_term_meta( $term_id, 'production_type', $_POST['production_type'] );
    if ( isset( $_POST['weight'] ) )
        update_term_meta( $term_id, 'weight', (int) $_POST['weight'] );
	if ( isset( $_POST['is_hidden'] ) )
		$is_hidden = ($_POST['is_hidden']) ? true : false;
		update_term_meta( $term_id, 'is_hidden', $_POST['is_hidden'] );
	if ( isset( $_POST['genre'] ) )
		update_term_meta( $term_id, 'genre', $_POST['genre'] );
}
add_action( 'edited_shows', 'tutv_save_shows_meta', 10, 2);
