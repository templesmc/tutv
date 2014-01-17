<?php
/*
Template Name: Report Shows Page
*/

$time = date('Y-m-d');


header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=show-report-{$time}.csv");
header("Pragma: no-cache");
header("Expires: 0");

?>
"Show","Slug","Genre","Type","Description"
<?php
	$myterms = get_terms('shows');
	if ($myterms) {
		foreach($myterms as $term) {
		$name = $term->name;
		$slug = $term->slug;
		$desc = $term->description;
		$genre = get_term_meta( $term->term_id, 'genre', true );
		$production_type = get_term_meta( $term->term_id, 'production_type', true );

		echo "\"$name\",\"$slug\",\"$genre\",\"$production_type\",\"$desc\"\n";
		}
	}


?>