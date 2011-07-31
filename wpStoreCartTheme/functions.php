<?php
global $wpstorecart_is_active, $wpscThemeOptions, $themename, $shortname, $wpdb, $content_width, $wpStoreCart;

$themename = "wpStoreCartTheme";
$shortname = "wpsct";
$version = "1.212";



if(isset($wpStoreCart)) {
    require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
    require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
    require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
    if(!isset($_SESSION)) {
            @session_start();

    }
}

// Theme support stuff here
show_admin_bar(false); // This theme does not support the Wordpress 3.1 admin bar
add_theme_support( 'post-thumbnails' ); // wpStoreCartTheme supports post thumbnails
add_theme_support('automatic-feed-links');
add_editor_style('editor.css');
$content_width = 680;

/**
 * Registers the custom menu
 */
function register_my_menus() {
  register_nav_menus(
    array('header-menu' => __( 'Header Menu' ) )
  );
}

// Directory for the slider image uploads.
if(!is_dir(WP_CONTENT_DIR . '/uploads/')) {
	@mkdir(WP_CONTENT_DIR . '/uploads/', 0777, true);
}
if(!is_dir(WP_CONTENT_DIR . '/uploads/wpstorecart/')) {
	@mkdir(WP_CONTENT_DIR . '/uploads/wpstorecart/', 0777, true);
}

