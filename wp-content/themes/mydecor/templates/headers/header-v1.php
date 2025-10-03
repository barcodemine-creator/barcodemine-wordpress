<?php
$mydecor_theme_options = mydecor_get_theme_options();

$header_classes = array();
if( $mydecor_theme_options['ts_enable_sticky_header'] ){
	$header_classes[] = 'has-sticky';
}

if( !$mydecor_theme_options['ts_enable_tiny_shopping_cart'] ){
	$header_classes[] = 'hidden-cart';
}

if( !$mydecor_theme_options['ts_enable_tiny_wishlist'] || !class_exists('WooCommerce') || !class_exists('YITH_WCWL') ){
	$header_classes[] = 'hidden-wishlist';
}

if( !$mydecor_theme_options['ts_header_currency'] ){
	$header_classes[] = 'hidden-currency';
}

if( !$mydecor_theme_options['ts_header_language'] ){
	$header_classes[] = 'hidden-language';
}

if( !$mydecor_theme_options['ts_enable_search'] ){
	$header_classes[] = 'hidden-search';
}
?>

<div id="vertical-menu-sidebar" class="menu-wrapper">
	<div class="overlay"></div>
	<div class="vertical-menu-content">
		<span class="close"></span>
		
		<div class="logo-wrapper"><?php mydecor_theme_logo(); ?></div>
		
		<?php if( $mydecor_theme_options['ts_header_language'] || $mydecor_theme_options['ts_header_currency'] ): ?>
		<div class="content-top">
			
			<?php if( $mydecor_theme_options['ts_header_language'] ): ?>
			<div class="header-language hidden-phone"><?php mydecor_wpml_language_selector(); ?></div>
			<?php endif; ?>
			
			<?php if( $mydecor_theme_options['ts_header_currency'] ): ?>
			<div class="header-currency hidden-phone"><?php mydecor_woocommerce_multilingual_currency_switcher(); ?></div>
			<?php endif; ?>
			
		</div>
		<?php endif; ?>
		
		<div class="ts-menu">
			<?php 
			if ( has_nav_menu( 'vertical' ) ) {
			?>
			
			<div class="vertical-menu-wrapper">
				<?php
				wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'vertical-menu pc-menu ts-mega-menu-wrapper','theme_location' => 'vertical','walker' => new MyDecor_Walker_Nav_Menu() ) );
				?>
			</div>
			<?php
			}
			?>
		</div>	
	</div>
</div>

<header class="ts-header <?php echo esc_attr(implode(' ', $header_classes)); ?>">
	<div class="header-container">
		<div class="header-template">
		
			<div class="header-sticky">
			
				<div class="header-middle">
					
					<div class="container">
						
						<?php if( ( wp_is_mobile() && $mydecor_theme_options['ts_only_load_mobile_menu_on_mobile'] ) || !$mydecor_theme_options['ts_only_load_mobile_menu_on_mobile'] ): ?>
						<div class="ts-mobile-icon-toggle">
							<span class="icon"></span>
						</div>
						<?php endif; ?>
						
						<div class="menu-logo">
							<?php if ( has_nav_menu( 'vertical' ) ):?>			
							<span class="vertical-menu-button hidden-phone"></span>
							<?php endif; ?>
							
							<div class="logo-wrapper"><?php mydecor_theme_logo(); ?></div>
						</div>
						
						<div class="menu-wrapper hidden-phone">
							
							<div class="ts-menu">
								<?php 
									if ( has_nav_menu( 'primary' ) ) {
										wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'main-menu pc-menu ts-mega-menu-wrapper','theme_location' => 'primary','walker' => new MyDecor_Walker_Nav_Menu() ) );
									}
									else{
										wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'main-menu pc-menu ts-mega-menu-wrapper' ) );
									}
								?>
							</div>
							
						</div>
						
						<div class="header-right">
						
							<?php if( $mydecor_theme_options['ts_enable_search'] ): ?>
							<div class="search-button hidden-phone">
								<span class="icon"></span>
							</div>
							<?php endif; ?>
							
							<?php if( $mydecor_theme_options['ts_enable_tiny_account'] ): ?>
							<div class="my-account-wrapper hidden-phone">
								<?php echo mydecor_tiny_account(); ?>
							</div>
							<?php endif; ?>
							
							<?php if( class_exists('YITH_WCWL') && $mydecor_theme_options['ts_enable_tiny_wishlist'] ): ?>
							<div class="my-wishlist-wrapper hidden-phone"><?php echo mydecor_tini_wishlist(); ?></div>
							<?php endif; ?>
							
							<?php if( $mydecor_theme_options['ts_enable_tiny_shopping_cart'] ): ?>					
							<div class="shopping-cart-wrapper">
								<?php echo mydecor_tiny_cart(); ?>
							</div>
							<?php endif; ?>
							
						</div>
						
					</div>
					
				</div>
				
			</div>			
			
		</div>	
	</div>
</header>