<?php 
if( !function_exists('ts_get_portfolio_items_content') ){
	return;
}

global $post;
$cat_list = get_the_terms($post, 'ts_portfolio_cat');
$cat_ids = array();
if( is_array($cat_list) ){
	foreach( $cat_list as $cat ){
		$cat_ids[] = $cat->term_id;
	}
}

$args = array(
		'post_type' 		=> $post->post_type
		,'post__not_in' 	=> array($post->ID)
		,'posts_per_page' 	=> 6
	);

if( !empty($cat_ids) ){
	$args['tax_query'] = array(
		array(
			'taxonomy'	=> 'ts_portfolio_cat'
			,'field'	=> 'term_id'
			,'terms'	=> $cat_ids
		)
	);
}

$posts = new WP_Query($args);

if( $posts->have_posts() ){	
	$atts = array(
				'show_title'		=> 1
				,'show_categories'	=> 1
				,'show_like_icon'	=> 1
				,'original_image'	=> 0
			);
	?>
	<div class="ts-portfolio-wrapper related-portfolios ts-slider ts-shortcode loading" data-nav="0" data-autoplay="1" data-columns="3">
		<div class="container">
			<header class="shortcode-heading-wrapper">
				<h2 class="shortcode-title">
					<?php esc_html_e('Related Projects', 'mydecor'); ?>
				</h2>
			</header>
			
			<div class="portfolio-inner items">
				<?php ts_get_portfolio_items_content($atts, $posts); ?>
			</div>
		</div>
	</div>
	<?php
}
wp_reset_postdata();
?>