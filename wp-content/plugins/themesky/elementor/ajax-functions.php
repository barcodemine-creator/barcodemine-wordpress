<?php
if( !function_exists('ts_get_blog_items_content') ){
	function ts_get_blog_items_content( $atts = array(), $posts = null ){
		global $post;
		
		$is_ajax_frontend = wp_doing_ajax() && isset($_POST['action']) && $_POST['action'] == 'ts_blogs_load_items';
		if( $is_ajax_frontend ){
			if( !isset($_POST['atts']) ){
				die('0');
			}
			$atts = $_POST['atts'];
			$paged = isset($_POST['paged'])?absint($_POST['paged']):1;
			
			extract($atts);
			
			$args = array(
				'post_type' 			=> 'post'
				,'post_status' 			=> 'publish'
				,'ignore_sticky_posts' 	=> 1
				,'posts_per_page'		=> $limit
				,'orderby'				=> $orderby
				,'order'				=> $order
				,'paged'				=> $paged
				,'tax_query'			=> array()
			);
			
			if( $categories ){
				$args['tax_query'][] = array(
											'taxonomy' 	=> 'category'
											,'terms' 	=> explode(',', $categories)
											,'field' 	=> 'term_id'
											,'include_children' => false
										);
			}
			
			$posts = new WP_Query($args);
			ob_start();
		}
		
		extract($atts);
		
		$blog_thumb_size = 'mydecor_blog_thumb';
		if( $layout == 'masonry' ){
			$blog_thumb_size = 'full';
		}
		
		if( $posts->have_posts() ):
			$item_class = '';
			if( !$is_slider ){
				$item_class = 24/(int)$columns;
				$item_class = 'ts-col-'.$item_class;
			}
			
			$show_thumbnail_old = $show_thumbnail;
			while( $posts->have_posts() ): $posts->the_post();
				$show_thumbnail = $show_thumbnail_old;
			
				$post_format = get_post_format(); /* Video, Audio, Gallery, Quote */
				if( $is_slider && $post_format == 'gallery' ){ /* Remove Slider in Slider */
					$post_format = false;
				}
				?>
				<article class="item <?php echo ( $post_format == 'gallery' )?'nav-middle':'' ?> <?php echo esc_attr($post_format); ?> <?php echo esc_attr($item_class) ?>">
					<div class="article-content">
					<?php if( $show_thumbnail && $post_format != 'quote' ){ ?>
						<div class="thumbnail-content">
							<?php 
							if( $post_format == 'gallery' || $post_format === false || $post_format == 'standard' ){
							?>
								<a class="thumbnail <?php echo esc_attr($post_format); ?> <?php echo ($post_format == 'gallery')?'loading ts-slider':''; ?>" href="<?php echo ($post_format == 'gallery')?'javascript: void(0)':get_permalink() ?>">
									<figure>
									<?php 
									
									if( $post_format == 'gallery' ){
										$gallery = get_post_meta($post->ID, 'ts_gallery', true);
										$gallery_ids = explode(',', $gallery);
										if( is_array($gallery_ids) && has_post_thumbnail() ){
											array_unshift($gallery_ids, get_post_thumbnail_id());
										}
										foreach( $gallery_ids as $gallery_id ){
											echo wp_get_attachment_image( $gallery_id, $blog_thumb_size );
										}
										
										if( empty($gallery_ids) ){
											$show_thumbnail = false;
										}
									}
									
									if( $post_format === false || $post_format == 'standard' ){
										if( has_post_thumbnail() ){
											the_post_thumbnail( $blog_thumb_size ); 
										}
										else{
											$show_thumbnail = false;
										}
									}
									
									?>
									</figure>
									<div class="effect-thumbnail"></div>
								</a>
								
								
							<?php 
							}
							
							if( $post_format == 'video' ){
								$video_url = get_post_meta($post->ID, 'ts_video_url', true);
								echo do_shortcode('[ts_video src="'.$video_url.'"]');
								$show_thumbnail = false;
							}
							
							if( $post_format == 'audio' ){
								$audio_url = get_post_meta($post->ID, 'ts_audio_url', true);
								$show_thumbnail = false;
								if( strlen($audio_url) > 4 ){
									$file_format = substr($audio_url, -3, 3);
									if( in_array($file_format, array('mp3', 'ogg', 'wav')) ){
										echo do_shortcode('[audio '.$file_format.'="'.$audio_url.'"]');
									}
									else{
										echo do_shortcode('[ts_soundcloud url="'.$audio_url.'" width="100%" height="122"]');
									}
								}
							}
						?>
						
						</div>
					<?php } ?>
					
					<?php if( $post_format != 'quote' ): ?>
						
						<div class="entry-content">
							<header>
							
								<!-- Blog Categories -->
								<?php if( $show_categories ) : ?>
								<div class="cats-link">
									<?php echo get_the_category_list(', '); ?>
								</div>
								<?php endif; ?>
							
								<?php if( $show_title ): ?>
								<h4 class="heading-title entry-title">
									<a class="post-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h4>
								<?php endif; ?>
								
							</header>
							
							<?php if( $show_excerpt && function_exists('mydecor_the_excerpt_max_words') ): ?>
							<div class="excerpt"><?php mydecor_the_excerpt_max_words($excerpt_words, '', true, '', true); ?></div>
							<?php endif; ?>
							
							<?php if( $show_date || $show_author || $show_comment ) : ?>
							<div class="entry-meta-top">
						
								<!-- Blog Author -->
								<?php if( $show_author ): ?>
								<span class="vcard author">
									<span><?php esc_html_e('By', 'mydecor'); ?></span>
									<?php the_author_posts_link(); ?>
								</span>
								<?php endif; ?>
								
								<!-- Blog Date Time -->
								<?php if( $show_date ) : ?>
								<span class="date-time">
									<?php echo get_the_time( get_option('date_format') ); ?>
								</span>
								<?php endif; ?>
								
								<!-- Blog Comment -->
								<?php if( $show_comment && function_exists('mydecor_get_post_comment_count') ): ?>
								<span class="comment-count">
									<?php echo  mydecor_get_post_comment_count(); ?>
								</span>
								<?php endif; ?>
							
							</div>
							<?php endif; ?>
							
							<?php if( $show_readmore ): ?>
							<div class="entry-meta-bottom">
								<!-- Blog Read More Button -->
								<a class="button-readmore button button-primary" href="<?php the_permalink(); ?>"><?php esc_html_e('read more', 'themesky'); ?></a>
							</div>
							<?php endif; ?>
						</div>
							
						<?php else: /* Post format is quote */ ?>
							<div class="quote-wrapper">
								<blockquote>
									<?php 
									$quote_content = get_the_excerpt();
									if( !$quote_content ){
										$quote_content = get_the_content();
									}
									echo do_shortcode($quote_content);
									?>
									<?php if( $show_date || $show_author || $show_categories ) : ?>
								
										<div class="entry-meta-top">
										
											<!-- Blog Author -->
											<?php if( $show_author ): ?>
											<span class="vcard author">
												<span><?php esc_html_e('By', 'themesky'); ?></span>
												<?php the_author_posts_link(); ?>
											</span>
											<?php endif; ?>
										
											<!-- Blog Date Time -->
											<?php if( $show_date ) : ?>
											<span class="date-time">
												<?php echo get_the_time( get_option('date_format') ); ?>
											</span>
											<?php endif; ?>
											
											<!-- Blog Categories -->
											<?php if( $show_categories ) : ?>
											<span class="cats-link">
												<?php echo get_the_category_list(', '); ?>
											</span>
											<?php endif; ?>

										</div>
										
									<?php endif; ?>
								</blockquote>
								
							</div>
						<?php endif; ?>
					</div>
				</article>
			<?php 
			endwhile;
		endif;
		
		wp_reset_postdata();
		
		if( $is_ajax_frontend ){
			die( ob_get_clean() );
		}
	}
}

