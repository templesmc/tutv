<?php
/*
Template Name: Report Episode Page
*/

$time = date('Y-m-d');

$page = (get_query_var('paged')) ? get_query_var('paged') : 1;

header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=episode-report-{$time}-page-{$page}.csv");
header("Pragma: no-cache");
header("Expires: 0");

query_posts( array( 
	'post_type' => 'episodes',
	'posts_per_page' => 399,
	'paged' => $page
) );

?>
"Post ID","Show","Title","EP","Slug","Server ID","Author/ Distributor","TRT","TV","Stream","Vimeo","WP Author"
<?php	
	while ( have_posts() ) : the_post();
		//var_dump($post);
		$show = wp_get_object_terms( $post->ID, 'shows' );
		$show = $show[0]->name;
		$custom_fields = get_post_custom( $post->ID );
		$user=get_userdata( $post->post_author );
		//var_dump($custom_fields);
		echo "\"{$post->ID}\",\"$show\",\"{$post->post_title}\",\"{$custom_fields['episode'][0]}\",\"{$post->post_name}\",\"{$custom_fields['Server ID'][0]}\",\"{$custom_fields['Author/ Distributor'][0]}\",\"{$custom_fields['TRT'][0]}\",\"{$custom_fields['TV'][0]}\",\"{$custom_fields['Live Stream'][0]}\",\"{$custom_fields['Vimeo'][0]}\",\"{$user->display_name}\"\n";
		
	endwhile;
?>