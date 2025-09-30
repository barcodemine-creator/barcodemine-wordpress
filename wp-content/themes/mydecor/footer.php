<?php $mydecor_theme_options = mydecor_get_theme_options(); ?>
<div class="clear"></div>
</div><!-- #main .wrapper -->
<div class="clear"></div>
	<?php if( !is_page_template('page-templates/blank-page-template.php') && $mydecor_theme_options['ts_footer_block'] ): ?>
	<footer id="colophon" class="footer-container footer-area">
		<div class="container">
			<?php mydecor_get_footer_content( $mydecor_theme_options['ts_footer_block'] ); ?>
		</div>
	</footer>
	<?php endif; ?>
</div><!-- #page -->

<?php if( !is_page_template('page-templates/blank-page-template.php') ): ?>
	<?php if( ( wp_is_mobile() && $mydecor_theme_options['ts_only_load_mobile_menu_on_mobile'] ) || !$mydecor_theme_options['ts_only_load_mobile_menu_on_mobile'] ): ?>
	<!-- Group Header Button -->
	<div id="group-icon-header" class="ts-floating-sidebar">
		<div class="overlay"></div>
		<div class="ts-sidebar-content <?php echo has_nav_menu( 'vertical' )?'':'no-tab'; ?>">
			
			<div class="sidebar-content">
				<ul class="tab-mobile-menu">
					<li id="main-menu" class="active"><span><?php esc_html_e('Menu', 'mydecor'); ?></span></li>
					<li id="vertical-menu"><span><?php echo mydecor_get_vertical_menu_heading(); ?></span></li>
				</ul>
				
				<h6 class="menu-title"><span><?php esc_html_e('Main Menu', 'mydecor'); ?></span></h6>
				
				<div class="mobile-menu-wrapper ts-menu tab-menu-mobile">
					<?php 
					if( has_nav_menu( 'mobile' ) ){
							wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'mobile-menu', 'theme_location' => 'mobile', 'walker' => new MyDecor_Walker_Nav_Menu() ) );
						}else if( has_nav_menu( 'primary' ) ){
							wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'mobile-menu', 'theme_location' => 'primary', 'walker' => new MyDecor_Walker_Nav_Menu() ) );
						}
						else{
							wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'mobile-menu' ) );
						}
					?>
				</div>
				
				<?php if( has_nav_menu( 'vertical' ) ){ ?>
				<div class="mobile-menu-wrapper ts-menu tab-vertical-menu">
					<?php
					wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'vertical-menu pc-menu ts-mega-menu-wrapper','theme_location' => 'vertical', 'walker' => new MyDecor_Walker_Nav_Menu() ) );
					?>
				</div>
				<?php } ?>
				
				<?php if( $mydecor_theme_options['ts_header_currency'] || $mydecor_theme_options['ts_header_language'] ): ?>
				<div class="group-button-header">
						
					<?php if( $mydecor_theme_options['ts_header_language'] ): ?>
					<div class="header-language"><?php mydecor_wpml_language_selector(); ?></div>
					<?php endif; ?>
					
					<?php if( $mydecor_theme_options['ts_header_currency'] ): ?>
					<div class="header-currency"><?php mydecor_woocommerce_multilingual_currency_switcher(); ?></div>
					<?php endif; ?>
					
				</div>
				<?php endif; ?>
				
			</div>	
			
		</div>
		
	</div>

	<!-- Mobile Group Button -->
	<div id="ts-mobile-button-bottom">

		<?php if( $mydecor_theme_options['ts_mobile_bottom_bar_custom_content'] ): ?>
		<div class="mobile-button-custom"><?php echo do_shortcode(stripslashes($mydecor_theme_options['ts_mobile_bottom_bar_custom_content'])); ?></div>
		<?php endif; ?>
		
		<div class="mobile-button-home"><a href="<?php echo esc_url( home_url('/') ) ?>"></a></div>
		
		<?php if( class_exists('WooCommerce') ): ?>
		<div class="mobile-button-shop"><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"></a></div>
		<?php endif; ?>
		
		<?php if( $mydecor_theme_options['ts_enable_search'] ): ?>
		<div class="search-button"><span class="icon"></span></div>
		<?php endif; ?>
		
		<?php if( $mydecor_theme_options['ts_enable_tiny_account'] ): ?>
		<div class="my-account-wrapper"><?php echo mydecor_tiny_account( false ); ?></div>
		<?php endif; ?>

		<?php if( class_exists('YITH_WCWL') && $mydecor_theme_options['ts_enable_tiny_wishlist'] ): ?>
		<div class="my-wishlist-wrapper"><?php echo mydecor_tini_wishlist(); ?></div>
		<?php endif; ?>
		
	</div>
	<?php endif; ?>

	<!-- Shopping Cart Floating Sidebar -->
	<?php if( class_exists('WooCommerce') && $mydecor_theme_options['ts_enable_tiny_shopping_cart'] && $mydecor_theme_options['ts_shopping_cart_sidebar'] && !is_cart() && !is_checkout() ): ?>
	<div id="ts-shopping-cart-sidebar" class="ts-floating-sidebar">
		<div class="overlay"></div>
		<div class="ts-sidebar-content">
			<span class="close"></span>
			<div class="ts-tiny-cart-wrapper"></div>
		</div>
	</div>
	<?php endif; ?>
<?php endif; ?>

<?php 
if( ( !wp_is_mobile() && $mydecor_theme_options['ts_back_to_top_button'] ) || ( wp_is_mobile() && $mydecor_theme_options['ts_back_to_top_button_on_mobile'] ) ): 
?>
<div id="to-top" class="scroll-button">
	<a class="scroll-button" href="javascript:void(0)" title="<?php esc_attr_e('Back to Top', 'mydecor'); ?>"><?php esc_html_e('Back to Top', 'mydecor'); ?></a>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>