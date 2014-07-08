<?php

$tutv_df_new_meta_boxes =
array( //an array containing arrays for each field.
"date" => array(
  "name" => "date",
  "std" => "",
  "title" => "Date",
  "description" => "Select a Date.",
  "type" => "date")
);

function tutv_df_new_meta_boxes() {
global $post, $tutv_df_new_meta_boxes;

  foreach($tutv_df_new_meta_boxes as $df_meta_box) {

    if ( $df_meta_box['type'] == 'date') {
      $df_meta_box_value = get_post_meta($post->ID, $df_meta_box['name'].'_value', true);


      if($df_meta_box_value == "") {
        $df_meta_box_value = $df_meta_box['std'];
      }
      echo '<input type="hidden" name="'.$df_meta_box['name'].'_noncename" id="'.$df_meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';

      echo "<style>.date_jj, .date_hh, .date_mn {width:2em;}.date_aa {width:3.4em;}</style>";

      echo '<h4>'.$df_meta_box['title'].'</h4>';


      // Month

      if ($df_meta_box_value) {
        $month = date('n', $df_meta_box_value);
      } else {
        $month = 0;
      }

      $monthname = array(1 => "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

      echo "<select name='{$df_meta_box['name']}_value_month' class='{$df_meta_box['name']}_mm' >";
      for($currentmonth = 1; $currentmonth <= 12; $currentmonth++)
      {
        echo '<option value="';
        echo intval($currentmonth);
        echo '"';
        if ($currentmonth == $month) {echo ' selected="selected" ';}
        echo '>' . $monthname[$currentmonth] . '</option>';
      }
      echo '</select>';


      // Day

      if ($df_meta_box_value) {
        $day = date("j", $df_meta_box_value); }
      else {
        $day = '';
      }

      echo "<input name='{$df_meta_box['name']}_value_day' class='{$df_meta_box['name']}_jj' type='text' value='$day'>, ";


      // Year

      if ($df_meta_box_value) {
        $year = date('Y', $df_meta_box_value);
      }
      else {
        $year = '';
      }

      echo "<input name='{$df_meta_box['name']}_value_year' class='{$df_meta_box['name']}_aa' type='text' value='$year'> @ ";


      // Hour

      if ($df_meta_box_value) {
        $hour = date('H', $df_meta_box_value);
      }
      else {
        $hour = '';
      }

      echo "<input name='{$df_meta_box['name']}_value_hour' class='{$df_meta_box['name']}_hh' type='text' value='$hour'> : ";


      // Minute

      if ($df_meta_box_value) {
        $min = date('i', $df_meta_box_value);
      } else {
        $min = '';
      }

      echo "<input name='{$df_meta_box['name']}_value_minute' class='{$df_meta_box['name']}_mn' type='text' value='$min'>";


      // Labels

      echo '<p>' . $df_meta_box['description'];

      if ( $df_meta_box_value ) {
        echo ' The currently selected date is <strong>' . date("l F j, Y", $df_meta_box_value) . " at " . date("h:i A", $df_meta_box_value) . '</strong>.</p>';
      } else {
        echo 'No date is currently selected.</p>';
      }

    }

    else {
      $df_meta_box_value = get_post_meta($post->ID, $df_meta_box['name'].'_value', true);


      if($df_meta_box_value == "") {
        $df_meta_box_value = $df_meta_box['std'];
      }
      echo'<input type="hidden" name="'.$df_meta_box['name'].'_noncename" id="'.$df_meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';

      echo'<h4>'.$df_meta_box['title'].'</h4>';

      echo'<input type="text" name="'.$df_meta_box['name'].'_value" value="'.$df_meta_box_value.'" style="width:99.5%;"/><br />';

      echo'<p><label for="'.$df_meta_box['name'].'_value">'.$df_meta_box['description'].'</label></p>';

    }
  }
}


function tutv_df_create_meta_box() {
  if ( function_exists('add_meta_box') ) {
        add_meta_box( 'df-new-meta-boxes', 'Add Event Information', 'tutv_df_new_meta_boxes', 'events', 'normal', 'high' );
  }
}





function tutv_df_save_postdata( $post_id ) {
  global $post, $tutv_df_new_meta_boxes;

  // set timezone to wordpress timezone
  date_default_timezone_set( get_option('timezone_string') );

  foreach($tutv_df_new_meta_boxes as $df_meta_box) {

    $data = ( isset( $_POST[ $df_meta_box['name'].'_value' ] ) ) ? $_POST[ $df_meta_box['name'].'_value' ] : '';

    if( $df_meta_box['type'] == 'date' ) {
      if( empty( $_POST[$df_meta_box['name'].'_noncename'] ) || !wp_verify_nonce( $_POST[$df_meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
        return $post_id;
      }

      if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ))
        return $post_id;
      } else {
        if ( !current_user_can( 'edit_post', $post_id ))
        return $post_id;
      }

      $day = (int) $_POST[$df_meta_box['name'].'_value_day'];
      $month = $_POST[$df_meta_box['name'].'_value_month'];
      $year = (int) $_POST[$df_meta_box['name'].'_value_year'];
      $hour = (int) $_POST[$df_meta_box['name'].'_value_hour'];
      $min = (int) $_POST[$df_meta_box['name'].'_value_minute'];
      if ($day != '' || $year != '' || $hour != '' || $min != '' ){
        $date = strtotime ( $month . '/' . $day . '/' . $year . ' ' . $hour . ':' . $min );
        $date_day = date('Ymd', $date);
      }
      else{
        $date = "";
        $date_day = "";
      }

      update_post_meta($post_id, $df_meta_box['name'].'_value', $date);
      update_post_meta($post_id, $df_meta_box['name'].'_day_value', $date_day);

    } else {
      if ( !wp_verify_nonce( $_POST[$df_meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
        return $post_id;
      }

      if ( !current_user_can( "edit_{$_POST['post_type']}", $post_id ) ) {
        return $post_id;
      }

      $data = $_POST[$df_meta_box['name'].'_value'];


      update_post_meta($post_id, $df_meta_box['name'].'_value', $data);
    }
  }
}

add_action('admin_menu', 'tutv_df_create_meta_box');
add_action('save_post', 'tutv_df_save_postdata', 5);

// Redirect events page to streamline workflow
//
// If an event post is newly published, redirect to add a new post; if
// it is updated, redirect to the events list
function tutv_events_redirect( $location, $post_id ) {
  $post = get_post( $post_id );

  if( 'publish' == $post->post_status && 'events' == $post->post_type ) {

    // url will contain 'message=1' if the post is updated
    $is_updated_post = preg_match( '/message=1/', $location );

    // url will contain 'message=6' if the post is published
    $is_published_post = preg_match( '/message=6/', $location );

    if ( $is_updated_post ) {
      // redirect to the listing page if this has just been updated
      $location = 'edit.php?post_type=events';
    } elseif( $is_published_post ) {
      // redirect to add a new post if this has just been published
      // and display a 'post saved' message
      $location = 'post-new.php?post_type=events&message=7';
    }
  }

  return $location;
}
add_action( 'redirect_post_location', 'tutv_events_redirect', 10, 2 );

