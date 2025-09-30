<?php 
get_header();

global $post;
setup_postdata($post);

$theme_options = mydecor_get_theme_options();

$show_breadcrumb = apply_filters('mydecor_show_breadcrumb_on_single_portfolio', true);

$container_classes = array();
if( $show_breadcrumb ){
	$container_classes[] = 'show_breadcrumb_' . $theme_options['ts_breadcrumb_layout'];
}

$video_url = get_post_meta($post->ID, 'ts_video_url', true);

$thumbnail_style = $theme_options['ts_portfolio_thumbnail_style'];

$classes = array();
$classes[] = $thumbnail_style;
$classes[] = 'columns-' . $theme_options['ts_portfolio_thumbnail_columns'];

$is_slider = $thumbnail_style == 'slider' ? true : false;

mydecor_breadcrumbs_title($show_breadcrumb, false, '');
?>
<div id="content" class="page-container container-post <?php echo esc_attr(implode(' ', $container_classes)) ?>">
	
	<!-- main-content -->
	<div id="main-content" class="ts-col-24">
		<article class="single single-post single-portfolio <?php echo esc_attr(implode(' ', $classes)) ?>">
		
			<div class="entry-main">
				<!-- Blog Thumbnail -->
				<?php if( $theme_options['ts_portfolio_thumbnail'] ): ?>
				<div class="entry-format <?php echo esc_attr($is_slider?'nav-middle':''); ?>">
					<div class="thumbnail <?php echo esc_attr($is_slider?'gallery loading':''); ?>">
						<figure>
							<?php
							$gallery = get_post_meta($post->ID, 'ts_gallery', true);
							if( $gallery ){
								$gallery_ids = explode(',', $gallery);
							}
							else{
								$gallery_ids = array();
							}
							
							if( is_array($gallery_ids) && has_post_thumbnail() ){
								array_unshift($gallery_ids, get_post_thumbnail_id());
							}
							foreach( $gallery_ids as $gallery_id ){
								echo wp_get_attachment_image( $gallery_id, 'full' );
							}						
							?>
						</figure>
						<?php 
						if( $video_url ){
							echo do_shortcode('[ts_video src="'.esc_url($video_url).'"]');
						}
						?>
					</div>
				</div>
				<?php endif; ?>
				
				<div class="entry-content">	
					
					<!-- Portfolio Title -->
					<?php if( $theme_options['ts_portfolio_title'] ): ?>
						<h2 class="entry-title"><?php the_title() ?></h2>
					<?php endif; ?>
						
					<!-- Portfolio Content -->
					<?php if( $theme_options['ts_portfolio_content'] ): ?>
						<div class="portfolio-content">
							<?php the_content(); ?>
						</div>
					<?php endif; ?>
					
					<div class="meta-content">
						<!-- Portfolio Likes -->
						<?php if( $theme_options['ts_portfolio_likes'] ): ?>
							<div class="portfolio-info like-button">
							<?php
								global $ts_portfolios;
								$like_num = 0;
								$already_like = false;
								if( is_a($ts_portfolios, 'TS_Portfolios') && method_exists($ts_portfolios, 'get_like') ){
									$like_num = $ts_portfolios->get_like($post->ID);
									$already_like = $ts_portfolios->user_already_like($post->ID);
								}
								?>
								<div class="portfolio-like">
									<span class="ic-like <?php echo esc_attr($already_like?'already-like':''); ?>" data-post_id="<?php echo esc_attr($post->ID) ?>"></span>
									<span class="like-num" data-single="<?php esc_attr_e('Like', 'mydecor'); ?>" data-plural="<?php esc_attr_e('Likes', 'mydecor'); ?>">
										<?php echo esc_html( sprintf( _n( '%s Like', '%s Likes', $like_num, 'mydecor' ), $like_num ) ); ?>
									</span>
								</div>
							</div>
						<?php endif; ?>
						
						<!-- Portfolio Sharing -->
						<?php if( $theme_options['ts_portfolio_sharing'] && function_exists('ts_template_social_sharing') ): ?>
						<div class="social-sharing portfolio-info">
							<?php ts_template_social_sharing(); ?>
						</div>
						<?php endif; ?>
						
						<!-- Portfolio Client -->
						<?php $client = get_post_meta($post->ID, 'ts_client', true); ?>
						<?php if( $theme_options['ts_portfolio_client'] && $client ): ?>
						<div class="portfolio-info">
							<span><?php esc_html_e('Client:', 'mydecor') ?></span>
							<span class="client"><?php echo esc_html($client); ?></span>
						</div>
						<?php endif; ?>
						
						<!-- Portfolio Year -->
						<?php $year = get_post_meta($post->ID, 'ts_year', true); ?>
						<?php if( $theme_options['ts_portfolio_year'] && $year ): ?>
						<div class="portfolio-info">
							<span><?php esc_html_e('Year:', 'mydecor') ?></span>
							<span class="year"><?php echo esc_html($year); ?></span>
						</div>
						<?php endif; ?>
						
						<!-- Portfolio Categories -->
						<?php
						$categories_list = get_the_term_list($post->ID, 'ts_portfolio_cat', '', ' , ', '');
						if ( $categories_list && $theme_options['ts_portfolio_categories'] ):
						?>
						<div class="portfolio-info">
							<span><?php esc_html_e('Categories:', 'mydecor'); ?></span>
							<span class="cat-links"><?php echo wp_kses( $categories_list, 'mydecor_link' ); ?></span>
						</div>
						<?php endif; ?>
						
						<!-- Portfolio Custom Field -->
						<?php if( $theme_options['ts_portfolio_custom_field'] ): ?>
						<div class="portfolio-info">
							<span><?php echo esc_html($theme_options['ts_portfolio_custom_field_title']); ?>:</span>
							<div class="custom-field">
								<?php echo do_shortcode( $theme_options['ts_portfolio_custom_field_content'] ) ?>
							</div>
						</div>
						<?php endif; ?>
						
						<!-- Portfolio URL -->
						<?php if( $theme_options['ts_portfolio_url'] ):
						$portfolio_url = get_post_meta($post->ID, 'ts_portfolio_url', true);
						if( $portfolio_url == '' ){
							$portfolio_url = get_the_permalink();
						}
						?>
						<div class="portfolio-info">
							<span><?php esc_html_e('Link:', 'mydecor') ?></span>
							<a href="<?php echo esc_url($portfolio_url); ?>" class="portfolio-url"><?php echo esc_url($portfolio_url); ?></a>
						</div>
						<?php endif; ?>
					
					</div>
						
				</div>
			</div>
			
		</article>
	</div><!-- end main-content -->
	
</div>

<?php 
if( $theme_options['ts_portfolio_related_posts'] ){
	get_template_part('templates/related-portfolios');
}

get_footer();
?>