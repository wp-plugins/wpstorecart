<?PHP global $wpstorecart_is_active, $wpdb, $cart, $wpsc, $is_checkout,$wpscCarthasBeenCalled, $wpStoreCart,$wpscWidgetSettings ;?>           
            <div id="c_right"><!-- Right Content Panel  Start -->
            
            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Right Sidebar") ) : ?>

            	<div class="title">Shopping Cart</div>
                <div class="content">
				<?PHP
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
				?>
                </div>
                <div class="bottom_line"></div>


            	<div class="title">My Account</div>
                <div class="content">
			<?PHP
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
			?>
                </div>
                <div class="bottom_line"></div>				
			
			
			<!--
            	<div class="title">Contacts</div>
                <div class="content">
                Have some questions? Let Us answer to all your questions!!
                <li style="margin-left:8px;">&nbsp;<img src="<?php bloginfo('template_directory'); ?>/img/phone.png" /> <font size="3px;" style="font-family:'Century Gothic'">&nbsp; 888-888-888</font></li>
                <li style="margin-left:8px;"><img src="<?php bloginfo('template_directory'); ?>/img/mail.png" /> <font size="3px;" style="font-family:'Century Gothic'">&nbsp; yourmail@mail.com</font></li>
                <img src="<?php bloginfo('template_directory'); ?>/img/twitter.png" style="margin-top:6px;" />
                <img src="<?php bloginfo('template_directory'); ?>/img/facebook.png" />
                </div>
                <div class="bottom_line"></div>
            --> 
			<?php endif; ?>           
            </div><!-- Right Content Panel  End -->
