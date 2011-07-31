<?php global $wpstorecart_is_active, $wpdb, $cart, $wpsc, $is_checkout,$wpscCarthasBeenCalled, $wpStoreCart,$wpscWidgetSettings ;?>           
            <div id="c_right"><!-- Right Content Panel  Start -->
            
            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Right Sidebar") ) : ?>

                <?php  if(@isset($wpStoreCart)) { ?>
            	<div class="title">Shopping Cart</div>
                <div class="content">
			<?php
                        if(@isset($wpStoreCart)) {
                            $wpscWidgetSettings = 'filler';
                            require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
                            require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
                            require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
                            if(!isset($_SESSION)) {
                                    @session_start();

                            }
                            if(@!is_object($cart)) {
                                $cart =& $_SESSION['wpsc'];
                                if(@!is_object($cart)) {
                                    $cart = new wpsc();
                                }
                            }
                            $output = NULL;
                            $old_checkout = $is_checkout;
                            $is_checkout = false;
                            $output = $cart->display_cart($wpsc);
                            $is_checkout = $old_checkout;
                            $wpscCarthasBeenCalled = true;
                            echo $output;
                            $output = NULL;
                        } else {
                            echo '<ul><li>This Theme requires the free and open source wpStoreCart <a href="http://wpstorecart.com" title="Wordpress eCommerce Plugin">Wordpress eCommerce Plugin</a></li></ul>';
                        }
                        ?>
                </div>
                <div class="bottom_line"></div>


            	<div class="title">My Account</div>
                <div class="content">
			<?php
                        if(@isset($wpStoreCart)) {
                            $devOptions = $wpStoreCart->getAdminOptions();

                            if ( is_user_logged_in() ) {
                                    if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
                                        $permalink = get_permalink($devOptions['mainpage']) .'?wpsc=orders';
                                    } else {
                                        $permalink = get_permalink($devOptions['mainpage']) .'&wpsc=orders';
                                    }
                                    $output .= '<ul>';
                                    $output .= '<li><a href="'.$permalink.'">'.$devOptions['myordersandpurchases'].'</a></li>';
                                    $output .= '<li><a href="'.wp_logout_url(get_permalink()).'">'.$devOptions['logout'].'</a></li>';
                                    $output .= '</ul>';
                            } else {

                                 $output .= '
                                <strong>'.$devOptions['login'].'</strong><br />
                                <form id="login" method="post" action="'. wp_login_url( get_permalink() ) .'">

                                    <label>'.$devOptions['username'].' <input type="text" value="" name="log" /></label>
                                    <label>'.$devOptions['password'].' <input type="password" value="" name="pwd"  /></label>
                                    <input type="submit" value="Login" />

                                </form>

                                ';
                            }

                            echo $output;
                            $output = NULL;
                        } else {
                            echo '<ul><li>This Theme requires the free and open source wpStoreCart <a href="http://wpstorecart.com" title="Wordpress eCommerce Plugin">Wordpress eCommerce Plugin</a></li></ul>';
                        }
			?>
                </div>
                <div class="bottom_line"></div>				
			
			<?php } else { // Default sidebar if wpStoreCart is not loaded. ?>
                            <div class="title"><?php _e('Categories', 'wpsc-default'); ?></div>
                            <div class="content">
                                <ul class="list-cat">
                                    <?php wp_list_categories(); ?>
                                </ul>
                            </div>
                            <div class="bottom_line"></div>

                            <div class="title"><?php _e('Archives', 'wpsc-default'); ?></div>
                            <div class="content">
                                <ul class="list-archives">
                                    <?php wp_get_archives('type=monthly'); ?>
                                </ul>
                            </div>
                            <div class="bottom_line"></div>
                
                        <?php } ?>
			<?php endif; ?>           
            </div><!-- Right Content Panel  End -->