// Let's make sure the meta table exists, if not, let's create it.  Needed for the slider
$table_name = $wpdb->prefix . "wpstorecart_meta";
if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$sql = "
	CREATE TABLE {$table_name} (
	`primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`value` TEXT NOT NULL,
	`type` VARCHAR(32) NOT NULL,
	`foreignkey` INT NOT NULL
	);
	";
	dbDelta($sql);
}

function wpscThemeAdminOptions() {
	global $wpscThemeOptions;
	$wpscThemeOptions = array('display_home_link' => 'true',
							'use_product_preview' => 'true',
							'use_slider' => 'Homepage Only',
							'slider_effect' => 'random',
							'slider_effect_speed' => '500',
							'turnon_wpstorecart' => 'false',
							'slider_pause_time' => '3000',
							'font' => 'Raleway_250.font.js',
							'text_view_cart' => 'View Cart',
							'text_check_out' => 'Check Out',
							'text_you_have' => 'You have',
							'text_items_with_total_value_of' => 'items with total value of',
							'text_username' => 'Username',
							'text_password' => 'Password',
							'text_welcome_back' => 'Welcome back',
							'text_my_orders' => 'My Orders',
							'text_logout' => 'Logout'
							);

	$devOptions = get_option('wpStoreCartThemeOptions');
	if (!empty($devOptions)) {
		foreach ($devOptions as $key => $option) {
			$wpscThemeOptions[$key] = $option;
		}
	}            
	update_option('wpStoreCartThemeOptions', $wpscThemeOptions);
	return $wpscThemeOptions;
}

$wpscThemeOptions = wpscThemeAdminOptions();

// Register the widget ready sidebar
if ( function_exists('register_sidebar') )
register_sidebar(array(
	'name' => 'Right Sidebar',
	'before_widget' => '',
	'after_widget' => '</div><div class="bottom_line"></div>',
	'before_title' => '<div class="title">',
	'after_title' => '</div><div class="content">',
));

// Testing to see if wpStoreCart is installed
if (file_exists(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart.php') && isset($wpStoreCart)) {
	$wpstorecart_is_active = true; //plugin is installed
} else {
	$wpstorecart_is_active = false;
}

function wpsct_init() {
	wp_enqueue_script( 'jquery' );
        if (!is_admin()) {
                    global $wpscThemeOptions, $wpStoreCart;
                    wp_enqueue_style( 'wpsctstylesheet',  get_bloginfo('stylesheet_url'));
                    wp_enqueue_style( 'wpsctnivo',  get_stylesheet_directory_uri(). '/nivo-slider.css');
                    wp_enqueue_script('cufonyui', get_template_directory_uri() . '/js/cufon-yui.js');
                    wp_enqueue_script('thefont', get_template_directory_uri() . '/js/'.$wpscThemeOptions['font']);
                    wp_enqueue_script('jquerynivosp', get_template_directory_uri() . '/js/jquery.nivo.slider.pack.js');
                    if($wpscThemeOptions['use_product_preview']=='true' && isset($wpStoreCart)) {
                            wp_enqueue_script('ezpz_tooltip',WP_PLUGIN_URL . '/wpstorecart/js/jquery.ezpz_tooltip.js',array('jquery'),'1.4' );
                    }
        }
} 



function update_small_cart_callback() {
	global $wpStoreCart, $cart, $wpscThemeOptions;

        if(@isset($wpStoreCart)) {
            require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
            require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
            require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
            if(!isset($_SESSION)) {
                            @session_start();

            }
        }
	if(isset($wpStoreCart)) {
		$devOptions = $wpStoreCart->getAdminOptions();
	} else {
		exit();
	}

	echo $wpscThemeOptions['text_you_have']. ' <b>'. $cart->itemcount .'</b> '.$wpscThemeOptions['text_items_with_total_value_of'].' <b>'. $devOptions['currency_symbol'] .number_format($cart->total, 2) .$devOptions['currency_symbol_right'] .'</b>';
	exit();
}

if ( ! function_exists( 'twentyten_comment' ) ) :

function twentyten_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'wpsc-default' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'wpsc-default' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'wpsc-default' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'wpsc-default' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'wpsc-default' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'wpsc-default' ), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;


function wpsct_create_slider() {
    global $wpdb;
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
}


function wpsct_display_frontpage() {
    global $wpstorecart_is_active, $wpscThemeOptions, $wpdb, $wpStoreCart;

    if(@isset($wpStoreCart)) {
        require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
        require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
        require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
        if(!isset($_SESSION)) {
                @session_start();
        }
    }
    if(isset($wpStoreCart)) {
            $devOptions = $wpStoreCart->getAdminOptions();
    } else {
            //exit();
    }

    if(is_page($devOptions['mainpage']) && !isset($_GET['wpsc'])) {
            global $wpdb, $cart, $wpsc, $wpscThemeOptions;

            $table_name = $wpdb->prefix . "wpstorecart_products";

            $quantity = $devOptions['itemsperpage'];

            if( !isset( $_GET['storepage'] ) || !is_numeric($_GET['storepage'])) {
                    $startat = 0;
            } else {
                    $startat = ($_GET['storepage'] - 1) * $quantity;
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
            $page = isset( $_GET['storepage'] ) ? abs( (int) $_GET['storepage'] ) : 1;

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
                                                    if(@!isset($usetext)) {
                                                       $usetext='true';
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
                                            echo '<input type="image" src="'.get_template_directory_uri(). '/img/AddToCart.jpg" style="margin-left:12px;width:67px;height:25px;" id="my-add-button-fake" name="my-add-button-fake" value="" />';
                                        }

                                    }

                                                                    } else {
                                                                            echo $devOptions['out_of_stock'];
                                                                    }

                                                                    echo '<a href="'.$permalink.'"><img src="'.get_template_directory_uri(). '/img/ViewInfo.jpg" style="margin-left:10px;" /></a></div>';



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
                    'base' => add_query_arg( 'storepage', '%#%' ),
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

function wpsct_create_menu() {
        $wpsctadminpage2 = add_theme_page('Theme Settings - wpStoreCart ', 'Theme Settings', 'edit_theme_options', 'wpstorecarttheme-settings', 'wpsct_settings_page');
	//$wpsctadminpage2 = add_submenu_page('themes.php','Theme Settings - wpStoreCart ', 'Theme Settings', 'edit_theme_options', 'wpstorecarttheme-settings', 'wpsct_settings_page');
	add_action("admin_print_scripts-$wpsctadminpage2", 'wpsct_admin_scripts');
	add_action( 'admin_init', 'wpscThemeAdminOptions' );
}

function wpsct_admin_scripts() {
	global $APjavascriptQueue, $wpStoreCart;

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('swfupload');
	wp_enqueue_script('jquerydd', get_template_directory_uri() . '/js/jquery.dd.js');
	echo '
	<style type="text/css">
		#upload-progressbar-container {
			min-width:200px;
			max-width:200px;
			min-height:20px;
			max-height:20px;
			background-color:#FFF;
			display:block;
		}
		#upload-progressbar {
			min-height:20px;
			max-height:20px;
			background-color:#6ba6ff;
			width:0px;
			display:none;
			border:1px solid #1156be;
		}
		
/************** Skin 1 *********************/
.dd {
	/*display:inline-block !important;*/
	text-align:left;
	background-color:#fff;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	float:left;
}
.dd .ddTitle {
	background:#f2f2f2;
	border:1px solid #c3c3c3;
	padding:3px;
	text-indent:0;
	cursor:default;
	overflow:hidden;
	height:16px;
	width:135px;
}
.dd .ddTitle span.arrow {
	background:url("'.get_template_directory_uri() . '/img/dd_arrow.gif") no-repeat 0 0; float:right; display:inline-block;width:16px; height:16px; cursor:pointer;
}

.dd .ddTitle span.ddTitleText {text-indent:1px; overflow:hidden; line-height:16px;}
.dd .ddTitle span.ddTitleText img{text-align:left; padding:0 2px 0 0}
.dd .ddTitle img.selected {
	padding:0 3px 0 0;
	vertical-align:top;

}
.dd .ddChild {
	position:absolute;
	border:1px solid #c3c3c3;
	border-top:none;
	display:none;
	margin:0;
	width:auto;
	overflow:auto;
	overflow-x:hidden !important;
	background-color:#ffffff;

}
.dd .ddChild .opta a, .dd .ddChild .opta a:visited {padding-left:10px}
.dd .ddChild a {
	display:block;
	padding:2px 0 2px 3px;
	text-decoration:none;
	color:#000;
	overflow:hidden;
	white-space:nowrap;
	cursor:pointer;
}
.dd .ddChild a:hover {
	background-color:#66CCFF;
}
.dd .ddChild a img {
	border:0;
	padding:0 2px 0 0;
	vertical-align:middle;
}
.dd .ddChild a.selected {
	background-color:#66CCFF;

}
.hidden {display:none;}

.dd .borderTop{width:120px;border-top:1px solid #c3c3c3 !important;}
.dd .noBorderTop{width:120px;border-top:none 0  !important}

		
	</style>
	
	
			<script type="text/javascript">
			//<![CDATA[

			
			
			var productUploadStartEventHandler = function (file) { 
				var continue_with_upload; 
				continue_with_upload = true; 
				return continue_with_upload; 
			}; 

			var productUploadSuccessEventHandler = function (file, server_data, receivedResponse) {
				document.wpstorecartaddproductform.wpStoreCartproduct_download.value = document.wpstorecartaddproductform.wpStoreCartproduct_download.value + file.name + "<<<||";
                this.startUpload();
			}; 	
			
			function uploadError(file, errorCode, message) {
				try {

					switch (errorCode) {
					case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
						alert("Error Code: HTTP Error, File name. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:
						alert("Error Code: No backend file. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
						alert("Error Code: Upload Failed. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.IO_ERROR:
						alert("Error Code: IO Error. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
						alert("Error Code: Security Error. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
						alert("Error Code: Upload Limit Exceeded. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND:
						alert("Error Code: The file was not found. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
						alert("Error Code: File Validation Failed. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
						break;
					case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
						break;
					default:
						alert("Error Code: " + errorCode + ". Message: " + message);
						break;
					}
				} catch (ex) {
					this.debug(ex);
				}
			}

			function uploadProgress(file, bytesLoaded, bytesTotal) {
				try {
					var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
					jQuery("#upload-progressbar").css("display", "block");
					jQuery("#upload-progressbar").css("width", percent+"%");
					jQuery("#upload-progressbar").html("<center>"+ percent+"%</center>");
				} catch (e) {
				}
			}			

			function beginTheUpload(selected, addtoqueue, inqueuealready) {
				this.startUpload();
			}
			
			function debugSWFUpload (message) {
				try {
					if (window.console && typeof(window.console.error) === "function" && typeof(window.console.log) === "function") {
						if (typeof(message) === "object" && typeof(message.name) === "string" && typeof(message.message) === "string") {
							window.console.error(message);
						} else {
							window.console.log(message);
						}
					}
				} catch (ex) {
				}
				try {
					if (this.settings.debug) {
						this.debugMessage(message);
					}
				} catch (ex1) {
				}
			}
			
			var swfu; 
			window.onload = function () { 
				var settings_object = { 
					upload_url : "'.WP_PLUGIN_URL.'/wpstorecart/php/upload.php", 
					post_params: {"PHPSESSID" : "'.session_id().'"},
					flash_url : "'.site_url().'/wp-includes/js/swfupload/swfupload.swf",
					file_size_limit : "2048 MB",
					file_types : "*.*",
					file_types_description : "Any file type",
					file_upload_limit : "0",
					file_post_name: "Filedata",					
					button_placeholder_id : "spanSWFUploadButton",
					button_image_url : "'.WP_PLUGIN_URL.'/wpstorecart/images/XPButtonUploadText_61x22.png",
					button_width: 61,
					button_height: 22,
					debug : false, 
					debug_handler : debugSWFUpload,
					file_dialog_complete_handler: beginTheUpload,
                                        upload_progress_handler: uploadProgress,
					upload_start_handler : productUploadStartEventHandler, 
					upload_success_handler : productUploadSuccessEventHandler,
					upload_error_handler : uploadError
				}; 
				
				swfu = new SWFUpload(settings_object); 

			};


			//]]>
			</script>			
			
	';

        if(isset($wpStoreCart)) {
            wp_enqueue_script('toolbox_expose',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/toolbox.expose.min.js',array('jquery'),'1.4' );
            wp_enqueue_script('jqt_overlay',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/overlay.min.js',array('jquery'),'1.4' );
            wp_enqueue_script('jqt_overlay_apple',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/overlay.apple.min.js',array('jquery'),'1.4' );
            $wpStoreCart->overlay_css();
        }
	echo $APjavascriptQueue;	
}

function wpsct_settings_page() {
	global $wpscThemeOptions, $wpStoreCart, $wpdb;

        if(!current_user_can('edit_theme_options')) {
            exit();
        }



	if(isset($wpStoreCart)) {
		$devOptions = $wpStoreCart->getAdminOptions();
	} else {
                echo '<br /><br /><div class="updated fade"><ul><li>This Theme requires the free and open source wpStoreCart <a href="http://wpstorecart.com" title="Wordpress eCommerce Plugin">Wordpress eCommerce Plugin</a> for full functionality.</li></ul></div>';
	}	
	

	
	$table_name = $wpdb->prefix . "wpstorecart_meta";
	$results = $wpdb->get_results("SELECT * FROM {$table_name} WHERE `type`='wpsct_slideshow';", ARRAY_A);	

	if(isset($_POST['display_home_link'])) {
                $nonce=$_REQUEST['_wpnonce'];
                if (! wp_verify_nonce($nonce, 'wpsct-nonce-field') ) die('Security check');
		$wpscThemeOptions['display_home_link'] = $wpdb->escape($_POST['display_home_link']);
		$wpscThemeOptions['font'] = $wpdb->escape($_POST['font']);
		$wpscThemeOptions['use_product_preview'] = $wpdb->escape($_POST['use_product_preview']);
		$wpscThemeOptions['use_slider'] = $wpdb->escape($_POST['use_slider']);
		$wpscThemeOptions['slider_effect'] = $wpdb->escape($_POST['slider_effect']);
		$wpscThemeOptions['slider_effect_speed'] = $wpdb->escape($_POST['slider_effect_speed']);
                if(isset($_POST['turnon_wpstorecart'])){
                    $wpscThemeOptions['turnon_wpstorecart'] = $wpdb->escape($_POST['turnon_wpstorecart']);
                }
		$wpscThemeOptions['slider_pause_time'] = $wpdb->escape($_POST['slider_pause_time']);
		$wpscThemeOptions['text_view_cart'] = $wpdb->escape($_POST['text_view_cart']);
		$wpscThemeOptions['text_check_out'] = $wpdb->escape($_POST['text_check_out']);
		$wpscThemeOptions['text_you_have'] = $wpdb->escape($_POST['text_you_have']);
		$wpscThemeOptions['text_items_with_total_value_of'] = $wpdb->escape($_POST['text_items_with_total_value_of']);
		$wpscThemeOptions['text_username'] = $wpdb->escape($_POST['text_username']);
		$wpscThemeOptions['text_password'] = $wpdb->escape($_POST['text_password']);
		$wpscThemeOptions['text_welcome_back'] = $wpdb->escape($_POST['text_welcome_back']);
		$wpscThemeOptions['text_my_orders'] = $wpdb->escape($_POST['text_my_orders']);
		$wpscThemeOptions['text_logout'] = $wpdb->escape($_POST['text_logout']);

		$icounter = 1;
		$ucounter = 0;
		$finalSlideShowCode ='';
		while($icounter <= 50) {
			if(@isset($_POST['theimagefor_'.$ucounter])) {
				if($_POST['theimagefor_'.$ucounter]!='' || $_POST['theimagefor_'.$ucounter]!=NULL) {
					$finalSlideShowCode .= $_POST['theimagefor_'.$ucounter].'<<<'.$_POST['thelinkfor_'.$ucounter].'||';
				}
				$icounter++;
				$ucounter++;
			} else {
				$finalSlideShowCode .= $_POST['wpStoreCartproduct_download'];
				$icounter = 51; // This breaks us out of the loop if needed, after adding any new images to the database
			}
		}
		
		if($results==false) {
			$insert = "INSERT INTO `{$table_name}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".$finalSlideShowCode."', 'wpsct_slideshow', '0');";	
		} else {
			$insert = "UPDATE  `{$table_name}` SET `value` = '".$finalSlideShowCode."' WHERE `type`='wpsct_slideshow';";
		}

		$newresults = $wpdb->query( $insert );			
		$results = $wpdb->get_results("SELECT * FROM {$table_name} WHERE `type`='wpsct_slideshow';", ARRAY_A);	
		
		update_option('wpStoreCartThemeOptions', $wpscThemeOptions);
	}
	

	if(isset($wpStoreCart)) {
            $wpStoreCart->spHeader();
            $wpStoreCart->spSettings();

            echo'
            <h2> </h2>
            <ul class="tabs">
                    <li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab1" onclick="window.location = \'admin.php?page=wpstorecart-settings&theCurrentTab=tab1\';"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_general.jpg" /></a></li>
                    <li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab2" onclick="window.location = \'admin.php?page=wpstorecart-settings&theCurrentTab=tab2\';"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_email.jpg" /></a></li>';

            echo '<li><a href="admin.php?page=wpstorecarttheme-settings" onclick="window.location = \'admin.php?page=wpstorecarttheme-settings\';"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_theme.jpg" /></a></li>';


            echo '<li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab3" onclick="window.location = \'admin.php?page=wpstorecart-settings&theCurrentTab=tab3\';"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_product.jpg" /></a></li>';

            if($devOptions['storetype']!='Digital Goods Only') { // Hide shipping if digital only store
                    echo '<li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab6" onclick="window.location = \'admin.php?page=wpstorecart-settings&theCurrentTab=tab6\';"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_shipping.jpg" /></a></li>';
            }
            echo '<li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab4" onclick="window.location = \'admin.php?page=wpstorecart-settings&theCurrentTab=tab4\';"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_payment.jpg" /></a></li>
                    <li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab5" onclick="window.location = \'admin.php?page=wpstorecart-settings&theCurrentTab=tab5\';"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_text.jpg" /></a></li>
                    <li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab7" onclick="window.location = \'admin.php?page=wpstorecart-settings&theCurrentTab=tab7\';"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_customers.jpg" /></a></li>
            </ul>
            <div style="clear:both;"></div>';
            echo '<h2>wpStoreCart Theme Options <a href="http://wpstorecart.com/documentation/settings/theme-settings/" target="_blank"><img src="'.plugins_url('/wpstorecart/images/bighelp.png').'" alt="" /></a></h2>';
        } else {
            echo '<h2>wpStoreCart Theme Options</h2>';
        }
	
	
	echo '<form method="post" action="#" name="wpstorecartaddproductform">';

        // Nonces done here:
        if ( function_exists('wp_nonce_field') ) {
            wp_nonce_field('wpsct-nonce-field');

        }
        
	echo '<table class="widefat">
	<thead><tr><th>Option</th><th>Value</th></tr></thead><tbody>
	';
	echo '<tr><td>Display home link: </td><td><select name="display_home_link">
	<option value="true"';if($wpscThemeOptions['display_home_link']==='true'){echo ' SELECTED';}echo '>true</option>
	<option value="false"';if($wpscThemeOptions['display_home_link']==='false'){echo ' SELECTED';}echo '>false</option>	
	</select>
	</td></tr>'; 
	
	echo '<tr><td>Logo Font (h6): </td><td><select name="font" id="font" style="width:135px;">
		<option style="width:135px;" value="Raleway_250.font.js"';selected($wpscThemeOptions['font'],'Raleway_250.font.js') ;echo ' title="'.get_template_directory_uri().'/img/fonts/raleway.png"> Raleway</option>
		<option style="width:135px;" value="ChunkFive_400.font.js"';selected($wpscThemeOptions['font'],'ChunkFive_400.font.js');echo ' title="'.get_template_directory_uri().'/img/fonts/chunkfive.png"> ChunkFive</option>
		<option style="width:135px;" value="Fanwood_400.font.js"';selected($wpscThemeOptions['font'],'Fanwood_400.font.js');echo ' title="'.get_template_directory_uri().'/img/fonts/fanwood.png"> Fanwood</option>
		<option style="width:135px;" value="Goudy_Bookletter_1911_400.font.js"';selected($wpscThemeOptions['font'],'Goudy_Bookletter_1911_400.font.js');echo ' title="'.get_template_directory_uri().'/img/fonts/goudy.png"> Goudy</option>
		<option style="width:135px;" value="Junction_400.font.js"';selected($wpscThemeOptions['font'],'Junction_400.font.js');echo ' title="'.get_template_directory_uri().'/img/fonts/junction.png"> Junction</option>
		<option style="width:135px;" value="League_Gothic_400.font.js"';selected($wpscThemeOptions['font'],'League_Gothic_400.font.js');echo ' title="'.get_template_directory_uri().'/img/fonts/leaguegothic.png"> League Gothic</option>
	</select>
	<script type="text/javascript">
	//<![CDATA[

	jQuery(document).ready(function($) { 
		$("#font").msDropDown();
	});	
	
	//]]>
	</script>
	</td></tr>'; 	

	echo '<tr><td>Display product information on hover: </td><td><select name="use_product_preview">
	<option value="true"';if($wpscThemeOptions['use_product_preview']==='true'){echo ' SELECTED';}echo '>true</option>
	<option value="false"';if($wpscThemeOptions['use_product_preview']==='false'){echo ' SELECTED';}echo '>false</option>	
	</select>
	</td></tr>'; 

	echo '</tbody></table>';

	echo '<h2>Slide Show Images &amp; Settings</h2>';
	echo '<table class="widefat">
	<thead><tr><th>Option</th><th>Value</th></tr></thead><tbody>
	';	
	
	echo '<tr><td>Enable the image slider: </td><td><select name="use_slider">
	<option value="Homepage Only"';if($wpscThemeOptions['use_slider']=='Homepage Only'){echo ' SELECTED';}echo '>Homepage Only</option>
	<option value="All Pages"';if($wpscThemeOptions['use_slider']=='All Pages'){echo ' SELECTED';}echo '>Display on All Pages</option>
	<option value="Disable"';if($wpscThemeOptions['use_slider']=='Disable'){echo ' SELECTED';}echo '>Disabled</option>
	</select></td></tr>'; 	
	
	echo '<tr><td>Slider effect: </td><td><select name="slider_effect">
	<option value="sliceDown"';if($wpscThemeOptions['slider_effect']=='sliceDown'){echo ' SELECTED';}echo '>sliceDown</option>
	<option value="sliceDownLeft"';if($wpscThemeOptions['slider_effect']=='sliceDownLeft'){echo ' SELECTED';}echo '>sliceDownLeft</option>
	<option value="sliceUp"';if($wpscThemeOptions['slider_effect']=='sliceUp'){echo ' SELECTED';}echo '>sliceUp</option>
	<option value="sliceUpLeft"';if($wpscThemeOptions['slider_effect']=='sliceUpLeft'){echo ' SELECTED';}echo '>sliceUpLeft</option>
	<option value="sliceUpDown"';if($wpscThemeOptions['slider_effect']=='sliceUpDown'){echo ' SELECTED';}echo '>sliceUpDown</option>
	<option value="sliceUpDownLeft"';if($wpscThemeOptions['slider_effect']=='sliceUpDownLeft'){echo ' SELECTED';}echo '>sliceUpDownLeft</option>
	<option value="fold"';if($wpscThemeOptions['slider_effect']=='fold'){echo ' SELECTED';}echo '>fold</option>
	<option value="fade"';if($wpscThemeOptions['slider_effect']=='fade'){echo ' SELECTED';}echo '>fade</option>
	<option value="random"';if($wpscThemeOptions['slider_effect']=='random'){echo ' SELECTED';}echo '>random</option>
	<option value="slideInRight"';if($wpscThemeOptions['slider_effect']=='sliceInRight'){echo ' SELECTED';}echo '>slideInRight</option>
	<option value="slideInLeft"';if($wpscThemeOptions['slider_effect']=='sliceInLeft'){echo ' SELECTED';}echo '>slideInLeft</option>
	</select></td></tr>'; 
	
	echo '<tr><td>Speed of slider: </td><td><input type="text" value="'.esc_attr($wpscThemeOptions['slider_effect_speed']).'" name="slider_effect_speed" /></td></tr>';
	echo '<tr><td>Time between slides: </td><td><input type="text" value="'.esc_attr($wpscThemeOptions['slider_pause_time']).'" name="slider_pause_time" /></td></tr>';
	echo '<tr><td>Images to use in the slideshow:</td><td><input type="hidden" name="wpStoreCartproduct_download" id="wpStoreCartproduct_download" style="width: 200px;" value="" /><br />
				Upload a file: <span id="spanSWFUploadButton"></span>
							<div id="upload-progressbar-container">
								<div id="upload-progressbar">
								</div>
							</div>	
	</td></tr>
		';	
	echo '</tbody></table>';
	echo '<h2>Links for slideshow images</h2>';

        if(@!isset($results[0])) {
            $results[0]['value'] = NULL;
        }

	echo '<table class="widefat">
	<thead><tr><th>Link URL</th><th>Image</th></tr></thead><tbody id="linksforimages">
		  </tbody></table>
	<input type="hidden" value="0" id="numberofslideshowimages" name="numberofslideshowimages" />
	<input type="hidden" value="'.$results[0]['value'].'" id="wpStoreCartproduct_download_old" name="wpStoreCartproduct_download_old" />
	<script type="text/javascript">
	//<![CDATA[

	jQuery(document).ready(function($) { 

		var theSlideShowInfo = $("#wpStoreCartproduct_download_old").val();
		var brokenstring = theSlideShowInfo.split("||");
		var theContentForOutput = "";
		var numberofslideshowimages = 0;
		for(var i in brokenstring) {
			if(brokenstring[i]!="") {
				var newbrokenstring = brokenstring[i].split("<<<");
				if (newbrokenstring[1]==undefined) {
					newbrokenstring[0] = brokenstring[i];
					newbrokenstring[1] = "";
				}
				theContentForOutput = theContentForOutput + \'<tr id="slideshowimagetr_\'+[i]+\'"><td><img src="'.plugins_url('/wpstorecart/images/cross.png').'" alt="delete" style="cursor:pointer;" onclick="jQuery(\\\'#slideshowimagetr_\'+[i]+\'\\\').hide(\\\'explode\\\', 1000);jQuery(\\\'#thelinkfor_\'+[i]+\'\\\').val(\\\'\\\');jQuery(\\\'#theimagefor_\'+[i]+\'\\\').val(\\\'\\\');" /> <input type="text" value="\'+newbrokenstring[1]+\'" name="thelinkfor_\'+i+\'" id="thelinkfor_\'+i+\'" style="width:250px;" /><input type="hidden" value="\'+newbrokenstring[0]+\'" name="theimagefor_\'+i+\'" id="theimagefor_\'+i+\'" /></td><td><img src="'.get_bloginfo('url'). '/wp-content/uploads/wpstorecart/\'+newbrokenstring[0]+\'" alt="" style="height:100px;max-height:100px;" /></td></tr>\';
			}
			$(\'#linksforimages\').replaceWith(\'<tbody id="linksforimages">\'+theContentForOutput+\'</tbody>\');
			numberofslideshowimages = i;
		}
		$("#numberofslideshowimages").val(numberofslideshowimages);
	});	
	
	//]]>
	</script>	

	
	';


	
	echo '<h2>Text Options</h2>';	
	echo '<table>';
	echo '<tr><td>View Cart</td><td><input type="text" value="'.$wpscThemeOptions['text_view_cart'].'" name="text_view_cart" style="width:250px;" /></td></tr>'; 
	echo '<tr><td>Check Out</td><td><input type="text" value="'.$wpscThemeOptions['text_check_out'].'" name="text_check_out" style="width:250px;" /></td></tr>'; 
	echo '<tr><td>You have</td><td><input type="text" value="'.$wpscThemeOptions['text_you_have'].'" name="text_you_have" style="width:250px;" /></td></tr>'; 
	echo '<tr><td>items with total value of</td><td><input type="text" value="'.$wpscThemeOptions['text_items_with_total_value_of'].'" name="text_items_with_total_value_of" style="width:250px;" /></td></tr>'; 
	echo '<tr><td>username</td><td><input type="text" value="'.$wpscThemeOptions['text_username'].'" name="text_username" style="width:250px;" /></td></tr>'; 
	echo '<tr><td>password</td><td><input type="text" value="'.$wpscThemeOptions['text_password'].'" name="text_password" style="width:250px;" /></td></tr>'; 
	echo '<tr><td>Welcome back</td><td><input type="text" value="'.$wpscThemeOptions['text_welcome_back'].'" name="text_welcome_back" style="width:250px;" /></td></tr>'; 
	echo '<tr><td>My Orders </td><td><input type="text" value="'.$wpscThemeOptions['text_my_orders'].'" name="text_my_orders" style="width:250px;" /></td></tr>'; 
	echo '<tr><td>Logout</td><td><input type="text" value="'.$wpscThemeOptions['text_logout'].'" name="text_logout" style="width:250px;" /></td></tr>'; 
	echo '</table>';
	echo '<table>';
	echo '<tr><td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td><td><input type="submit" value="Update" /></form></td>';
	echo '</table>
	';
	
}

add_action('wp_ajax_update_small_cart', 'update_small_cart_callback');
add_action('wp_ajax_nopriv_update_small_cart', 'update_small_cart_callback');
add_action('init', 'wpsct_init');
add_action('init', 'register_my_menus' );
add_action('admin_menu', 'wpsct_create_menu');

?>