/* Portfolio */
if( !function_exists('ts_get_portfolio_items_content') ){
	function ts_get_portfolio_items_content( $atts = array(), $posts = null ){
		global $post, $ts_portfolios;
		
		$is_ajax_frontend = wp_doing_ajax() && isset($_POST['action']) && $_POST['action'] == 'ts_portfolio_load_items';
		if( $is_ajax_frontend ){
			if( !isset($_POST['atts']) ){
				die('0');
			}
			$atts = $_POST['atts'];
			$paged = isset($_POST['paged'])?absint($_POST['paged']):1;
			
			extract($atts);
			
			$args = array(
				'post_type'				=> 'ts_portfolio'
				,'posts_per_page'		=> $limit
				,'post_status'			=> 'publish'
				,'paged' 				=> $paged
				,'orderby'				=> $orderby
				,'order'				=> $order
			);			
			
			if( $categories ){
				$args['tax_query']	= array(
								array(
									'taxonomy'	=> 'ts_portfolio_cat'
									,'field'	=> 'term_id'
									,'terms'	=> explode(',', $categories)
								)
							);
			}
			$posts = new WP_Query( $args );
			ob_start();
		}
		
		extract($atts);
		
		if( $posts->have_posts() ):
			while( $posts->have_posts() ): $posts->the_post();
				$classes = '';
				$post_terms = wp_get_post_terms($post->ID, 'ts_portfolio_cat');
				if( is_array($post_terms) ){
					foreach( $post_terms as $term ){
						$classes .= $term->slug . ' ';
					}
				}
				
				$link = esc_url(get_post_meta($post->ID, 'ts_portfolio_url', true));
				if( $link == '' ){
					$link = get_permalink();
				}
				
				/* Get Like */
				$like_num = 0;
				$user_already_like = false;
				if( is_a($ts_portfolios, 'TS_Portfolios') ){
					$like_num = $ts_portfolios->get_like( $post->ID );
					$user_already_like = $ts_portfolios->user_already_like( $post->ID );
				}
				?>
				<div class="item <?php echo esc_attr($classes) ?>">
					<div class="item-wrapper">
						<div class="portfolio-thumbnail">
							<figure>
								<a href="<?php echo esc_url($link); ?>">
								<?php 
								if( has_post_thumbnail() ){
									the_post_thumbnail( $original_image?'full':'ts_portfolio_thumb' );
								}
								?>		
								</a>								
							</figure>
						</div>
						
						<div class="portfolio-meta">
								
								<?php if( $show_like_icon ){ ?>
								<a href="#" class="like <?php echo ($user_already_like)?'already-like':'' ?>" 
									data-post_id="<?php echo esc_attr($post->ID) ?>" title="<?php echo ($user_already_like)?esc_html__('You liked it', 'themesky'):esc_html__('Like it', 'themesky') ?>"
									data-liked-title="<?php esc_html_e('You liked it', 'themesky') ?>" data-like-title="<?php esc_html_e('Like it', 'themesky') ?>">
								</a>
								<?php } ?>
								
								<?php
								$categories_list = get_the_term_list($post->ID, 'ts_portfolio_cat', '', ' ', '');
								if ( $show_categories && $categories_list ):
								?>
								<div class="cats-link">
									<?php echo $categories_list; ?>
								</div>
								<?php endif; ?>
							
								<?php if( $show_title ): ?>
								<h4 class="heading-title">
									<a href="<?php echo esc_url($link); ?>">
										<?php echo get_the_title(); ?>
									</a>
								</h4>
								<?php endif; ?>

						</div>
					</div>
				</div>
			<?php
			endwhile;
		endif;
		
		wp_reset_postdata();
		
		if( $is_ajax_frontend ){
			die( ob_get_clean() );
		}
	}
}

