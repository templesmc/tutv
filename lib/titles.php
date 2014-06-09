<?php
/**
 * Page titles
 */
function tutv_title() {
	if (is_home()) {
		if (get_option('page_for_posts', true)) {
			echo get_the_title(get_option('page_for_posts', true));
		} else {
			_e('Latest Posts', 'exai');
		}
	} elseif (is_archive()) {
		$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
		if ($term) {
			echo $term->name;
		} elseif (is_post_type_archive()) {
			echo get_queried_object()->labels->name;
		} elseif (is_day()) {
			printf(__('Daily Archives: %s', 'exai'), get_the_date());
		} elseif (is_month()) {
			printf(__('Monthly Archives: %s', 'exai'), get_the_date('F Y'));
		} elseif (is_year()) {
			printf(__('Yearly Archives: %s', 'exai'), get_the_date('Y'));
		} elseif (is_author()) {
			$author = get_queried_object();
			printf(__('Author Archives: %s', 'exai'), $author->display_name);
		} else {
			single_cat_title();
		}
	} elseif (is_search()) {
		printf(__('Search Results for %s', 'exai'), get_search_query());
	} elseif (is_404()) {
		_e('Not Found', 'exai');
	} else {
		the_title();
	}
}
