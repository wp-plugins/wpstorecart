<?php 
global $wpstorecart_is_active, $wpscThemeOptions, $wpdb;
get_header(); // Get the header
if(@isset($wpStoreCart)) {
    require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
    require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
    require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
    if(!isset($_SESSION)) {
            @session_start();
    }
}
?>
			<div id="c_left"><!-- Left Content Panel  Start -->

			<?php if(($wpscThemeOptions['use_slider']=='Homepage Only' && is_front_page() && !isset($_GET['wpsc'])) || $wpscThemeOptions['use_slider']=='All Pages') { ?>
				<div class="slider"><!-- Slider Start -->
                
                	<div id="theslider">
						<?php

                                                

							$table_name = $wpdb->prefix . "wpstorecart_meta";
							$results = $wpdb->get_results("SELECT * FROM {$table_name} WHERE `type`='wpsct_slideshow';", ARRAY_A);
							if($results==false) {

							} else {
								foreach ($results as $result) {
									$exploded = explode('||',$result['value']);
									foreach ($exploded as $image) {
										$explodedagain = explode('<<<',$image);
										if(!isset($explodedagain[1])) {
											$the_img_url = $image;
											$the_link_url = '#';
										} else {
											$the_img_url = $explodedagain[0];
											$the_link_url = $explodedagain[1];										
										}
										if($the_img_url!='') {
											echo '<a href="'.$the_link_url.'"><img src="'.WP_CONTENT_URL . '/uploads/wpstorecart/'.$the_img_url.'" alt="" /></a>';
										}
									}
								}
							}
						?>						
   					</div>
       			   <script type="text/javascript">
				   /* <![CDATA[ */
         		   jQuery(document).ready(function($) {
						$('#theslider').nivoSlider({
							effect:'<?php echo $wpscThemeOptions['slider_effect']; ?>', 
							slices:15,
							animSpeed:<?php echo $wpscThemeOptions['slider_effect_speed']; ?>, 
							pauseTime:<?php echo $wpscThemeOptions['slider_pause_time']; ?>,
							startSlide:0, 
							directionNav:true, 
							directionNavHide:true, 
							keyboardNav:true, 
							pauseOnHover:true
						 });
           		    });
					/* ]]> */
          		   </script>
                
                </div><!-- Slider End -->
            <?php } // End is home ?>
				<?php if(have_posts()) {while ( have_posts() ) : the_post() ?>
				<?php if(isset($wpStoreCart)) {if($post->ID != $devOptions['mainpage'] || isset($_GET['wpsc'])) { ?>
				<div class="post-header">
					<h1 class="post-title"><a class="post-title-link" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php if(trim(get_the_title($post->ID))!=''){the_title();} else {echo '---';} ?></a></h1>
					<?php if(get_post_type()=='post') { ?>Posted <time class="post-date"><?php the_time('F d, Y'); ?></time> in <?php the_category(','); ?> by <?php the_author(); ?> <?php edit_post_link('(Edit)'); }?>
				</div>
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php
					global $post;
                                        if(@isset($wpStoreCart)) {
                                            if($post->ID==$devOptions['checkoutpage']) {
                                                    global $wpscIsCheckoutPage;
                                                    $wpscIsCheckoutPage = true; // Setting this to true creates a div around the checkout with the CSS class of .wpsc-checkout-page-contents
                                                    if(!is_user_logged_in()) {
                                                            echo '<img src="'.get_bloginfo('template_directory').'/img/checkout_progress_1.png" class="wpsc-checkout-progress" alt="" />';
                                                    } else {
                                                            echo '<img src="'.get_bloginfo('template_directory').'/img/checkout_progress_2.png" class="wpsc-checkout-progress" alt="" />';
                                                    }
                                            }
                                        }
                                        if(is_single() || is_page()) {
                                                the_content();
                                                wp_link_pages();
                                                echo '<p>'; the_tags(); echo '</p>';

                                        } else {
                                                the_excerpt();
                                        }
					?>
				</div>
				<?php if(is_single() || is_page()) { ?>
						<div class="comments-template">

						<?php comments_template(); ?>

						</div>
				<?php } ?>


				<?php } } else {
                                    // this is for displaying the pages when wpStoreCart plugin is not activated or installed
                                    ?>
                                        <div class="post-header">
                                                <h1 class="post-title"><a class="post-title-link" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php if(trim(get_the_title($post->ID))!=''){the_title();} else {echo '---';} ?></a></h1>
                                                <?php if(get_post_type()=='post') { ?>Posted <time class="post-date"><?php the_time('F d, Y'); ?></time> in <?php the_category(','); ?> by <?php the_author(); ?> <?php edit_post_link('(Edit)'); }?>
                                        </div>
                                        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                                <?php
                                                global $post;

                                                if(is_single() || is_page()) {
                                                        the_content();
                                                        wp_link_pages();
                                                        echo '<p>'; the_tags(); echo '</p>';

                                                } else {
                                                        the_excerpt();
                                                }
                                                ?>
                                        </div>
                                        <?php if(is_single() || is_page()) { ?>
                                                        <div class="comments-template">

                                                        <?php comments_template(); ?>

                                                        </div>
                                        <?php } ?>
                                    <?php 
                                } // done with the non-wpStoreCart version






				if(isset($wpStoreCart)) {
					if(is_page($devOptions['mainpage']) && !isset($_GET['wpsc'])) {
						global $wpdb, $cart, $wpsc, $wpscThemeOptions;
						
						$table_name = $wpdb->prefix . "wpstorecart_products";

						$quantity = $devOptions['itemsperpage'];
						
						if( !isset( $_GET['cpage'] ) || !is_numeric($_GET['cpage'])) {
							$startat = 0;
						} else {
							$startat = ($_GET['cpage'] - 1) * $quantity;
						}

						if($devOptions['frontpageDisplays']=='List all products' || $devOptions['frontpageDisplays']=='List newest products') {
							$sql = "SELECT * FROM `{$table_name}` ORDER BY `dateadded` DESC LIMIT {$startat}, {$quantity};";
							$total = $wpdb->get_var("SELECT COUNT(primkey) FROM `{$table_name}` ORDER BY `dateadded` DESC;");
						}
						if($devOptions['frontpageDisplays']=='List most popular products') {
							$sql = "SELECT * FROM `{$table_name}` ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC LIMIT {$startat}, {$quantity};";
							 $total = $wpdb->get_var("SELECT COUNT(primkey) FROM `{$table_name}` ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC;");
						}
						if($devOptions['frontpageDisplays']=='List all categories (Ascending)') {
							$sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` ASC LIMIT {$startat}, {$quantity};";
							$total = $wpdb->get_var("SELECT COUNT(primkey) FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` ASC;");
							$secondcss = 'wpsc-categories';
						} else {
							$secondcss = 'wpsc-products';
						}
						if($devOptions['frontpageDisplays']=='List all categories') {
							$sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` DESC LIMIT {$startat}, {$quantity};";
							$total = $wpdb->get_var("SELECT COUNT(primkey) FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` DESC;");
							$secondcss = 'wpsc-categories';
						} else {
							$secondcss = 'wpsc-products';
						}
						$results = $wpdb->get_results( $sql , ARRAY_A );

						
						$comments_per_page = $quantity;
						$page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;

						if($devOptions['displayThumb']=='true') {
							$usepictures='true';
							$maxImageWidth = $devOptions['wpStoreCartwidth'];
							$maxImageHeight = $devOptions['wpStoreCartheight'];
						}
						if($devOptions['displayintroDesc']=='true') {
							$usetext='true';
						}

						// If we're dealing with categories, we have different fields to deal with than products.
						if($devOptions['frontpageDisplays']=='List all categories') {
							if(isset($results)) {
									foreach ($results as $result) {
											if(trim($result['thumbnail']=='')) {
												$result['thumbnail'] = WP_PLUGIN_URL.'/wpstorecart/images/default_product_img.jpg';
											}
											if($result['postid'] == 0 || $result['postid'] == '') { // If there's no dedicated category pages, use the default
												if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
													$permalink = get_permalink($devOptions['mainpage']) .'?wpsc=lc&wpsccat='.$result['primkey'];
												} else {
													$permalink = get_permalink($devOptions['mainpage']) .'&wpsc=lc&wpsccat='.$result['primkey'];
												}
											} else {
												$permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
											}
											if($devOptions['displayType']=='grid'){
													echo '<div class="wpsc-grid '.$secondcss.'">';
											}
											if($devOptions['displayType']=='list'){
													echo '<div class="wpsc-list '.$secondcss.'">';
											}
											if($usetext=='true') {
													echo '<p><a href="'.$permalink.'">'.$result['category'].'</a></p>';
											}
											if($usepictures=='true' || $result['thumbnail']!='' ) {
													echo '<center><a href="'.$permalink.'"><img class="wpsc-thumbnail" src="'.$result['thumbnail'].'" alt="'.htmlentities($result['category']).'"';if($maxImageWidth>1 || $maxImageHeight>1) { echo'style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} echo '/></a></center>';
											}
											if($devOptions['displayintroDesc']=='true'){
													echo '<p style="display:none;">'.$result['description'].'</p>';
											}
											echo '</div>';
									}
									echo '<div class="wpsc-clear"></div>';
							}
						} else { // This is for products:
							if(isset($results)) {
									foreach ($results as $result) {
											$permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
											if($devOptions['displayType']=='grid'){
													echo '<div class="wpsc-grid wpsc-products">';
											}
											if($devOptions['displayType']=='list'){
													echo '<div class="wpsc-list wpsc-products">';
											}
											if($usetext=='true') {
													echo '<a href="'.$permalink.'"><ins><h1 class="wpsc-h1">'.$result['name'].'</h1></ins></a>';
											}
											if($usepictures=='true') {
													echo '<center><div style="min-width:128px;min-height:125px;width:128px;height:125px;"><a href="'.$permalink.'"><img id="example-target-'.$result['primkey'].'" class="wpsc-thumbnail tooltip-target" src="'.$result['thumbnail'].'" alt="'.htmlentities($result['name']).'"';if($maxImageWidth>1 || $maxImageHeight>1) { echo ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} echo '/></a></div></center>';
											}
											if($devOptions['displayintroDesc']=='true'){
													echo '<div class="tooltip-content" id="example-content-'.$result['primkey'].'"><center><img src="'.$result['thumbnail'].'" alt="'.htmlentities($result['name']).'" class="insideimg" /></center><br />'.substr($result['introdescription'], 0, 255).'...</div>';
											}
											if($devOptions['displayAddToCart']=='true'){
															// Flat rate shipping implmented here:
															if($devOptions['flatrateshipping']=='all_single') {
																$result['shipping'] = $devOptions['flatrateamount'];
															} elseif($devOptions['flatrateshipping']=='off' || $devOptions['flatrateshipping']=='all_global') {
																$result['shipping'] = '0.00';
															}
													echo '
													
													<form method="post" action="">

															<input type="hidden" name="my-item-id" value="'.$result['primkey'].'" />
															<input type="hidden" name="my-item-primkey" value="'.$result['primkey'].'" />
															<input type="hidden" name="my-item-name" value="'.htmlentities($result['name']).'" />
															<input type="hidden" name="my-item-price" value="'.$result['price'].'" />
															<input type="hidden" name="my-item-shipping" value="'.$result['shipping'].'" />
                                                                                                                        <input type="hidden" id="my-item-img" name="my-item-img" value="'.$result['thumbnail'].'" />
                                                                                                                        <input type="hidden" id="my-item-url" name="my-item-url" value="'.get_permalink($result['postid']).'" />
                                                                                                                        <input type="hidden" id="my-item-tax" name="my-item-tax" value="0" />
															<input type="hidden" name="my-item-qty" value="1" />
															<input type="hidden" name="my-add-button" value="" />

													';
													echo '<div class="buttons">';
													if($result['useinventory']==0 || ($result['useinventory']==1 && $result['inventory'] > 0) || $devOptions['storetype']=='Digital Goods Only' ) {
                                                                        $table_name30 = $wpdb->prefix . "wpstorecart_meta";
                                                                        $grabrecord = "SELECT * FROM `{$table_name30}` WHERE `type`='productvariation' AND `foreignkey`={$result['primkey']};";

                                                                        $vresults = $wpdb->get_results( $grabrecord , ARRAY_A );

                                                                        if(isset($vresults)) {
                                                                            $results_disable_add_to_cart = $wpdb->get_results("SELECT `value` FROM `{$table_name30}` WHERE `type`='disableaddtocart' AND `foreignkey`={$result['primkey']};", ARRAY_N);
                                                                            if($results_disable_add_to_cart==false ) {
                                                                                $display_add_to_cart_at_all_times = 'no';
                                                                            } else {
                                                                                if($results_disable_add_to_cart[0][0]=='yes') {
                                                                                    $display_add_to_cart_at_all_times = 'yes';
                                                                                } else {
                                                                                    $display_add_to_cart_at_all_times = 'no';
                                                                                }
                                                                            }
                                                                            if($display_add_to_cart_at_all_times=='no') { // will display the Add to Cart if there are no variations or if it is set to display automatically.
                                                                                echo '<input type="image" src="'; bloginfo('template_directory'); echo '/img/AddToCart.jpg" style="margin-left:12px;width:67px;height:25px;" id="my-add-button-fake" name="my-add-button-fake" value="" />';
                                                                            }
																			
                                                                        }													
														
													} else {
														echo $devOptions['out_of_stock'];
													}
													
													echo '<a href="'.$permalink.'"><img src="'; bloginfo('template_directory'); echo '/img/ViewInfo.jpg" style="margin-left:10px;" /></a></div>';

											

													echo '
													</form>
													';
											}

											echo  '</div>';
									}
									echo '<div class="wpsc-clear"></div>';
							}
						}
						echo '<div class="wpsc-pagination">';
						echo paginate_links( array(
							'base' => add_query_arg( 'cpage', '%#%' ),
							'format' => '',
							'type' => 'list',
							'end_size' => 15,
							'prev_text' => __('&laquo;'),
							'next_text' => __('&raquo;'),
							'total' => ceil($total / $comments_per_page),
							'current' => $page
						));						
						echo '</div>';
					} 
				}




			endwhile;}

                        echo '<div class="alignleft previouslink">';previous_posts_link(); echo '</div><div class="alignright nextlink">';next_posts_link();echo '</div>';

                        ?>



            </div><!-- Left Content Panel  End -->
<?php 
$wpscIsCheckoutPage = false;
get_sidebar(); ?> 
            
<?php get_footer(); ?>