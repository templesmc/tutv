<?php
/*
Template Name: Report CSV Page
*/

$time = date('Y-m-d');


header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=report-{$time}.csv");
header("Pragma: no-cache");
header("Expires: 0");


	query_posts( array( 
		'post_type' => 'episodes',
		'posts_per_page' => -1,
	) );
?>
"Post ID","Show","Episode","Slug","Post Type","Server ID","Author/Distributor","TRT","TV","Stream","Web","Vimeo","Status","WP Author"
<?php	
	while ( have_posts() ) : the_post();
		//var_dump($post);
		$show = wp_get_object_terms( $post->ID, 'shows' );
		$show = $show[0]->name;
		$custom_fields = get_post_custom( $post->ID );
		$user=get_userdata( $post->post_author );
		//var_dump($custom_fields);
		echo "\"{$post->ID}\",\"$show\",\"{$post->post_title}\",\"{$post->post_name}\",\"{$post->post_type}\",\"{$custom_fields['Server ID'][0]}\",\"{$custom_fields['Author/ Distributor'][0]}\",\"{$custom_fields['TRT'][0]}\",\"{$custom_fields['TV'][0]}\",\"{$custom_fields['Live Stream'][0]}\",\"{$custom_fields['Web On-Demand'][0]}\",\"{$custom_fields['Vimeo'][0]}\",\"{$post->post_status}\",\"{$user->user_nicename}\"\n";
		
	endwhile;
?>