/* Product In Tabs */
if( !function_exists('ts_get_product_content_in_category_tab') ){
	function ts_get_product_content_in_category_tab( $atts = array(), $product_cat = '', $is_general_tab = false ){
		$is_ajax_frontend = wp_doing_ajax() && isset($_POST['action']) && $_POST['action'] == 'ts_get_product_content_in_category_tab';
		
		if( $is_ajax_frontend ){
			if( empty($_POST['atts']) ){
				die('0');
			}
			$atts = $_POST['atts'];
			$product_cat = isset($_POST['product_cat'])?$_POST['product_cat']:'';
			$is_general_tab = (isset($_POST['is_general_tab']) && $_POST['is_general_tab'])?true:false;
			
			ob_start();
		}
		
		if( $is_general_tab ){
			$atts['product_type'] = $atts['product_type_general_tab'];
		}
		
		extract($atts);
		
		$options = array(
				'show_image'			=> $show_image
				,'show_label'			=> $show_label
				,'show_title'			=> $show_title
				,'show_sku'				=> $show_sku
				,'show_price'			=> $show_price
				,'show_short_desc'		=> $show_short_desc
				,'show_categories'		=> $show_categories
				,'show_rating'			=> $show_rating
				,'show_add_to_cart'		=> $show_add_to_cart
				,'show_color_swatch'	=> $show_color_swatch
				,'number_color_swatch'	=> $number_color_swatch
			);
			
		ts_remove_product_hooks( $options );
		
		$args = array(
			'post_type'				=> 'product'
			,'post_status' 			=> 'publish'
			,'ignore_sticky_posts'	=> 1
			,'posts_per_page' 		=> $limit
			,'orderby' 				=> 'date'
			,'order' 				=> 'desc'
			,'meta_query' 			=> WC()->query->get_meta_query()
			,'tax_query'           	=> WC()->query->get_tax_query()
		);

		ts_filter_product_by_product_type($args, $product_type);
		
		if( $product_cat ){
			$args['tax_query'][] = array(
									'taxonomy' 	=> 'product_cat'
									,'terms' 	=> array_map('trim', explode(',', $product_cat))
									,'field' 	=> 'term_id'
									,'include_children' => $include_children
									);
		}
		
		if( (int)$columns <= 0 ){
			$columns = 3;
		}
		
		$old_woocommerce_loop_columns = wc_get_loop_prop('columns');
		wc_set_loop_prop('columns', $columns);

		$products = new WP_Query( $args );
		
		if( isset($show_shop_more_button, $products->found_posts, $products->post_count) && $products->found_posts == $products->post_count ){
			echo '<div class="hidden hide-shop-more"></div>';
		}
		
		$count = 0;
		
		woocommerce_product_loop_start();
		if( $products->have_posts() ){

			while( $products->have_posts() ){ 
				$products->the_post();
				
				if( $is_slider && $rows > 1 && $count % $rows == 0 ){
					echo '<div class="product-group">';
				}
				
				wc_get_template_part( 'content', 'product' );
				
				if( $is_slider && $rows > 1 && ($count % $rows == $rows - 1 || $count == $products->post_count - 1) ){
					echo '</div>';
				}
				$count++;
			}

		}
		woocommerce_product_loop_end();
		
		wp_reset_postdata();

		/* restore hooks */
		ts_restore_product_hooks();

		wc_set_loop_prop('columns', $old_woocommerce_loop_columns);
		
		if( $is_ajax_frontend ){
			die( ob_get_clean() );
		}
	}
}