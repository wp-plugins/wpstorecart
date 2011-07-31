<?php 
global $wpstorecart_is_active, $wpscThemeOptions, $wpdb, $wpStoreCart, $wpscIsCheckoutPage;
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
                                                    // Creates the slider
                                                    wpsct_create_slider();
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
                                        <?php if ( has_post_thumbnail() ) { the_post_thumbnail(array(714,520));}?>
				</div>
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php
					global $post;
                                        if(@isset($wpStoreCart)) {
                                            if($post->ID==$devOptions['checkoutpage']) {
                                                    global $wpscIsCheckoutPage;
                                                    $wpscIsCheckoutPage = true; // Setting this to true creates a div around the checkout with the CSS class of .wpsc-checkout-page-contents
                                                    if(!is_user_logged_in()) {
                                                            echo '<img src="'.get_template_directory_uri().'/img/checkout_progress_1.png" class="wpsc-checkout-progress" alt="" />';
                                                    } else {
                                                            echo '<img src="'.get_template_directory_uri().'/img/checkout_progress_2.png" class="wpsc-checkout-progress" alt="" />';
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
                                    wpsct_display_frontpage();
				}




			endwhile;}

                        echo '<div class="alignleft previouslink">';previous_posts_link(); echo '</div><div class="alignright nextlink">';next_posts_link();echo '</div>';

                        ?>



            </div><!-- Left Content Panel  End -->
<?php 
$wpscIsCheckoutPage = false;
get_sidebar(); ?> 
            
<?php get_footer(); ?>