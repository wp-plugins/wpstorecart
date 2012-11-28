<?php
/*
 * ===============================================================================================================
 * wpStoreCartCheckoutWidget SIDEBAR WIDGET
 */
if (class_exists("WP_Widget")) {

	// ------------------------------------------------------------------
	// ------------------------------------------------------------------

	class wpStoreCartCategoryWidget extends WP_Widget {
		/** constructor */
		function wpStoreCartCategoryWidget() {
			parent::WP_Widget(false, $name = 'wpStoreCart Simple Categories');
		}

		/** @see WP_Widget::widget */
		function widget($args, $instance) {
			global $wpdb;
			$output = NULL;
			$table_name = $wpdb->prefix . "wpstorecart_categories";

                        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);
			$numberOfproductsToDisplay = empty($instance['numberOfproductsToDisplay']) ? '10' : $instance['numberOfproductsToDisplay'];
			$widgetShowproductImages = empty($instance['widgetShowproductImages']) ? 'false' : $instance['widgetShowproductImages'];
                        $maxImageWidth = empty($instance['maxImageWidth']) ? 'false' : $instance['maxImageWidth'];
                        $maxImageHeight = empty($instance['maxImageHeight']) ? 'false' : $instance['maxImageHeight'];

			echo $before_widget;
			if ( $title ) { echo $before_title . $title . $after_title; }
			if(is_numeric($numberOfproductsToDisplay)){
				$sql = "SELECT * FROM `{$table_name}` ORDER BY `parent` DESC LIMIT 0, {$numberOfproductsToDisplay};";
				$results = $wpdb->get_results( $sql , ARRAY_A );
				if(isset($results)) {
					foreach ($results as $result) {
                                                if($result['postid'] == 0 || $result['postid'] == '') { // If there's no dedicated category pages, use the default
                                                    if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
                                                        $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'?wpsc=lc&wpsccat='.$result['primkey'];
                                                    } else {
                                                        $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'&wpsc=lc&wpsccat='.$result['primkey'];
                                                    }
                                                } else {
                                                    $permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
                                                }
						if($widgetShowproductImages=='true') {
                                                        if(trim($result['thumbnail']=='')) {
                                                            $result['thumbnail'] = plugins_url().'/wpstorecart/images/default_product_img.jpg';
                                                        }
							$output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.$result['category'].'"'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a>';
						}
						$output .= '<p><a href="'.$permalink.'">'.$result['category'].'</a></p>';
					}
				}
			} else {
				$output .= 'wpStoreCart did not like your widget!  The number of categories to display contained non-numeric data. Please fix your widget or consult the wpStoreCart documentation for help.';
			}
			echo $output;
			echo $after_widget;
		}

		/** @see WP_Widget::update */
		function update($new_instance, $old_instance) {
			$instance['title']= strip_tags(stripslashes($new_instance['title']));
			$instance['numberOfproductsToDisplay'] = strip_tags(stripslashes($new_instance['numberOfproductsToDisplay']));
			$instance['widgetShowproductImages'] = strip_tags(stripslashes($new_instance['widgetShowproductImages']));
                        $instance['maxImageWidth'] = strip_tags(stripslashes($new_instance['maxImageWidth']));
                        $instance['maxImageHeight'] = strip_tags(stripslashes($new_instance['maxImageHeight']));
			return $instance;
		}

		/** @see WP_Widget::form */
		function form($instance) {
			@$title = esc_attr($instance['title']);
			@$numberOfproductsToDisplay = htmlspecialchars($instance['numberOfproductsToDisplay']);
			@$widgetShowproductImages = htmlspecialchars($instance['widgetShowproductImages']);
                        @$maxImageWidth = htmlspecialchars($instance['maxImageWidth']);
                        @$maxImageHeight = htmlspecialchars($instance['maxImageHeight']);

			echo '<p><label for="'. $this->get_field_id('title') .'">'; _e('Title:'); echo ' <input class="widefat" id="'. $this->get_field_id('title') .'" name="'. $this->get_field_name('title') .'" type="text" value="'. $title .'" /></label></p>';
			echo '<p style="text-align:left;"><label for="' . $this->get_field_name('numberOfproductsToDisplay') . '">' . __('Number of categories to display:') . ' <input style="width: 80px;" id="' . $this->get_field_id('numberOfproductsToDisplay') . '" name="' . $this->get_field_name('numberOfproductsToDisplay') . '" type="text" value="' . $numberOfproductsToDisplay . '" /></label></p>';
			//echo '<p style="text-align:left;"><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Show images:') . ' <input style="width: 200px;" id="' . $this->get_field_id('widgetShowproductImages') . '" name="' . $this->get_field_name('widgetShowproductImages') . '" type="text" value="' . $widgetShowproductImages . '" /></label></p>';
			echo '<p><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Show images:') . '<label for="' . $this->get_field_name('widgetShowproductImages') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_yes" name="' . $this->get_field_name('widgetShowproductImages') . '" value="true" '; if ($widgetShowproductImages == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowproductImages') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_no" name="' . $this->get_field_name('widgetShowproductImages') . '" value="false" '; if ($widgetShowproductImages == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageWidth') . '">' . __('Max thumb width:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageWidth') . '" name="' . $this->get_field_name('maxImageWidth') . '" type="text" value="' . $maxImageWidth . '" /> px</label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageHeight') . '">' . __('Max thumb height:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageHeight') . '" name="' . $this->get_field_name('maxImageHeight') . '" type="text" value="' . $maxImageHeight . '" /> px</label></p>';
		}

	}
	// ------------------------------------------------------------------
	// ------------------------------------------------------------------



	class wpStoreCartCheckoutWidget extends WP_Widget {
		/** constructor */
		function wpStoreCartCheckoutWidget() {
			parent::WP_Widget(false, $name = 'wpStoreCart Cart Contents');
		}

		/** @see WP_Widget::widget */
		function widget($args, $instance) {
			global $cart, $is_checkout,$wpscCarthasBeenCalled, $wpscWidgetSettings;
                        $wpscWidgetSettings = array();

			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);
                        $widgetShowproductImages = empty($instance['widgetShowproductImages']) ? 'false' : $instance['widgetShowproductImages'];
                        $widgetShowTax = empty($instance['widgetShowTax']) ? 'true' : $instance['widgetShowTax'];
                        $widgetShowSubtotal = empty($instance['widgetShowSubtotal']) ? 'true' : $instance['widgetShowSubtotal'];
                        $widgetShowTotal = empty($instance['widgetShowTotal']) ? 'true' : $instance['widgetShowTotal'];
                        $widgetShowShipping = empty($instance['widgetShowShipping']) ? 'true' : $instance['widgetShowShipping'];
                        $wpscWidgetSettings['iswidget']=true;
                        $wpscWidgetSettings['widgetShowTax']=$widgetShowTax;
                        $wpscWidgetSettings['widgetShowSubtotal']=$widgetShowSubtotal;
                        $wpscWidgetSettings['widgetShowTotal']=$widgetShowTotal;
                        $wpscWidgetSettings['widgetShowShipping']=$widgetShowShipping;
                        
                        $wpsc_shoppingcart = new wpsc_shoppingcart();

			$output = NULL;

			echo $before_widget;
			if ( $title ) { echo $before_title . $title . $after_title; }
			$old_checkout = $is_checkout;
			$is_checkout = false;
                        if($widgetShowproductImages=='true') {
                           $is_checkout = true;
                        }
			$output = $wpsc_shoppingcart->display_cart();
			$is_checkout = $old_checkout;
                        $wpscCarthasBeenCalled = true;
			echo $output;
			echo $after_widget;
		}

		/** @see WP_Widget::update */
		function update($new_instance, $old_instance) {
			$instance['title']= strip_tags(stripslashes($new_instance['title']));
                        $instance['widgetShowproductImages'] = strip_tags(stripslashes($new_instance['widgetShowproductImages']));
                        $instance['widgetShowShipping'] = strip_tags(stripslashes($new_instance['widgetShowShipping']));
                        $instance['widgetShowTax'] = strip_tags(stripslashes($new_instance['widgetShowTax']));
                        $instance['widgetShowSubtotal'] = strip_tags(stripslashes($new_instance['widgetShowSubtotal']));
                        $instance['widgetShowTotal'] = strip_tags(stripslashes($new_instance['widgetShowTotal']));
			return $instance;
		}

		/** @see WP_Widget::form */
		function form($instance) {
			@$title = esc_attr($instance['title']);
                        @$widgetShowproductImages = htmlspecialchars($instance['widgetShowproductImages']);
                        @$widgetShowShipping = htmlspecialchars($instance['widgetShowShipping']);
                        @$widgetShowSubtotal = htmlspecialchars($instance['widgetShowSubtotal']);
                        @$widgetShowTax = htmlspecialchars($instance['widgetShowTax']);
                        @$widgetShowTotal = htmlspecialchars($instance['widgetShowTotal']);
			echo '<p><label for="'. $this->get_field_id('title') .'">'; _e('Title:'); echo ' <input class="widefat" id="'. $this->get_field_id('title') .'" name="'. $this->get_field_name('title') .'" type="text" value="'. $title .'" /></label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Use as the final checkout:') . '<br /><label for="' . $this->get_field_name('widgetShowproductImages') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_yes" name="' . $this->get_field_name('widgetShowproductImages') . '" value="true" '; if ($widgetShowproductImages == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowproductImages') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_no" name="' . $this->get_field_name('widgetShowproductImages') . '" value="false" '; if ($widgetShowproductImages == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowShipping') . '">' . __('Show shipping costs:') . '<br /><label for="' . $this->get_field_name('widgetShowShipping') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowShipping') . '_yes" name="' . $this->get_field_name('widgetShowShipping') . '" value="true" '; if ($widgetShowShipping == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowShipping') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowShipping') . '_no" name="' . $this->get_field_name('widgetShowShipping') . '" value="false" '; if ($widgetShowShipping == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowTax') . '">' . __('Show tax:') . '<br /><label for="' . $this->get_field_name('widgetShowTax') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowTax') . '_yes" name="' . $this->get_field_name('widgetShowTax') . '" value="true" '; if ($widgetShowTax == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowTax') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowTax') . '_no" name="' . $this->get_field_name('widgetShowTax') . '" value="false" '; if ($widgetShowTax == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowSubtotal') . '">' . __('Show subtotal without shipping:') . '<br /><label for="' . $this->get_field_name('widgetShowSubtotal') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowSubtotal') . '_yes" name="' . $this->get_field_name('widgetShowSubtotal') . '" value="true" '; if ($widgetShowSubtotal == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowSubtotal') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowSubtotal') . '_no" name="' . $this->get_field_name('widgetShowSubtotal') . '" value="false" '; if ($widgetShowSubtotal == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowTotal') . '">' . __('Show total, including any shipping:') . '<br /><label for="' . $this->get_field_name('widgetShowTotal') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowTotal') . '_yes" name="' . $this->get_field_name('widgetShowTotal') . '" value="true" '; if ($widgetShowTotal == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowTotal') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowTotal') . '_no" name="' . $this->get_field_name('widgetShowTotal') . '" value="false" '; if ($widgetShowTotal == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
		}

	}

	// ------------------------------------------------------------------
	// ------------------------------------------------------------------


        class wpStoreCartLoginWidget extends WP_Widget {
		/** constructor */
		function wpStoreCartLoginWidget() {
			parent::WP_Widget(false, $name = 'wpStoreCart User Account/Login');
		}

		/** @see WP_Widget::widget */
		function widget($args, $instance) {		
			$output = NULL;
			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);

                        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

			echo $before_widget;
			if ( $title ) { echo $before_title . $title . $after_title; }


                        if($wpStoreCartOptions['orderspage']=='') {
                            if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
                                $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'?wpsc=orders';
                            } else {
                                $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'&wpsc=orders';
                            }
                        } else {
                             $permalink = get_permalink($wpStoreCartOptions['orderspage']);
                        }
                        
                        if ( is_user_logged_in() ) {
                                $output .= '<ul>';
                                $output .= '<li><a href="'.$permalink.'">'.$wpStoreCartOptions['myordersandpurchases'].'</a></li>';
                                $output .= '<li><a href="'.wp_logout_url(get_permalink()).'">'.$wpStoreCartOptions['logout'].'</a></li>';

                                $output .= '</ul>';
                        } else {

                            if($wpStoreCartOptions['requireregistration']!='disable') {
                                $output .= '
                                <strong>'.$wpStoreCartOptions['login'].'</strong><br />
                                <form id="login" method="post" action="'. wp_login_url( get_permalink() ) .'">

                                    <label>'.$wpStoreCartOptions['username'].' <input type="text" value="" name="log" /></label><br />
                                    <label>'.$wpStoreCartOptions['password'].' <input type="password" value="" name="pwd"  /></label><br />
                                    <input type="submit" value="Login" />

                                </form>
                                ';
                            }
                            
                             if($wpStoreCartOptions['requireregistration']=='false' || $wpStoreCartOptions['requireregistration']=='disable') {
                                $output .= '<ul>';
                                
                                if(@!isset($_SESSION)) {
                                        @session_start();
                                }                                
                                
                                if(@isset($_SESSION['wpsc_email'])) {
                                    if($wpStoreCartOptions['orderspage']=='') {
                                        if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
                                            $permalink2 = get_permalink($wpStoreCartOptions['mainpage']) .'?wpsc=orders&wpsc_guest_clear=1';
                                        } else {
                                            $permalink2 = get_permalink($wpStoreCartOptions['mainpage']) .'&wpsc=orders&wpsc_guest_clear=1';
                                        }
                                    } else {
                                        if(strpos(get_permalink($wpStoreCartOptions['orderspage']),'?')===false) {
                                            $permalink2 = get_permalink($wpStoreCartOptions['orderspage']) .'?wpsc=orders&wpsc_guest_clear=1';
                                        } else {
                                            $permalink2 = get_permalink($wpStoreCartOptions['orderspage']) .'&wpsc=orders&wpsc_guest_clear=1';
                                        }                                        
                                    }                                    
                                    $output .= '<li>'.__('Signed in as a guest:','wpstorecart').' '.$_SESSION['wpsc_email'].'</li>';
                                    $output .= '<li><a href="'.$permalink2.'">'.__('Log out of guest account','wpstorecart').'</a></li>';
                                }
                                
                                $output .= '<li><a href="'.$permalink.'">'.$wpStoreCartOptions['myordersandpurchases'].'</a></li>';
                                $output .= '</ul>';

                             }
                        }

			echo $output;
			echo $after_widget;
		}

		/** @see WP_Widget::update */
		function update($new_instance, $old_instance) {	
			$instance['title']= strip_tags(stripslashes($new_instance['title']));

			return $instance;
		}

		/** @see WP_Widget::form */
		function form($instance) {				
			@$title = esc_attr($instance['title']);

			echo '<p><label for="'. $this->get_field_id('title') .'">'; _e('Title:'); echo ' <input class="widefat" id="'. $this->get_field_id('title') .'" name="'. $this->get_field_name('title') .'" type="text" value="'. $title .'" /></label></p>';

		}

	}
	
	// ------------------------------------------------------------------
	// ------------------------------------------------------------------ 
 
	class wpStoreCartTopproductsWidget extends WP_Widget {
		/** constructor */
		function wpStoreCartTopproductsWidget() {
			parent::WP_Widget(false, $name = 'wpStoreCart Top Products');
		}

		/** @see WP_Widget::widget */
		function widget($args, $instance) {		
			global $wpdb, $current_user;
                        get_currentuserinfo();
                        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
			$output = NULL;
			$table_name = $wpdb->prefix . "wpstorecart_products";
		
			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);
			$numberOfproductsToDisplay = empty($instance['numberOfproductsToDisplay']) ? '10' : $instance['numberOfproductsToDisplay'];
			$widgetShowproductImages = empty($instance['widgetShowproductImages']) ? 'false' : $instance['widgetShowproductImages'];
                        $maxImageWidth = empty($instance['maxImageWidth']) ? 'false' : $instance['maxImageWidth'];
                        $maxImageHeight = empty($instance['maxImageHeight']) ? 'false' : $instance['maxImageHeight'];

			echo $before_widget;
			if ( $title ) { echo $before_title . $title . $after_title; }
			if(is_numeric($numberOfproductsToDisplay)){
				$sql = "SELECT * FROM `{$table_name}` WHERE `status`='publish' AND `producttype`='product' ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC LIMIT 0, {$numberOfproductsToDisplay};";
				$results = $wpdb->get_results( $sql , ARRAY_A );
				if(isset($results)) {
					foreach ($results as $result) {
                                            // Group code
                                            $groupDiscount = wpscGroupDiscounts($result['category'], $current_user->ID);
                                            if ($groupDiscount['can_see_this_category']==false && $wpStoreCartOptions['gd_enable']=='true') {

                                            } else {
                                                $permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
                                                if($widgetShowproductImages=='true') {
                                                        $output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.$result['name'].'"'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a>';
                                                }
                                                $output .= '<p><a href="'.$permalink.'">'.stripslashes($result['name']).'</a></p>';
                                            }
					}
				}
			} else {
				$output .= 'wpStoreCart did not like your widget!  The number of products to display contained non-numeric data. Please fix your widget or consult the wpStoreCart documentation for help.';
			}
			echo $output;
			echo $after_widget;
		}

		/** @see WP_Widget::update */
		function update($new_instance, $old_instance) {	
			$instance['title']= strip_tags(stripslashes($new_instance['title']));
			$instance['numberOfproductsToDisplay'] = strip_tags(stripslashes($new_instance['numberOfproductsToDisplay']));
			$instance['widgetShowproductImages'] = strip_tags(stripslashes($new_instance['widgetShowproductImages']));
                        $instance['maxImageWidth'] = strip_tags(stripslashes($new_instance['maxImageWidth']));
                        $instance['maxImageHeight'] = strip_tags(stripslashes($new_instance['maxImageHeight']));
			return $instance;
		}

		/** @see WP_Widget::form */
		function form($instance) {				
			@$title = esc_attr($instance['title']);
			@$numberOfproductsToDisplay = htmlspecialchars($instance['numberOfproductsToDisplay']);
			@$widgetShowproductImages = htmlspecialchars($instance['widgetShowproductImages']);
                        @$maxImageWidth = htmlspecialchars($instance['maxImageWidth']);
                        @$maxImageHeight = htmlspecialchars($instance['maxImageHeight']);

			echo '<p><label for="'. $this->get_field_id('title') .'">'; _e('Title:'); echo ' <input class="widefat" id="'. $this->get_field_id('title') .'" name="'. $this->get_field_name('title') .'" type="text" value="'. $title .'" /></label></p>';
			echo '<p style="text-align:left;"><label for="' . $this->get_field_name('numberOfproductsToDisplay') . '">' . __('Number of products to display:') . ' <input style="width: 80px;" id="' . $this->get_field_id('numberOfproductsToDisplay') . '" name="' . $this->get_field_name('numberOfproductsToDisplay') . '" type="text" value="' . $numberOfproductsToDisplay . '" /></label></p>';
			//echo '<p style="text-align:left;"><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Show images:') . ' <input style="width: 200px;" id="' . $this->get_field_id('widgetShowproductImages') . '" name="' . $this->get_field_name('widgetShowproductImages') . '" type="text" value="' . $widgetShowproductImages . '" /></label></p>';
			echo '<p><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Show images:') . '<label for="' . $this->get_field_name('widgetShowproductImages') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_yes" name="' . $this->get_field_name('widgetShowproductImages') . '" value="true" '; if ($widgetShowproductImages == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowproductImages') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_no" name="' . $this->get_field_name('widgetShowproductImages') . '" value="false" '; if ($widgetShowproductImages == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageWidth') . '">' . __('Max thumb width:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageWidth') . '" name="' . $this->get_field_name('maxImageWidth') . '" type="text" value="' . $maxImageWidth . '" /> px</label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageHeight') . '">' . __('Max thumb height:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageHeight') . '" name="' . $this->get_field_name('maxImageHeight') . '" type="text" value="' . $maxImageHeight . '" /> px</label></p>';
		}

	} 
	// ------------------------------------------------------------------
	// ------------------------------------------------------------------
	
	class wpStoreCartRecentproductsWidget extends WP_Widget {
		/** constructor */
		function wpStoreCartRecentproductsWidget() {
			parent::WP_Widget(false, $name = 'wpStoreCart Recent Products');
		}

		/** @see WP_Widget::widget */
		function widget($args, $instance) {		
			global $wpdb, $current_user;
                        get_currentuserinfo();
                        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
			$output = NULL;
			$table_name = $wpdb->prefix . "wpstorecart_products";
		
			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);
			$numberOfproductsToDisplay = empty($instance['numberOfproductsToDisplay']) ? '10' : $instance['numberOfproductsToDisplay'];
			$widgetShowproductImages = empty($instance['widgetShowproductImages']) ? 'false' : $instance['widgetShowproductImages'];
                        $maxImageWidth = empty($instance['maxImageWidth']) ? 'false' : $instance['maxImageWidth'];
                        $maxImageHeight = empty($instance['maxImageHeight']) ? 'false' : $instance['maxImageHeight'];

			echo $before_widget;
			if ( $title ) { echo $before_title . $title . $after_title; }
			if(is_numeric($numberOfproductsToDisplay)){
				$sql = "SELECT * FROM `{$table_name}` WHERE `status`='publish' AND `producttype`='product' ORDER BY `dateadded` DESC LIMIT 0, {$numberOfproductsToDisplay};";
				$results = $wpdb->get_results( $sql , ARRAY_A );
				if(isset($results)) {
					foreach ($results as $result) {
                                            // Group code
                                            $groupDiscount = wpscGroupDiscounts($result['category'], $current_user->ID);
                                            if ($groupDiscount['can_see_this_category']==false && $wpStoreCartOptions['gd_enable']=='true') {

                                            } else {                                            
						$permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
						if($widgetShowproductImages=='true') {
							$output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.$result['name'].'"'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a>';
						}
						$output .= '<p><a href="'.$permalink.'">'.stripslashes($result['name']).'</a></p>';
                                            }
					}
				}
			} else {
				$output .= 'wpStoreCart did not like your widget!  The number of products to display contained non-numeric data. Please fix your widget or consult the wpStoreCart documentation for help.';
			}
			echo $output;
			echo $after_widget;
		}

		/** @see WP_Widget::update */
		function update($new_instance, $old_instance) {	
			$instance['title']= strip_tags(stripslashes($new_instance['title']));
			$instance['numberOfproductsToDisplay'] = strip_tags(stripslashes($new_instance['numberOfproductsToDisplay']));
			$instance['widgetShowproductImages'] = strip_tags(stripslashes($new_instance['widgetShowproductImages']));
                        $instance['maxImageWidth'] = strip_tags(stripslashes($new_instance['maxImageWidth']));
                        $instance['maxImageHeight'] = strip_tags(stripslashes($new_instance['maxImageHeight']));
                        
			return $instance;
		}

		/** @see WP_Widget::form */
		function form($instance) {				
			@$title = esc_attr($instance['title']);
			@$numberOfproductsToDisplay = htmlspecialchars($instance['numberOfproductsToDisplay']);
			@$widgetShowproductImages = htmlspecialchars($instance['widgetShowproductImages']);
                        @$maxImageWidth = htmlspecialchars($instance['maxImageWidth']);
                        @$maxImageHeight = htmlspecialchars($instance['maxImageHeight']);

			echo '<p><label for="'. $this->get_field_id('title') .'">'; _e('Title:'); echo ' <input class="widefat" id="'. $this->get_field_id('title') .'" name="'. $this->get_field_name('title') .'" type="text" value="'. $title .'" /></label></p>';
			echo '<p style="text-align:left;"><label for="' . $this->get_field_name('numberOfproductsToDisplay') . '">' . __('Number of products to display:') . ' <input style="width: 80px;" id="' . $this->get_field_id('numberOfproductsToDisplay') . '" name="' . $this->get_field_name('numberOfproductsToDisplay') . '" type="text" value="' . $numberOfproductsToDisplay . '" /></label></p>';
			//echo '<p style="text-align:left;"><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Show images:') . ' <input style="width: 200px;" id="' . $this->get_field_id('widgetShowproductImages') . '" name="' . $this->get_field_name('widgetShowproductImages') . '" type="text" value="' . $widgetShowproductImages . '" /></label></p>';
			echo '<p><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Show images:') . '<label for="' . $this->get_field_name('widgetShowproductImages') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_yes" name="' . $this->get_field_name('widgetShowproductImages') . '" value="true" '; if ($widgetShowproductImages == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowproductImages') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_no" name="' . $this->get_field_name('widgetShowproductImages') . '" value="false" '; if ($widgetShowproductImages == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageWidth') . '">' . __('Max thumb width:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageWidth') . '" name="' . $this->get_field_name('maxImageWidth') . '" type="text" value="' . $maxImageWidth . '" /> px</label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageHeight') . '">' . __('Max thumb height:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageHeight') . '" name="' . $this->get_field_name('maxImageHeight') . '" type="text" value="' . $maxImageHeight . '" /> px</label></p>';

		}

	} 	


	// wpStoreCartPaymentsWidget ------------------------------------------------------------------
	// ------------------------------------------------------------------

	class wpStoreCartPaymentsWidget extends WP_Widget {
		/** constructor */
		function wpStoreCartPaymentsWidget() {
			parent::WP_Widget(false, $name = 'wpStoreCart Payments Accepted');
		}

		/** @see WP_Widget::widget */
		function widget($args, $instance) {
			$output = NULL;

			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);
                        $widgetShowAmericanExpress = empty($instance['widgetShowAmericanExpress']) ? 'false' : $instance['widgetShowAmericanExpress'];
                        $widgetShowVisa = empty($instance['widgetShowVisa']) ? 'false' : $instance['widgetShowVisa'];
                        $widgetShowDiscover = empty($instance['widgetShowDiscover']) ? 'false' : $instance['widgetShowDiscover'];
                        $widgetShowMasterCard = empty($instance['widgetShowMasterCard']) ? 'false' : $instance['widgetShowMasterCard'];
                        $widgetShowDinersClub = empty($instance['widgetShowDinersClub']) ? 'false' : $instance['widgetShowDinersClub'];
                        $widgetShowJCB = empty($instance['widgetShowJCB']) ? 'false' : $instance['widgetShowJCB'];
                        $widgetShowPayPal = empty($instance['widgetShowPayPal']) ? 'false' : $instance['widgetShowPayPal'];
                        $widgetShowAuthorizeNet = empty($instance['widgetShowAuthorizeNet']) ? 'false' : $instance['widgetShowAuthorizeNet'];
                        $widgetShow2Checkout = empty($instance['widgetShow2Checkout']) ? 'false' : $instance['widgetShow2Checkout'];
                        $widgetStyle = apply_filters('widgetStyle', $instance['widgetStyle']);
                        $maxImageWidth = empty($instance['maxImageWidth']) ? 'false' : $instance['maxImageWidth'];
                        $maxImageHeight = empty($instance['maxImageHeight']) ? 'false' : $instance['maxImageHeight'];
                        if(trim($widgetStyle!='')) {
                            $widgetStyle = ' style="'.$widgetStyle.'"';
                        } else {
                            $widgetStyle = '';
                        }

			echo $before_widget;
			if ( $title ) { echo $before_title . $title . $after_title; }
                        if($widgetShowVisa=='true') {$output .= '<img '.$widgetStyle.' src="'.plugins_url().'/wpstorecart/images/payment/payment_types-visa.png" alt=""'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/>';}
                        if($widgetShowMasterCard=='true') {$output .= '<img '.$widgetStyle.' src="'.plugins_url().'/wpstorecart/images/payment/payment_types-mastercard.png" alt=""'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/>';}
                        if($widgetShowAmericanExpress=='true') {$output .= '<img '.$widgetStyle.' src="'.plugins_url().'/wpstorecart/images/payment/payment_types-american_express.png" alt=""'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/>';}
                        if($widgetShowDiscover=='true') {$output .= '<img '.$widgetStyle.' src="'.plugins_url().'/wpstorecart/images/payment/payment_types-discover.png" alt=""'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/>';}
                        if($widgetShowDinersClub=='true') {$output .= '<img '.$widgetStyle.' src="'.plugins_url().'/wpstorecart/images/payment/payment_types-dinersclub.png" alt=""'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/>';}
                        if($widgetShowJCB=='true') {$output .= '<img '.$widgetStyle.' src="'.plugins_url().'/wpstorecart/images/payment/payment_types-jcb.png" alt=""'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/>';}
                        if($widgetShowPayPal=='true') {$output .= '<img '.$widgetStyle.' src="'.plugins_url().'/wpstorecart/images/payment/payment_types-paypal.png" alt=""'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/>';}
                        if($widgetShowAuthorizeNet=='true') {$output .= '<img '.$widgetStyle.' src="'.plugins_url().'/wpstorecart/images/payment/payment_types-authorize.net.png" alt=""'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/>';}
                        if($widgetShow2Checkout=='true') {$output .= '<img '.$widgetStyle.' src="'.plugins_url().'/wpstorecart/images/payment/payment_types-2co.png" alt=""'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/>';}
                        //if($wpStoreCartOptions['allowlibertyreserve']=='true') {$output .= '<img src="'.plugins_url('/images/payment/' , __FILE__).'" alt=""'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/>';}
                        //if($wpStoreCartOptions['allowcheckmoneyorder']=='true') {$output .= '<img src="'.plugins_url('/images/payment/' , __FILE__).'" alt=""'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/>';}
                        //if($wpStoreCartOptions['allowqbms']=='true') {$output .= '<img src="'.plugins_url('/images/payment/' , __FILE__).'" alt=""'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/>';}
			echo $output;
			echo $after_widget;
		}

		/** @see WP_Widget::update */
		function update($new_instance, $old_instance) {
			$instance['title']= strip_tags(stripslashes($new_instance['title']));
                        $instance['widgetShowAmericanExpress'] = strip_tags(stripslashes($new_instance['widgetShowAmericanExpress']));
                        $instance['widgetShowVisa'] = strip_tags(stripslashes($new_instance['widgetShowVisa']));
                        $instance['widgetShowDiscover'] = strip_tags(stripslashes($new_instance['widgetShowDiscover']));
                        $instance['widgetShowMasterCard'] = strip_tags(stripslashes($new_instance['widgetShowMasterCard']));
                        $instance['widgetShowDinersClub'] = strip_tags(stripslashes($new_instance['widgetShowDinersClub']));
                        $instance['widgetShowJCB'] = strip_tags(stripslashes($new_instance['widgetShowJCB']));
                        $instance['widgetShowPayPal'] = strip_tags(stripslashes($new_instance['widgetShowPayPal']));
                        $instance['widgetShowAuthorizeNet'] = strip_tags(stripslashes($new_instance['widgetShowAuthorizeNet']));
                        $instance['widgetShow2Checkout'] = strip_tags(stripslashes($new_instance['widgetShow2Checkout']));
                        $instance['widgetStyle'] = strip_tags(stripslashes($new_instance['widgetStyle']));
                        $instance['maxImageWidth'] = strip_tags(stripslashes($new_instance['maxImageWidth']));
                        $instance['maxImageHeight'] = strip_tags(stripslashes($new_instance['maxImageHeight']));
			return $instance;
		}

		/** @see WP_Widget::form */
		function form($instance) {
			@$title = esc_attr($instance['title']);
                        @$widgetShowAmericanExpress = htmlspecialchars($instance['widgetShowAmericanExpress']);
                        @$widgetShowVisa = htmlspecialchars($instance['widgetShowVisa']);
                        @$widgetShowDiscover = htmlspecialchars($instance['widgetShowDiscover']);
                        @$widgetShowMasterCard = htmlspecialchars($instance['widgetShowMasterCard']);
                        @$widgetShowDinersClub = htmlspecialchars($instance['widgetShowDinersClub']);
                        @$widgetShowJCB = htmlspecialchars($instance['widgetShowJCB']);
                        @$widgetShowPayPal = htmlspecialchars($instance['widgetShowPayPal']);
                        @$widgetShowAuthorizeNet = htmlspecialchars($instance['widgetShowAuthorizeNet']);
                        @$widgetShow2Checkout = htmlspecialchars($instance['widgetShow2Checkout']);
                        @$widgetStyle = htmlspecialchars($instance['widgetStyle']);
                        @$maxImageWidth = htmlspecialchars($instance['maxImageWidth']);
                        @$maxImageHeight = htmlspecialchars($instance['maxImageHeight']);
                        if(!is_numeric($maxImageWidth) || $maxImageWidth=='') {$maxImageWidth = '50';}
                        if(!is_numeric($maxImageHeight) || $maxImageHeight=='') {$maxImageHeight = '30';}

			echo '<p><label for="'. $this->get_field_id('title') .'">'; _e('Title:'); echo ' <input class="widefat" id="'. $this->get_field_id('title') .'" name="'. $this->get_field_name('title') .'" type="text" value="'. $title .'" /></label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowVisa') . '">' . __('VISA:') . '<label for="' . $this->get_field_name('widgetShowVisa') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowVisa') . '_yes" name="' . $this->get_field_name('widgetShowVisa') . '" value="true" '; if ($widgetShowVisa == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowVisa') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowVisa') . '_no" name="' . $this->get_field_name('widgetShowVisa') . '" value="false" '; if ($widgetShowVisa == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowMasterCard') . '">' . __('MasterCard:') . '<label for="' . $this->get_field_name('widgetShowMasterCard') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowMasterCard') . '_yes" name="' . $this->get_field_name('widgetShowMasterCard') . '" value="true" '; if ($widgetShowMasterCard == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowMasterCard') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowMasterCard') . '_no" name="' . $this->get_field_name('widgetShowMasterCard') . '" value="false" '; if ($widgetShowMasterCard == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowAmericanExpress') . '">' . __('American Express:') . '<label for="' . $this->get_field_name('widgetShowAmericanExpress') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowAmericanExpress') . '_yes" name="' . $this->get_field_name('widgetShowAmericanExpress') . '" value="true" '; if ($widgetShowAmericanExpress == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowAmericanExpress') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowAmericanExpress') . '_no" name="' . $this->get_field_name('widgetShowAmericanExpress') . '" value="false" '; if ($widgetShowAmericanExpress == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowDiscover') . '">' . __('Discover:') . '<label for="' . $this->get_field_name('widgetShowDiscover') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowDiscover') . '_yes" name="' . $this->get_field_name('widgetShowDiscover') . '" value="true" '; if ($widgetShowDiscover == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowDiscover') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowDiscover') . '_no" name="' . $this->get_field_name('widgetShowDiscover') . '" value="false" '; if ($widgetShowDiscover == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowDinersClub') . '">' . __('Diners Club:') . '<label for="' . $this->get_field_name('widgetShowDinersClub') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowDinersClub') . '_yes" name="' . $this->get_field_name('widgetShowDinersClub') . '" value="true" '; if ($widgetShowDinersClub == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowDinersClub') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowDinersClub') . '_no" name="' . $this->get_field_name('widgetShowDinersClub') . '" value="false" '; if ($widgetShowDinersClub == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowJCB') . '">' . __('JCB:') . '<label for="' . $this->get_field_name('widgetShowJCB') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowJCB') . '_yes" name="' . $this->get_field_name('widgetShowJCB') . '" value="true" '; if ($widgetShowJCB == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowJCB') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowJCB') . '_no" name="' . $this->get_field_name('widgetShowJCB') . '" value="false" '; if ($widgetShowJCB == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowPayPal') . '">' . __('PayPal:') . '<label for="' . $this->get_field_name('widgetShowPayPal') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowPayPal') . '_yes" name="' . $this->get_field_name('widgetShowPayPal') . '" value="true" '; if ($widgetShowPayPal == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowPayPal') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowPayPal') . '_no" name="' . $this->get_field_name('widgetShowPayPal') . '" value="false" '; if ($widgetShowPayPal == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowAuthorizeNet') . '">' . __('Authorize.Net:') . '<label for="' . $this->get_field_name('widgetShowAuthorizeNet') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowAuthorizeNet') . '_yes" name="' . $this->get_field_name('widgetShowAuthorizeNet') . '" value="true" '; if ($widgetShowAuthorizeNet == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowAuthorizeNet') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowAuthorizeNet') . '_no" name="' . $this->get_field_name('widgetShowAuthorizeNet') . '" value="false" '; if ($widgetShowAuthorizeNet == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShow2Checkout') . '">' . __('2Checkout:') . '<label for="' . $this->get_field_name('widgetShow2Checkout') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShow2Checkout') . '_yes" name="' . $this->get_field_name('widgetShow2Checkout') . '" value="true" '; if ($widgetShow2Checkout == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShow2Checkout') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShow2Checkout') . '_no" name="' . $this->get_field_name('widgetShow2Checkout') . '" value="false" '; if ($widgetShow2Checkout == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>';
                        echo '<p><label for="'. $this->get_field_id('widgetStyle') .'">'; _e('Inline CSS:'); echo ' <input class="widefat" id="'. $this->get_field_id('widgetStyle') .'" name="'. $this->get_field_name('widgetStyle') .'" type="text" value="'. $widgetStyle .'" /></label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageWidth') . '">' . __('Max thumb width:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageWidth') . '" name="' . $this->get_field_name('maxImageWidth') . '" type="text" value="' . $maxImageWidth . '" /> px</label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageHeight') . '">' . __('Max thumb height:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageHeight') . '" name="' . $this->get_field_name('maxImageHeight') . '" type="text" value="' . $maxImageHeight . '" /> px</label></p>';
		}

	}
	// ------------------------------------------------------------------
	// ------------------------------------------------------------------


}
/**
 * ===============================================================================================================
 * END wpStoreCartTopproductsWidget SIDEBAR WIDGET
 */

	add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartCheckoutWidget");')); // Register the widget: wpStoreCartTopproductsWidget
        add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartLoginWidget");')); // Register the widget: wpStoreCartTopproductsWidget
        add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartTopproductsWidget");')); // Register the widget: wpStoreCartTopproductsWidget
	add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartRecentproductsWidget");')); // Register the widget: wpStoreCartRecentproductsWidget
        add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartCategoryWidget");')); // Register the widget: wpStoreCartCategoryWidget
        add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartPaymentsWidget");')); // Register the widget: wpStoreCartCategoryWidget


?>