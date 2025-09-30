<?php get_header(); ?>
<div class="page-container">
	<div id="main-content" class="ts-col-24">	
		<div id="primary" class="site-content">
			<article>
				<div class="text-404">
					<div>
						<h1 class="primary-color"><?php esc_html_e('404', 'mydecor'); ?></h1>
						<div class="right-404">
							<h2><?php esc_html_e('Sorry !', 'mydecor'); ?></h2>
							<p class="h5"><?php esc_html_e('The Page You’re Looking For Was Not Found', 'mydecor'); ?></p>
							<?php if( $referer = wp_get_referer() ): ?>
							<a href="<?php echo esc_url( $referer ) ?>" class="button-text"><?php esc_html_e('Go Back', 'mydecor'); ?></a>
							<?php endif; ?>
						</div>
					</div>
					<?php get_search_form(); ?>
				</div>
				<div class="img-404"></div>
			</article>
		</div>
	</div>
</div>
<?php
get_footer();