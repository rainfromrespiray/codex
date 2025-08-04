<?php
/*
 *  Author: Todd Motto | @toddmotto
 *  URL: html5blank.com | @html5blank
 *  Custom functions, support, custom post types and more.
 */

/*------------------------------------*\
	External Modules/Files
\*------------------------------------*/

// Load any external files you have here

/*------------------------------------*\
	Theme Support
\*------------------------------------*/

if (!isset($content_width))
{
    $content_width = 900;
}

if (function_exists('add_theme_support'))
{
    // Add Menu Support
    add_theme_support('menus');

    // Add Thumbnail Theme Support
    add_theme_support('post-thumbnails');
    add_image_size('large', 700, '', true); // Large Thumbnail
    add_image_size('medium', 250, '', true); // Medium Thumbnail
    add_image_size('small', 120, '', true); // Small Thumbnail
    add_image_size('custom-size', 700, 200, true); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');
    add_image_size('product-thumb', 500, 500, true); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');

    // Add Support for Custom Backgrounds - Uncomment below if you're going to use
    /*add_theme_support('custom-background', array(
	'default-color' => 'FFF',
	'default-image' => get_template_directory_uri() . '/img/bg.jpg'
    ));*/

    // Add Support for Custom Header - Uncomment below if you're going to use
    /*add_theme_support('custom-header', array(
	'default-image'			=> get_template_directory_uri() . '/img/headers/default.jpg',
	'header-text'			=> false,
	'default-text-color'		=> '000',
	'width'				=> 1000,
	'height'			=> 198,
	'random-default'		=> false,
	'wp-head-callback'		=> $wphead_cb,
	'admin-head-callback'		=> $adminhead_cb,
	'admin-preview-callback'	=> $adminpreview_cb
    ));*/

    // Enables post and comment RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Localisation Support
    add_action( 'after_setup_theme', function() {
    load_theme_textdomain( 'html5blank', get_template_directory() . '/languages' );
});

}

/*------------------------------------*\
	Functions
\*------------------------------------*/

// HTML5 Blank navigation
function html5blank_nav()
{
	wp_nav_menu(
	array(
		'theme_location'  => 'header-menu',
		'menu'            => '',
		'container'       => 'div',
		'container_class' => 'menu-{menu slug}-container',
		'container_id'    => '',
		'menu_class'      => 'menu',
		'menu_id'         => '',
		'echo'            => true,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'items_wrap'      => '<ul>%3$s</ul>',
		'depth'           => 0,
		'walker'          => ''
		)
	);
}

// HTML5 Blank navigation
function html5blank_nav_new($location)
{
    wp_nav_menu(
        array(
            'theme_location'  => $location,
            'menu'            => '',
            'container'       => 'div',
            'container_class' => 'menu-{menu slug}-container',
            'container_id'    => '',
            'menu_class'      => 'menu',
            'menu_id'         => '',
            'echo'            => true,
            'fallback_cb'     => 'wp_page_menu',
            'before'          => '',
            'after'           => '',
            'link_before'     => '',
            'link_after'      => '',
            'items_wrap'      => '<ul>%3$s</ul>',
            'depth'           => 0,
            'walker'          => ''
        )
    );
}


// Load HTML5 Blank scripts (header.php)
function html5blank_header_scripts()
{
    if ( $GLOBALS['pagenow'] != 'wp-login.php' && ! is_admin() ) {
        // Determine if we are on the custom checkout
        $is_checkout_nf = false !== strpos( $_SERVER['REQUEST_URI'], '/checkouts/nf/' );

        // Only load these libraries when not on the custom checkout.  They are
        // required for sliders and feature detection on most pages, but
        // contribute unnecessary weight on the checkout page.
        if ( ! $is_checkout_nf ) {
            wp_register_script( 'conditionizr', get_template_directory_uri() . '/js/lib/conditionizr-4.3.0.min.js', array(), '4.3.0' ); // Conditionizr
            wp_enqueue_script( 'conditionizr' ); // Enqueue it!

            wp_register_script( 'modernizr', get_template_directory_uri() . '/js/lib/modernizr-2.7.1.min.js', array(), '2.7.1' ); // Modernizr
            wp_enqueue_script( 'modernizr' ); // Enqueue it!

            wp_register_script( 'swiper', 'https://unpkg.com/swiper/swiper-bundle.min.js', array( 'jquery' ), '1.0.91' ); // Swiper
            wp_enqueue_script( 'swiper' ); // Enqueue it!
        }

        // html5blankscripts should always load
        wp_register_script( 'html5blankscripts', get_template_directory_uri() . '/js/scripts.js', array( 'jquery' ), '2.93' ); // Custom scripts
        wp_enqueue_script( 'html5blankscripts' ); // Enqueue it!
    }
}

// Load HTML5 Blank conditional scripts
function html5blank_conditional_scripts()
{
    if (is_page('pagenamehere')) {
        wp_register_script('scriptname', get_template_directory_uri() . '/js/scriptname.js', array('jquery'), '1.0.0'); // Conditional script(s)
        wp_enqueue_script('scriptname'); // Enqueue it!
    }
}

// Load HTML5 Blank styles
function html5blank_styles()
{
    wp_register_style('normalize', get_template_directory_uri() . '/normalize.css', array(), '1.0', 'all');
    wp_enqueue_style('normalize'); // Enqueue it!

    wp_register_style('html5blank', get_template_directory_uri() . '/style.css', array(), '1.21', 'all');
    wp_enqueue_style('html5blank'); // Enqueue it!

    wp_register_style('newcvi', get_template_directory_uri() . '/css/new.css', array(), '1.4611', 'all');
    wp_enqueue_style('newcvi'); // Enqueue it!

    // Only enqueue Swiper CSS when not on the custom checkout page.  Sliders are
    // not present on `/checkouts/nf/` so we save a network request by
    // conditionally excluding this stylesheet.
    $is_checkout_nf_styles = false !== strpos( $_SERVER['REQUEST_URI'], '/checkouts/nf/' );
    if ( ! $is_checkout_nf_styles ) {
        wp_register_style( 'swipercss', 'https://unpkg.com/swiper/swiper-bundle.min.css', array(), '1.198', 'all' );
        wp_enqueue_style( 'swipercss' ); // Enqueue it!
    }
}

// Register HTML5 Blank Navigation
function register_html5_menu()
{
    register_nav_menus(array( // Using array to specify more menus if needed
        'header-menu' => __('Header Menu', 'html5blank'), // Main Navigation
        'sidebar-menu' => __('Sidebar Menu', 'html5blank'), // Sidebar Navigation
        'extra-menu' => __('Extra Menu', 'html5blank') // Extra Navigation if needed (duplicate as many as you need!)
    ));
}

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}

// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function my_css_attributes_filter($var)
{
    return is_array($var) ? array() : '';
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list($thelist)
{
    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class($classes)
{
    global $post;
    if (is_home()) {
        $key = array_search('blog', $classes);
        if ($key > -1) {
            unset($classes[$key]);
        }
    } elseif (is_page()) {
        $classes[] = sanitize_html_class($post->post_name);
    } elseif (is_singular()) {
        $classes[] = sanitize_html_class($post->post_name);
    }

    return $classes;
}

// If Dynamic Sidebar Exists
if (function_exists('register_sidebar'))
{
    // Define Sidebar Widget Area 1
    register_sidebar(array(
        'name' => __('Widget Area 1', 'html5blank'),
        'description' => __('Description for this widget-area...', 'html5blank'),
        'id' => 'widget-area-1',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));

    // Define Sidebar Widget Area 2
    register_sidebar(array(
        'name' => __('Widget Area 2', 'html5blank'),
        'description' => __('Description for this widget-area...', 'html5blank'),
        'id' => 'widget-area-2',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
}

// Remove wp_head() injected Recent Comment styles
function my_remove_recent_comments_style()
{
    global $wp_widget_factory;
    remove_action('wp_head', array(
        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
        'recent_comments_style'
    ));
}

// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function html5wp_pagination()
{
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}

// Custom Excerpts
function html5wp_index($length) // Create 20 Word Callback for Index page Excerpts, call using html5wp_excerpt('html5wp_index');
{
    return 20;
}

// Create 40 Word Callback for Custom Post Excerpts, call using html5wp_excerpt('html5wp_custom_post');
function html5wp_custom_post($length)
{
    return 40;
}

// Create the Custom Excerpts callback
function html5wp_excerpt($length_callback = '', $more_callback = '')
{
    global $post;
    if (function_exists($length_callback)) {
        add_filter('excerpt_length', $length_callback);
    }
    if (function_exists($more_callback)) {
        add_filter('excerpt_more', $more_callback);
    }
    $output = get_the_excerpt();
    $output = apply_filters('wptexturize', $output);
    $output = apply_filters('convert_chars', $output);
    $output = '<p>' . $output . '</p>';
    echo $output;
}

// Custom View Article link to Post
function html5_blank_view_article($more)
{
    global $post;
    return '... <a class="view-article" href="' . get_permalink($post->ID) . '">' . __('View Article', 'html5blank') . '</a>';
}

// Remove Admin bar
function remove_admin_bar()
{
    return false;
}

// Remove 'text/css' from our enqueued stylesheet
function html5_style_remove($tag)
{
    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}

// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function remove_thumbnail_dimensions( $html )
{
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}

// Custom Gravatar in Settings > Discussion
function html5blankgravatar ($avatar_defaults)
{
    $myavatar = get_template_directory_uri() . '/img/gravatar.jpg';
    $avatar_defaults[$myavatar] = "Custom Gravatar";
    return $avatar_defaults;
}

// Threaded Comments
function enable_threaded_comments()
{
    if (!is_admin()) {
        if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
            wp_enqueue_script('comment-reply');
        }
    }
}

// Custom Comments Callback
function html5blankcomments($comment, $args, $depth)
{
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);

	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>
    <!-- heads up: starting < for the html tag (li or div) in the next line: -->
    <<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ) : ?>
	<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
	<?php endif; ?>
	<div class="comment-author vcard">
	<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['180'] ); ?>
	<?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
	</div>
<?php if ($comment->comment_approved == '0') : ?>
	<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
	<br />
<?php endif; ?>

	<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
		<?php
			printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','' );
		?>
	</div>

	<?php comment_text() ?>

	<div class="reply">
	<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
	</div>
	<?php if ( 'div' != $args['style'] ) : ?>
	</div>
	<?php endif; ?>
<?php }

/*------------------------------------*\
	Actions + Filters + ShortCodes
\*------------------------------------*/

// Add Actions
add_action('init', 'html5blank_header_scripts'); // Add Custom Scripts to wp_head
add_action('wp_print_scripts', 'html5blank_conditional_scripts'); // Add Conditional Page Scripts
add_action('get_header', 'enable_threaded_comments'); // Enable Threaded Comments
add_action('wp_enqueue_scripts', 'html5blank_styles'); // Add Theme Stylesheet
add_action('init', 'register_html5_menu'); // Add HTML5 Blank Menu
add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()
add_action('init', 'html5wp_pagination'); // Add our HTML5 Pagination
add_action('init', 'create_post_type_logos'); // Add Logos Custom Post Type
add_action('init', 'create_post_type_references'); // Add references Custom Post Type
add_action('init', 'create_post_type_faq'); // Add FAQ Custom Post Type
add_action('init', 'create_post_type_press'); // Add PRESS Custom Post Type
add_action('init', 'create_post_type_contact'); // Add Contact Custom Post Type
add_action('init', 'create_post_type_cases'); // Add Contact Custom Post Type
add_action('init', 'create_post_type_benefits'); // Add Contact Custom Post Type
add_action('init', 'create_post_type_instaposts'); // Add Contact Custom Post Type
add_action('init', 'create_post_type_tiktokvideos'); // Add Contact Custom Post Type
add_action('init', 'create_post_type_ytreviews'); // Add Contact Custom Post Type



/*

add_filter( 'woocommerce_email_recipient_customer_completed_order', 'your_email_recipient_filter_function', 10, 2);

function your_email_recipient_filter_function($recipient, $object) {
    $recipient = $recipient . ', arvedvalja@respiray.com';
    return $recipient;
}
*/
add_filter( 'woocommerce_email_headers', 'bbloomer_order_completed_email_add_cc_bcc', 9999, 3 );

function bbloomer_order_completed_email_add_cc_bcc( $headers, $email_id, $order ) {
    if ( 'customer_completed_order' == $email_id ) {
        $headers .= "Bcc: Respiray <arvedvalja@respiray.com>" . "\r\n"; // del if not needed
        #$headers .= "Bcc: Respiray <raul@roosfeld.com>" . "\r\n"; // del if not needed
    }
    return $headers;
}


function wc_add_order_notes_to_completed_emails($order) {
    #$order_number = $order->get_meta('_order_number');
    $order_number = $order->id;
    #$order_number = $order->get_id(); id was called incorrectly. Order properties should not be accessed directly. fix?

    #$order_number = 9773;
    #var_dump($order->id);
    #var_dump($order);

    $shipping_country = $order->get_shipping_country();

    if (in_array($shipping_country, ['US', 'CA', 'AU'])) {
        return;
    }


    if ($order_number) {

        $username = "WSI_RESPIRAYTEST";
        $password = "j57gc5ADwxQRqD7EgFyg";
        $remote_url = 'https://api.ongoingsystems.se/Boomerang/api/v1/orders?goodsOwnerId=317&orderNumber='.$order_number;

        // Create a stream
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header' => "Authorization: Basic " . base64_encode("$username:$password")
            )
        );

        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        $file = file_get_contents($remote_url, false, $context);
        $file = json_decode($file, true);
        $waybill = $file[0]['orderInfo']['wayBill'];
        $parcelNo = $file[0]['parcels']['parcelNumber'];

        /*
        if ( isset( $waybill ) ) {
            // Add a new row for tracking

            update_field('tracking_number', $waybill, $order->id);

            echo '<h2>' . __( 'Shipping info', 'woocommerce' ) . '</h2>';
            echo 'Track your order here: https://parcelsapp.com/en/tracking/'. $waybill;
            echo '<br><br>';

        }*/

        if ( empty( $waybill ) ) {
            // Add a new row for tracking

            update_field('tracking_number', $parcelNo, $order->id);

            echo '<h2>' . __( 'Shipping info', 'woocommerce' ) . '</h2>';
            echo 'Track your order here: https://parcelsapp.com/en/tracking/'. $parcelNo;
            echo '<br><br>';

        } else {
            // Add a new row for tracking

            update_field('tracking_number', $waybill, $order->id);

            echo '<h2>' . __( 'Shipping info', 'woocommerce' ) . '</h2>';
            echo 'Track your order here: https://parcelsapp.com/en/tracking/'. $waybill;
            echo '<br><br>';
        }
    }
}

#add_action( 'woocommerce_email_order_meta', 'wc_add_order_notes_to_completed_emails');
add_action( 'woocommerce_email_before_order_table', 'wc_add_order_notes_to_completed_emails');




function custom_price_shortcode_callback( $atts ) {

    $atts = shortcode_atts( array(
        'id' => null,
    ), $atts, 'product_price' );

    $html = '';

    if( intval( $atts['id'] ) > 0 && function_exists( 'wc_get_product' ) ){
        // Get an instance of the WC_Product object
        $product = wc_get_product( intval( $atts['id'] ) );

        // Get the product prices
        $price         = wc_get_price_to_display( $product, array( 'price' => $product->get_price() ) ); // Get the active price
        $regular_price = wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ); // Get the regular price
        $sale_price    = wc_get_price_to_display( $product, array( 'price' => $product->get_sale_price() ) ); // Get the sale price

        // Your price CSS styles
        $style1 = 'style="font-size:40px;color:#e79a99;font-weight:bold;"';
        $style2 = 'style="font-size:25px;color:#e79a99"';

        // Formatting price settings (for the wc_price() function)
        $args = array(
            'ex_tax_label'       => false,
            'currency'           => 'EUR',
            'decimal_separator'  => '.',
            'thousand_separator' => ' ',
            'decimals'           => 2,
            'price_format'       => '%2$s&nbsp;%1$s',
        );

        // Formatting html output
        if( ! empty( $sale_price ) && $sale_price != 0 && $sale_price < $regular_price )
            $html = "<del $style2>" . wc_price( $regular_price, $args ) . "</del> <ins $style1>" . wc_price( $sale_price, $args ) . "</ins>"; // Sale price is set
        else
            $html = "<ins $style1>" . wc_price( $price, $args ) . "</ins>"; // No sale price set
    }
    return $html;
}
add_shortcode( 'product_price', 'custom_price_shortcode_callback' );




#add_action( 'woocommerce_checkout_before_customer_details', 'display_shipping_notice' );
function display_shipping_notice() {
    echo '<div class="shipping-notice woocommerce-message" role="alert" style="display:none">Don\'t worry - all import duties & taxes are already included.</div>';
}


#add_action( 'woocommerce_after_cart_table', 'display_delivery_time_notice' );
function display_delivery_time_notice() {
    echo '<div class="shipping-notice woocommerce-message custom_message_shipping" role="alert">Please note that the first deliveries of the Wear A+ start 31 March 2023.</div>';
}

add_action( 'woocommerce_after_checkout_form', 'show_shipping_notice_js' );
function show_shipping_notice_js(){
    ?>
    <script>

        jQuery(function($){
            var countryCode  = 'GB', // Set the country code (That will display the message)
                countryField = 'select#billing_country'; // The Field selector to target

            function showHideShippingNotice( countryCode, countryField ){
                if( $(countryField).val() === countryCode ){
                    $('.shipping-notice').show();
                }
                else {
                    $('.shipping-notice').hide();
                }
            }

            // On Ready (after DOM is loaded)
            showHideShippingNotice( countryCode, countryField );

            // On billing country change (Live event)
            $('form.checkout').on('change', countryField, function() {
                showHideShippingNotice( countryCode, countryField );
            });
        });
    </script>
    <?php
}


















/**
 * Adds 'Invoice date' column header to 'Orders' page immediately after 'Date' column.
 *
 * @param string[] $columns
 * @return string[] $new_columns
 */
function sv_wc_cogs_add_order_invoicedate_column_header( $columns ) {

    $new_columns = array();

    foreach ( $columns as $column_name => $column_info ) {

        $new_columns[ $column_name ] = $column_info;

        if ( 'order_date' === $column_name ) {
            $new_columns['order_invoicedate'] = __( 'Invoice Date', 'Respiray' );
        }
    }

    return $new_columns;
}
add_filter( 'manage_edit-shop_order_columns', 'sv_wc_cogs_add_order_invoicedate_column_header', 20 );


if ( ! function_exists( 'sv_helper_get_order_meta' ) ) :

    /**
     * Helper function to get meta for an order.
     *
     * @param \WC_Order $order the order object
     * @param string $key the meta key
     * @param bool $single whether to get the meta as a single item. Defaults to `true`
     * @param string $context if 'view' then the value will be filtered
     * @return mixed the order property
     */
    function sv_helper_get_order_meta( $order, $key = '', $single = true, $context = 'edit' ) {

        // WooCommerce > 3.0
        if ( defined( 'WC_VERSION' ) && WC_VERSION && version_compare( WC_VERSION, '3.0', '>=' ) ) {

            $value = $order->get_meta( $key, $single, $context );

        } else {

            // have the $order->get_id() check here just in case the WC_VERSION isn't defined correctly
            $order_id = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id;
            $value    = get_post_meta( $order_id, $key, $single );
        }

        return $value;
    }

endif;
/**
 * Adds 'Invoice date' column content to 'Orders' page immediately after 'Date' column.
 *
 * @param string[] $column name of column being displayed
 */
function sv_wc_cogs_add_order_invoicedate_column_content( $column ) {
    global $post;

    if ( 'order_invoicedate' === $column ) {
        $order    = wc_get_order( $post->ID );
        $date = $order->get_date_completed();
        if ($date) {
            echo date_format($date, 'M d, Y');
        } else {
            echo '-';
        }

    }
}
add_action( 'manage_shop_order_posts_custom_column', 'sv_wc_cogs_add_order_invoicedate_column_content' );








/**
 * @snippet       Add Order Note @ Checkout Page - WooCommerce
 * @how-to        Get CustomizeWoo.com FREE
 * @sourcecode    https://businessbloomer.com/?p=358
 * @author        Rodolfo Melogli
 * @compatible    WC 3.5.1
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

add_action( 'woocommerce_after_order_notes', 'bbloomer_notice_shipping' );

function bbloomer_notice_shipping() {
    #echo '<p class="allow">We currently offer <strong>EU shipping only (including UK)</strong>. Other shipping locations will be added soon. </p>';
}







add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );
/**
 * Add 2 custom product data tabs
 */
function woo_new_product_tab( $tabs ) {
	
	// Adds the new tab
	if(get_field('ska_transport'))
	$tabs['ingredient_tab'] = array(
		'title' 	=> __( 'Shipping', 'woocommerce' ),
		'priority' 	=> 25,
		'callback' 	=> 'ska_transport_callback'
        );
        if(get_field('ska_specs'))
        $tabs['work_tab'] = array(
		'title' 	=> __( 'Specifications', 'woocommerce' ),
		'priority' 	=> 15,
		'callback' 	=> 'ska_specs_callback'
	);

	return $tabs;

}


function ska_transport_callback() {
        echo the_field('ska_transport');
}

function ska_specs_callback() {
        echo the_field('ska_specs');
}



// Remove the additional information tab
function woo_remove_product_tabs( $tabs ) {
    unset( $tabs['additional_information'] );
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

add_action('woocommerce_order_status_changed', 'send_custom_email_notifications', 10, 4 );
function send_custom_email_notifications( $order_id, $old_status, $new_status, $order ){
    if ( $new_status == 'cancelled' || $new_status == 'failed' ){
        $wc_emails = WC()->mailer()->get_emails(); // Get all WC_emails objects instances
        $customer_email = $order->get_billing_email(); // The customer email
    }

    if ( $new_status == 'cancelled' ) {
        // change the recipient of this instance
        $wc_emails['WC_Email_Cancelled_Order']->recipient = $customer_email;
        // Sending the email from this instance
        $wc_emails['WC_Email_Cancelled_Order']->trigger( $order_id );
    }
    elseif ( $new_status == 'failed' ) {
        // change the recipient of this instance
        $wc_emails['WC_Email_Failed_Order']->recipient = $customer_email;
        // Sending the email from this instance
        $wc_emails['WC_Email_Failed_Order']->trigger( $order_id );
    }
}



function mailchimp_custom_order_merge_tags($merge_tags, $order) {

    $merge_tags['PHONE'] = $order->getCustomer()->getAddress()->getPhone();
    $merge_tags['CTYPE'] = $order->getCustomer()->getCompany();

    return $merge_tags;
}


add_filter('mailchimp_get_ecommerce_merge_tags', 'mailchimp_custom_order_merge_tags', 10, 2);


add_action( 'woocommerce_after_shop_loop_item_title', 'woo_show_excerpt_shop_page', 5 );
function woo_show_excerpt_shop_page() {
    global $product;
    #var_dump($product->post);
    echo '<p>';
    echo $product->post->post_content;
    echo '</p>';
}




// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // Index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);







// Add Filters
add_filter('avatar_defaults', 'html5blankgravatar'); // Custom Gravatar in Settings > Discussion
add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
// add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected classes (Commented out by default)
// add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected ID (Commented out by default)
// add_filter('page_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> Page ID's (Commented out by default)
add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
add_filter('the_excerpt', 'shortcode_unautop'); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
add_filter('the_excerpt', 'do_shortcode'); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
add_filter('excerpt_more', 'html5_blank_view_article'); // Add 'View Article' button instead of [...] for Excerpts
add_filter('show_admin_bar', 'remove_admin_bar'); // Remove Admin bar
add_filter('style_loader_tag', 'html5_style_remove'); // Remove 'text/css' from enqueued stylesheet
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to thumbnails
add_filter('image_send_to_editor', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to post images
add_filter( 'woocommerce_adjust_non_base_location_prices', '__return_false' ); // fixed price for all countries
add_filter( 'wc_product_has_unique_sku', '__return_false' );

/*
 * (Issue) B2BKing Pages Do Not Load - Infinite Loading Icon
 * https://woocommerce-b2b-plugin.com/docs/issue-b2bking-pages-do-not-load-infinite-loading-icon/
 */
add_filter( 'b2bking_ajax_pages_load', '__return_false');



// Remove Filters
remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether

// Shortcodes
add_shortcode('html5_shortcode_demo', 'html5_shortcode_demo'); // You can place [html5_shortcode_demo] in Pages, Posts now.
add_shortcode('html5_shortcode_demo_2', 'html5_shortcode_demo_2'); // Place [html5_shortcode_demo_2] in Pages, Posts now.

// Shortcodes above would be nested like this -
// [html5_shortcode_demo] [html5_shortcode_demo_2] Here's the page title! [/html5_shortcode_demo_2] [/html5_shortcode_demo]




/**
 * Reorder EU VAT field on checkout page
 * 
 * @param $fields array The original array with the checkout fields
 * @return $fields array The updated array with the checkout fields
 * @see https://woocommerce.com/products/eu-vat-number/
 */
function smntcs_reorder_eu_vat_field( $fields ) {
	$fields['billing']['billing_vat_number']['priority'] = 81;
	$fields['billing']['billing_vat_number']['placeholder'] = __("VAT Number","Respiray");;
	#$fields['billing']['billing_company_size']['default'] = 'Business sizes';
	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'smntcs_reorder_eu_vat_field', 61 );




// Add this to your theme's functions.php
function jh_add_script_to_footer(){
    if( ! is_admin() ) { ?>
        <script>

            jQuery(document).ready(function($){

                function updatePrimeButtonQuantity(newQuantity) {
                    console.log(newQuantity);
                    console.log('siin');

                    const selectElement = document.getElementById('color');
                    const selectedValue = selectElement.value;
                    console.log(selectedValue);
                    if (selectedValue == 'White') {
                        window.bwp.updateWidget("4745010141287", newQuantity); // 4745010141287 white, 4745010141294 black
                    }
                    if (selectedValue == 'Black') {
                        window.bwp.updateWidget("4745010141294", newQuantity); // 4745010141287 white, 4745010141294 black
                    }

                }



                $(document).on('click', '.plus', function(e) { // replace '.quantity' with document (without single quote)
                    $input = $(this).prev('input.qty');
                    var val = parseInt($input.val());
                    var step = $input.attr('step');
                    step = 'undefined' !== typeof(step) ? parseInt(step) : 1;
                    $input.val( val + step ).change();
                    newQuantity = val + step;
                    updatePrimeButtonQuantity(newQuantity);
                });
                $(document).on('click', '.minus', function(e) {  // replace '.quantity' with document (without single quote)
                    $input = $(this).next('input.qty');
                    var val = parseInt($input.val());
                    var step = $input.attr('step');
                    step = 'undefined' !== typeof(step) ? parseInt(step) : 1;
                    if ((val - step) > 0) {
                        $input.val( val - step ).change();
                    }
                    newQuantity = val + step;
                    updatePrimeButtonQuantity(newQuantity);
                });
                document.getElementById('color').addEventListener('change', function(){
                        $input = $('input.qty');
                        newQuantity = parseInt($input.val());
                        //alert(newQuantity);
                        updatePrimeButtonQuantity(newQuantity);
                    }, false
                );
            });
        </script>



        <?php
    }
}
add_action( 'wp_footer', 'jh_add_script_to_footer' );





add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 9 );

/*
function custom_hello_elementor_viewport_content() {
    return 'width=100vw, height=100vh, user-scalable=no, viewport-fit=contain';
}

add_filter( 'hello_elementor_viewport_content', 'custom_hello_elementor_viewport_content' );
*/
add_action( 'wp_head', 'add_viewport_meta_tag' , '1' );

function add_viewport_meta_tag() {
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=contain">';
}



function coupon_error_message_change($err, $err_code, $WC_Coupon) {
    switch ( $err_code ) {

//CHANGE HIGHLIGHTED COUPON CODE

        case $WC_Coupon::E_WC_COUPON_NOT_APPLICABLE:

            $err = 'Sorry, this coupon cannot be combined with any other discounts.';
    }
    return $err;
}

add_filter( 'woocommerce_coupon_error','coupon_error_message_change',10,3 );




function page_content_mix() {
    global $product;
    #echo 'SKU: ' . $product->get_sku();



    ?>

    <script async fetchpriority='high' src='https://code.buywithprime.amazon.com/bwp.v1.js'></script>
    <div id="prime-button-container">
    <div
            id="amzn-buy-now"
            data-site-id="s12kbpac49"
            data-widget-id="w-CT6r9JYImU8kbfR8WjqCm8"
            data-sku="4745010141287"
            data-quantity ="1"
    ></div>
    </div>
<?php
}

add_action( 'woocommerce_after_add_to_cart_button', 'page_content_mix' );


function enqueue_custom_script() {
    wp_enqueue_script('jquery'); // Veenduge, et jQuery on laetud

    // Euroopa Liidu riikide loend
    $eu_countries = json_encode(array(
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU',
        'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'
    ));

    // Inline JavaScript kood
    $inline_script = "
    jQuery(document).ready(function($) {
        var euCountries = $eu_countries;

        function checkShippingCountry() {
            var shippingCountry = $('#shipping_country').val();

            if (shippingCountry !== 'US' && $.inArray(shippingCountry, euCountries) === -1) {
                $('.outside-us-eu-message').show();
            } else {
                $('.outside-us-eu-message').hide();
            }
        }

        // Kontrollime shipping_country väärtust lehe laadimisel
        checkShippingCountry();

        // Lisame sündmuse kuulaja select välja muutmiseks
        $('#shipping_country').change(function() {
            checkShippingCountry();
        });
    });
    ";

    wp_add_inline_script('jquery', $inline_script);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_script');






/*------------------------------------*\
	Custom Post Types
\*------------------------------------*/

// Create 1 Custom Post type for a Demo, called HTML5-Blank
function create_post_type_logos()
{
    register_taxonomy_for_object_type('category', 'logos'); // Register Taxonomies for Category
    register_taxonomy_for_object_type('post_tag', 'logo');
    register_post_type('logo', // Register Custom Post Type
        array(
        'labels' => array(
            'name' => __('Logos', 'html5blank'), // Rename these to suit
            'singular_name' => __('Logo Custom Post', 'html5blank'),
            'add_new' => __('Add New', 'html5blank'),
            'add_new_item' => __('Add New Logo Custom Post', 'html5blank'),
            'edit' => __('Edit', 'html5blank'),
            'edit_item' => __('Edit Logo Custom Post', 'html5blank'),
            'new_item' => __('New Logo Custom Post', 'html5blank'),
            'view' => __('View Logo Custom Post', 'html5blank'),
            'view_item' => __('View Logo Custom Post', 'html5blank'),
            'search_items' => __('Search Logo Custom Post', 'html5blank'),
            'not_found' => __('No Logo Custom Posts found', 'html5blank'),
            'not_found_in_trash' => __('No Logo Custom Posts found in Trash', 'html5blank')
        ),
        'public' => true,
        'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
        'has_archive' => true,
		'show_in_rest' => true,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail'
        ), // Go to Dashboard Custom HTML5 Blank post for supports
        'can_export' => true, // Allows export in Tools > Export
        'taxonomies' => array(
            'post_tag',
            'category'
        ) // Add Category and Post Tags support
    ));
}

// Create 1 Custom Post type for a Demo, called HTML5-Blank
function create_post_type_cases()
{
    register_taxonomy_for_object_type('category', 'cases'); // Register Taxonomies for Category
    register_taxonomy_for_object_type('post_tag', 'case');
    register_post_type('case-studies', // Register Custom Post Type
        array(
            'labels' => array(
                'name' => __('Case Study', 'html5blank'), // Rename these to suit
                'singular_name' => __('Case Study Custom Post', 'html5blank'),
                'add_new' => __('Add New', 'html5blank'),
                'add_new_item' => __('Add New Case Study Custom Post', 'html5blank'),
                'edit' => __('Edit', 'html5blank'),
                'edit_item' => __('Edit Case Study Custom Post', 'html5blank'),
                'new_item' => __('New Case Study Custom Post', 'html5blank'),
                'view' => __('View Case Study Custom Post', 'html5blank'),
                'view_item' => __('View Case Study Custom Post', 'html5blank'),
                'search_items' => __('Search Case Study Custom Post', 'html5blank'),
                'not_found' => __('No Case Study Custom Posts found', 'html5blank'),
                'not_found_in_trash' => __('No Case Study Custom Posts found in Trash', 'html5blank')
            ),
            'public' => true,
            'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail'
            ), // Go to Dashboard Custom HTML5 Blank post for supports
            'can_export' => true, // Allows export in Tools > Export
            'taxonomies' => array(
                'post_tag',
                'category'
            ) // Add Category and Post Tags support
        ));
}

// Create 1 Custom Post type for a Demo, called HTML5-Blank
function create_post_type_references()
{
    register_taxonomy_for_object_type('category', 'references'); // Register Taxonomies for Category
    register_taxonomy_for_object_type('post_tag', 'reference');
    register_post_type('reference', // Register Custom Post Type
        array(
        'labels' => array(
            'name' => __('References', 'html5blank'), // Rename these to suit
            'singular_name' => __('Reference Custom Post', 'html5blank'),
            'add_new' => __('Add New', 'html5blank'),
            'add_new_item' => __('Add New Reference Custom Post', 'html5blank'),
            'edit' => __('Edit', 'html5blank'),
            'edit_item' => __('Edit Reference Custom Post', 'html5blank'),
            'new_item' => __('New Reference Custom Post', 'html5blank'),
            'view' => __('View Reference Custom Post', 'html5blank'),
            'view_item' => __('View Reference Custom Post', 'html5blank'),
            'search_items' => __('Search Reference Custom Post', 'html5blank'),
            'not_found' => __('No Reference Custom Posts found', 'html5blank'),
            'not_found_in_trash' => __('No Reference Custom Posts found in Trash', 'html5blank')
        ),
        'public' => true,
        'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
        'has_archive' => true,
		'show_in_rest' => true,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail'
        ), // Go to Dashboard Custom HTML5 Blank post for supports
        'can_export' => true, // Allows export in Tools > Export
        'taxonomies' => array(
            'post_tag',
            'category'
        ) // Add Category and Post Tags support
    ));
}

// FAQ custom post
function create_post_type_faq()
{
    register_taxonomy_for_object_type('category', 'faq'); // Register Taxonomies for Category
    register_taxonomy_for_object_type('post_tag', 'faq');
    register_post_type('faq', // Register Custom Post Type
        array(
            'labels' => array(
                'name' => __('FAQ', 'html5blank'), // Rename these to suit
                'singular_name' => __('FAQ Custom Post', 'html5blank'),
                'add_new' => __('Add New', 'html5blank'),
                'add_new_item' => __('Add New FAQ Custom Post', 'html5blank'),
                'edit' => __('Edit', 'html5blank'),
                'edit_item' => __('Edit FAQ Custom Post', 'html5blank'),
                'new_item' => __('New FAQ Custom Post', 'html5blank'),
                'view' => __('View FAQ Custom Post', 'html5blank'),
                'view_item' => __('View FAQ Custom Post', 'html5blank'),
                'search_items' => __('Search FAQ Custom Post', 'html5blank'),
                'not_found' => __('No FAQ Custom Posts found', 'html5blank'),
                'not_found_in_trash' => __('No FAQ Custom Posts found in Trash', 'html5blank')
            ),
            'public' => true,
            'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
            'has_archive' => false,
            'show_in_rest' => true,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail'
            ), // Go to Dashboard Custom HTML5 Blank post for supports
            'can_export' => true, // Allows export in Tools > Export
            'taxonomies' => array(
                'post_tag',
                'category'
            ) // Add Category and Post Tags support
        ));
}



// Press custom post
function create_post_type_press()
{
    register_taxonomy_for_object_type('category', 'press'); // Register Taxonomies for Category
    register_taxonomy_for_object_type('post_tag', 'press');
    register_post_type('press', // Register Custom Post Type
        array(
            'labels' => array(
                'name' => __('Press', 'html5blank'), // Rename these to suit
                'singular_name' => __('Press Custom Post', 'html5blank'),
                'add_new' => __('Add New', 'html5blank'),
                'add_new_item' => __('Add New Press Custom Post', 'html5blank'),
                'edit' => __('Edit', 'html5blank'),
                'edit_item' => __('Edit Press Custom Post', 'html5blank'),
                'new_item' => __('New Press Custom Post', 'html5blank'),
                'view' => __('View Press Custom Post', 'html5blank'),
                'view_item' => __('View Press Custom Post', 'html5blank'),
                'search_items' => __('Search Press Custom Post', 'html5blank'),
                'not_found' => __('No Press Custom Posts found', 'html5blank'),
                'not_found_in_trash' => __('No Press Custom Posts found in Trash', 'html5blank')
            ),
            'public' => true,
            'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
            'has_archive' => false,
            'show_in_rest' => true,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail'
            ), // Go to Dashboard Custom HTML5 Blank post for supports
            'can_export' => true, // Allows export in Tools > Export
        ));
}



// Contact custom post
function create_post_type_contact()
{
    register_taxonomy_for_object_type('category', 'contact'); // Register Taxonomies for Category
    register_taxonomy_for_object_type('post_tag', 'contact');
    register_post_type('contact', // Register Custom Post Type
        array(
            'labels' => array(
                'name' => __('Contact', 'html5blank'), // Rename these to suit
                'singular_name' => __('Contact Custom Post', 'html5blank'),
                'add_new' => __('Add New', 'html5blank'),
                'add_new_item' => __('Add New Contact Custom Post', 'html5blank'),
                'edit' => __('Edit', 'html5blank'),
                'edit_item' => __('Edit Contact Custom Post', 'html5blank'),
                'new_item' => __('New Contact Custom Post', 'html5blank'),
                'view' => __('View Contact Custom Post', 'html5blank'),
                'view_item' => __('View Contact Custom Post', 'html5blank'),
                'search_items' => __('Search Contact Custom Post', 'html5blank'),
                'not_found' => __('No Contact Custom Posts found', 'html5blank'),
                'not_found_in_trash' => __('No Contact Custom Posts found in Trash', 'html5blank')
            ),
            'public' => true,
            'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
            'has_archive' => false,
            'show_in_rest' => true,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail'
            ), // Go to Dashboard Custom HTML5 Blank post for supports
            'can_export' => true, // Allows export in Tools > Export
        ));
}


// Create 1 Custom Post type for a Demo, called HTML5-Blank
function create_post_type_benefits()
{
    register_taxonomy_for_object_type('category', 'benefits'); // Register Taxonomies for Category
    register_taxonomy_for_object_type('post_tag', 'benefit');
    register_post_type('benefit', // Register Custom Post Type
        array(
            'labels' => array(
                'name' => __('Benefits', 'html5blank'), // Rename these to suit
                'singular_name' => __('Benefits Custom Post', 'html5blank'),
                'add_new' => __('Add New', 'html5blank'),
                'add_new_item' => __('Add New Benefits Custom Post', 'html5blank'),
                'edit' => __('Edit', 'html5blank'),
                'edit_item' => __('Edit Benefits Custom Post', 'html5blank'),
                'new_item' => __('New Benefits Custom Post', 'html5blank'),
                'view' => __('View Benefits Custom Post', 'html5blank'),
                'view_item' => __('View Benefits Custom Post', 'html5blank'),
                'search_items' => __('Search Benefits Custom Post', 'html5blank'),
                'not_found' => __('No Benefits Custom Posts found', 'html5blank'),
                'not_found_in_trash' => __('No Benefits Custom Posts found in Trash', 'html5blank')
            ),
            'public' => true,
            'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail'
            ), // Go to Dashboard Custom HTML5 Blank post for supports
            'can_export' => true, // Allows export in Tools > Export
            'taxonomies' => array(
                'post_tag',
                'category'
            ) // Add Category and Post Tags support
        ));
}


// Create 1 Custom Post type for a Demo, called HTML5-Blank
function create_post_type_instaposts()
{
    register_taxonomy_for_object_type('category', 'instaposts'); // Register Taxonomies for Category
    register_taxonomy_for_object_type('post_tag', 'instapost');
    register_post_type('instapost', // Register Custom Post Type
        array(
            'labels' => array(
                'name' => __('Instagram posts', 'html5blank'), // Rename these to suit
                'singular_name' => __('Instagram posts Custom Post', 'html5blank'),
                'add_new' => __('Add New', 'html5blank'),
                'add_new_item' => __('Add New Instagram posts Custom Post', 'html5blank'),
                'edit' => __('Edit', 'html5blank'),
                'edit_item' => __('Edit Instagram posts Custom Post', 'html5blank'),
                'new_item' => __('New Instagram posts Custom Post', 'html5blank'),
                'view' => __('View Instagram posts Custom Post', 'html5blank'),
                'view_item' => __('View Instagram posts Custom Post', 'html5blank'),
                'search_items' => __('Search Instagram posts Custom Post', 'html5blank'),
                'not_found' => __('No Instagram posts Custom Posts found', 'html5blank'),
                'not_found_in_trash' => __('No Instagram posts Custom Posts found in Trash', 'html5blank')
            ),
            'public' => true,
            'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail'
            ), // Go to Dashboard Custom HTML5 Blank post for supports
            'can_export' => true, // Allows export in Tools > Export
            'taxonomies' => array(
                'post_tag',
                'category'
            ) // Add Category and Post Tags support
        ));
}

// Create 1 Custom Post type for a Demo, called HTML5-Blank
function create_post_type_tiktokvideos()
{
    register_taxonomy_for_object_type('category', 'tiktokvideos'); // Register Taxonomies for Category
    register_taxonomy_for_object_type('post_tag', 'tiktokvideo');
    register_post_type('tiktokvideo', // Register Custom Post Type
        array(
            'labels' => array(
                'name' => __('TikTok posts', 'html5blank'), // Rename these to suit
                'singular_name' => __('TikTok posts Custom Post', 'html5blank'),
                'add_new' => __('Add New', 'html5blank'),
                'add_new_item' => __('Add New TikTok posts Custom Post', 'html5blank'),
                'edit' => __('Edit', 'html5blank'),
                'edit_item' => __('Edit TikTok posts Custom Post', 'html5blank'),
                'new_item' => __('New TikTok posts Custom Post', 'html5blank'),
                'view' => __('View TikTok posts Custom Post', 'html5blank'),
                'view_item' => __('View TikTok posts Custom Post', 'html5blank'),
                'search_items' => __('Search TikTok posts Custom Post', 'html5blank'),
                'not_found' => __('No TikTok posts Custom Posts found', 'html5blank'),
                'not_found_in_trash' => __('No TikTok posts Custom Posts found in Trash', 'html5blank')
            ),
            'public' => true,
            'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail'
            ), // Go to Dashboard Custom HTML5 Blank post for supports
            'can_export' => true, // Allows export in Tools > Export
            'taxonomies' => array(
                'post_tag',
                'category'
            ) // Add Category and Post Tags support
        ));
}

// Create 1 Custom Post type for a Demo, called HTML5-Blank
function create_post_type_ytreviews()
{
    register_taxonomy_for_object_type('category', 'ytreviews'); // Register Taxonomies for Category
    register_taxonomy_for_object_type('post_tag', 'ytreview');
    register_post_type('ytreview', // Register Custom Post Type
        array(
            'labels' => array(
                'name' => __('Youtube review posts', 'html5blank'), // Rename these to suit
                'singular_name' => __('Youtube review posts Custom Post', 'html5blank'),
                'add_new' => __('Add New', 'html5blank'),
                'add_new_item' => __('Add New Youtube review posts Custom Post', 'html5blank'),
                'edit' => __('Edit', 'html5blank'),
                'edit_item' => __('Edit Youtube review posts Custom Post', 'html5blank'),
                'new_item' => __('New Youtube review posts Custom Post', 'html5blank'),
                'view' => __('View Youtube review posts Custom Post', 'html5blank'),
                'view_item' => __('View Youtube review posts Custom Post', 'html5blank'),
                'search_items' => __('Search Youtube review posts Custom Post', 'html5blank'),
                'not_found' => __('No Youtube review posts Custom Posts found', 'html5blank'),
                'not_found_in_trash' => __('No Youtube review posts Custom Posts found in Trash', 'html5blank')
            ),
            'public' => true,
            'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail'
            ), // Go to Dashboard Custom HTML5 Blank post for supports
            'can_export' => true, // Allows export in Tools > Export
            'taxonomies' => array(
                'post_tag',
                'category'
            ) // Add Category and Post Tags support
        ));
}


/*------------------------------------*\
	ShortCode Functions
\*------------------------------------*/

// Shortcode Demo with Nested Capability
function html5_shortcode_demo($atts, $content = null)
{
    return '<div class="shortcode-demo">' . do_shortcode($content) . '</div>'; // do_shortcode allows for nested Shortcodes
}

// Shortcode Demo with simple <h2> tag
function html5_shortcode_demo_2($atts, $content = null) // Demo Heading H2 shortcode, allows for nesting within above element. Fully expandable.
{
    return '<h2>' . $content . '</h2>';
}

class WFACP_Triger_IntlFlag_Change_billing_same_as_shipping {

    public function __construct() {
        add_action( 'wfacp_internal_css', [ $this, 'call_js' ], 999 );

    }


    public function call_js() {
        ?>

        <script>

            window.addEventListener('load', function () {
                (function ($) {

                    if (jQuery('#billing_same_as_shipping').length > 0) {
                        jQuery(document.body).on('change', '#billing_same_as_shipping,.billing_same_as_shipping', function () {
                            jQuery(document.body).trigger('wfacp_intl_setup');
                        });

                    }


                })(jQuery);

            });

        </script>
        <?php

    }
}

new WFACP_Triger_IntlFlag_Change_billing_same_as_shipping();



function display_product_main_and_variation_images($atts) {
    // Atribuutide määratlemine ja vaikimisi väärtuste seadmine
    $atts = shortcode_atts(
        array(
            'id' => '', // Toote ID
        ),
        $atts,
        'product_variation_images'
    );

    // Kui toote ID pole määratletud, tagastage tühi string
    if (empty($atts['id'])) {
        return '';
    }

    // Hankige toote objekt
    $product = wc_get_product($atts['id']);

    // Kui toote objekti pole leitud, tagastage tühi string
    if (!$product) {
        return '';
    }

    // Määratle kohandatud pilt konkreetsete toodete jaoks
    $custom_image_products = array(30983, 32755, 32676); // Toote ID-d
    $custom_image_url = 'https://respiray.com/wp-content/uploads/2023/03/Respiray-Wear-A-Wearable-Air-Purifier-black-and-white.webp';

    // Alustame väljundit
    $output = '<div class="single-product">';

    // Kontrolli, kas toote ID on määratud ID-de seas
    if (in_array($atts['id'], $custom_image_products)) {
        // Kuvame kohandatud pildi
        $output .= '<div class="product-main-image"><img src="' . esc_url($custom_image_url) . '" alt="Custom Product Image"></div>';
    } else {
        // Kuvame toote põhipildi
        $output .= '<div class="product-main-image">' . $product->get_image() . '</div>';
    }

    // Kui tootel on variatsioone, hankige variatsioonide pildid
    if ($product->is_type('variable')) {
        $variations = $product->get_available_variations();
        if (!empty($variations)) {
            $output .= '<div class="product-variation-images">';

            foreach ($variations as $variation) {
                $variation_id = $variation['variation_id'];
                $variation_product = wc_get_product($variation_id);
                if ($variation_product && $variation_product->get_image_id()) {
                    $output .= '<div class="product-variation-image" id="variation_'.$variation_id.'">' . $variation_product->get_image() . '</div>';
                }
            }
            $output .= '</div>';
        }
    }

    $output .= '</div>'; // Lõpeta single-product div

    return $output;
}
add_shortcode('product_variation_images', 'display_product_main_and_variation_images');


function display_product_price($atts) {
    // Atribuutide määratlemine ja vaikimisi väärtuste seadmine
    $atts = shortcode_atts(
        array(
            'id' => '', // Toote ID
        ),
        $atts,
        'product_price'
    );

    // Kui toote ID pole määratletud, tagastage tühi string
    if (empty($atts['id'])) {
        return '';
    }

    // Hankige toote objekt
    $product = wc_get_product($atts['id']);

    // Kui toote objekti pole leitud, tagastage tühi string
    if (!$product) {
        return '';
    }

    // Alustame väljundit
    $output = '<div class="single-product-price">';

    // Hankige toote hind
    $output .= '<div class="product-main-price">' . $product->get_price_html() . '</div>';



    $output .= '</div>'; // Lõpeta single-product-price div

    return $output;
}
add_shortcode('product_price', 'display_product_price');











add_action('wp_ajax_add_multiple_products_to_cart', 'custom_add_multiple_products_to_cart');
add_action('wp_ajax_nopriv_add_multiple_products_to_cart', 'custom_add_multiple_products_to_cart');

function custom_add_multiple_products_to_cart() {
    $added_products = [];
    $errors = [];

    // Add variable product if IDs are provided
    if (isset($_POST['product_id']) && isset($_POST['variation_id'])) {
        $product_id = absint($_POST['product_id']);
        $variation_id = absint($_POST['variation_id']);
        $quantity = 1; // Adjust quantity as needed

        // Get the variation attributes if necessary (for products with complex variations)
        $variation = new WC_Product_Variation($variation_id);
        $variation_attributes = $variation->get_attributes();

        $added = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation_attributes);
        if ($added) {
            $added_products[] = "Variable product (ID: $product_id with variation $variation_id) added successfully.";
        } else {
            $errors[] = "Failed to add variable product (ID: $product_id with variation $variation_id) to the cart.";
        }
    }


    // Add left variation product if provided
    if (isset($_POST['product_id']) && isset($_POST['left_variation_id'])) {
        $product_id = absint($_POST['product_id']);
        $left_variation_id = absint($_POST['left_variation_id']);
        $quantity = 1; // Adjust quantity as needed

        // Get the variation attributes if necessary
        $variation = new WC_Product_Variation($left_variation_id);
        $variation_attributes = $variation->get_attributes();

        $added = WC()->cart->add_to_cart($product_id, $quantity, $left_variation_id, $variation_attributes);
        if ($added) {
            $added_products[] = "Left variation (ID: $left_variation_id) added successfully.";
        } else {
            $errors[] = "Failed to add left variation (ID: $left_variation_id) to the cart.";
        }
    }

    // Add right variation product if provided
    if (isset($_POST['product_id']) && isset($_POST['right_variation_id'])) {
        $product_id = absint($_POST['product_id']);
        $right_variation_id = absint($_POST['right_variation_id']);
        $quantity = 1; // Adjust quantity as needed

        // Get the variation attributes if necessary
        $variation = new WC_Product_Variation($right_variation_id);
        $variation_attributes = $variation->get_attributes();

        $added = WC()->cart->add_to_cart($product_id, $quantity, $right_variation_id, $variation_attributes);
        if ($added) {
            $added_products[] = "Right variation (ID: $right_variation_id) added successfully.";
        } else {
            $errors[] = "Failed to add right variation (ID: $right_variation_id) to the cart.";
        }
    }





    // Add simple product if provided
    if (isset($_POST['simple_product_id'])) {
        $simple_product_id = absint($_POST['simple_product_id']);
        $quantity = 1; // Adjust quantity as needed

        $added = WC()->cart->add_to_cart($simple_product_id, $quantity);
        if ($added) {
            $added_products[] = "Simple product (ID: $simple_product_id) added successfully.";
        } else {
            $errors[] = "Failed to add simple product (ID: $simple_product_id) to the cart.";
        }
    }

    // Respond with success or error
    if (!empty($added_products)) {
        wp_send_json_success(['message' => implode(' ', $added_products)]);
    } else {
        wp_send_json_error(['message' => implode(' ', $errors)]);
    }

    wp_die();
}








// Funktsioon konkreetse toote kuvamiseks
function display_single_product($atts) {
    // Atribuutide määratlemine ja vaikimisi väärtuste seadmine
    $atts = shortcode_atts(
        array(
            'id' => '', // Toote ID
        ),
        $atts,
        'single_product'
    );

    // Kui toote ID pole määratletud, tagastage tühi string
    if (empty($atts['id'])) {
        return 'Toote ID on määratlemata.';
    }

    // Hankige toote objekt
    $product = wc_get_product($atts['id']);

    // Kui toote objekti pole leitud, tagastage teavitus
    if (!$product) {
        return 'Toodet ei leitud.';
    }

    // Alustame väljundit
    $output = '<div class="single-product">';

    // Hankige toote pilt
    #$output .= '<div class="product-image">' . $product->get_image() . '</div>';

    // Hankige toote nimi
    #$output .= '<div class="product-name">' . $product->get_name() . '</div>';

    // Hankige toote hind
    #$output .= '<div class="product-price">' . $product->get_price_html() . '</div>';

    // Kui tootel on variatsioon (värvivalik), kuvatakse dropdown menüü
    if ($product->is_type('variable')) {
        $variations = $product->get_available_variations();
        if (!empty($variations)) {
            $output .= '<form class="variations_form cart" action="' . esc_url($product->add_to_cart_url()) . '" method="post" enctype="multipart/form-data">';
            $output .= '<table class="variations custom_variations_solution" cellspacing="0"><tbody>';
            $output .= '<tr><td class="label"><label for="attribute_pa_color">' . __('Color', 'woocommerce') . '</label></td>';
            $output .= '<td class="value"><select name="attribute_pa_color" id="attribute_pa_color">';

            foreach ($variations as $variation) {
                $variation_product = wc_get_product($variation['variation_id']);
                $attributes = $variation_product->get_attributes();
                foreach ($attributes as $attribute_name => $attribute_value) {
                    if ($attribute_name === 'color' || $attribute_name === 'select-color') {
                        $output .= '<option value="' . esc_attr($attribute_value) . '" data-variation-id="' . esc_attr($variation['variation_id']) . '">' . esc_html($attribute_value) . '</option>';
                    }
                }
            }

            $output .= '</select></td></tr>';
            $output .= '<tr><td colspan="2"><div class="choose_color_custom"><div class="custom_white sel"><span>White</span></div><div class="custom_black"><span>Black</span></div><div class="clear"></div></div></td></tr>';
            $output .= '<tr>';
            $output .= '<td colspan="2">';
            $output .= '<div class="quantity">';
            $output .= '<input class="minus" type="button" value="-">';
            #$output .= '<input type="text" id="product-quantity" name="quantity" value="1" min="1" step="1" />';
            $output .= '<input type="text" step="1" min="1" max="" name="quantity" value="1" title="Qty" class="input-text qty text" size="4" pattern="[0-9]*" inputmode="numeric" />';
            $output .= '<input class="plus" type="button" value="+">';
            $output .= '</td></tr>';
            $output .= '</div>';
            $output .= '</tbody></table>';
            $output .= '<div class="single_variation_wrap"><div class="single_variation"></div>';
            $output .= '<button type="submit" class="single_add_to_cart_button button alt">Add to cart</button>';
            $output .= '<input type="hidden" name="add-to-cart" value="' . absint($product->get_id()) . '" />';
            $output .= '<input type="hidden" name="product_id" value="' . absint($product->get_id()) . '" />';
            $output .= '<input type="hidden" name="variation_id" class="variation_id" value="" />';
            $output .= '</div></form>';
        }
    } else {
        // Kui toode ei ole variatsioonidega, kuvatakse tavaline "Lisa korvi" nupp
        $output .= '<form class="cart" action="' . esc_url($product->add_to_cart_url()) . '" method="post" enctype="multipart/form-data">';
        $output .= '<button type="submit" id="single_add_to_cart_button" class="single_add_to_cart_button button alt">' . esc_html($product->single_add_to_cart_text()) . '</button>';
        $output .= '<input type="hidden" name="add-to-cart" value="' . absint($product->get_id()) . '" />';
        $output .= '</form>';
    }

    $output .= '</div>'; // Lõpeta single-product div

    return $output;
}
add_shortcode('single_product', 'display_single_product');

// Lisage vajalikud skriptid ja stiilid
function enqueue_custom_woocommerce_scripts() {
    if (!is_product()) {
        wp_enqueue_script('wc-add-to-cart-variation');
        wp_enqueue_script('wc-add-to-cart');
        wp_enqueue_script('wc-cart-fragments');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_custom_woocommerce_scripts');

// JavaScript variatsiooni ID seadmiseks
function add_variation_id_script() {
    ?>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Kui dropdown menüüs on muutus, siis uuenda variatsiooni ID
            $('#attribute_pa_color').on('change', function() {
                var variationId = $(this).find(':selected').data('variation-id');
                console.log(variationId);
                setTimeout(function() {
                    $('.variation_id').val(variationId);
                }, 500); // 500 millisekundi viivitus
            });

            // Vormikontroll enne esitust
            $('.variations_form').on('submit', function(e) {
                e.preventDefault(); // Vältida vormi vaikimisi käitumist

                var form = $(this);
                var product_id = form.find('input[name="product_id"]').val();
                var variation_id = form.find('input[name="variation_id"]').val();
                var quantity = form.find('input[name="quantity"]').val();
                var attribute_color = form.find('#attribute_pa_color').val();
                var addToCartButton = form.find('.single_add_to_cart_button');

                if (!variation_id) {
                    alert("Palun valige variatsioon.");
                    return;
                }

                // Disable nupp ja näita loaderit
                addToCartButton.prop('disabled', true); // Muuda teksti
                addToCartButton.append('<span class="loading-spinner"></span>'); // Lisa loader


                var data = {
                    action: 'woocommerce_ajax_add_to_cart',
                    product_id: product_id,
                    variation_id: variation_id,
                    quantity: quantity,
                    attribute_color: attribute_color
                };

                $.ajax({
                    type: 'POST',
                    url: wc_add_to_cart_params.ajax_url,
                    data: data,
                    /*
                    success: function(response) {
                        if (response.error && response.product_url) {
                            window.location = response.product_url;
                            return;
                        } else {
                            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, form]);
                            // Näidake näiteks eduteadet või värskendage ostukorvi ikooni
                            //alert("Toode lisati ostukorvi!");
                        }
                    }*/
                    data: data,
                    success: function(response) {
                        if (response.error && response.product_url) {
                            window.location = response.product_url;
                            return;
                        } else {
                            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, form]);
                            // Näidake näiteks eduteadet või värskendage ostukorvi ikooni
                           //alert("Toode lisati ostukorvi!");

                            // Eemalda loader ja taasta nupp
                            addToCartButton.prop('disabled', false).text('Add to cart');
                            addToCartButton.find('.loading-spinner').remove();
                        }
                    },
                    error: function() {
                        // Tõrke korral taasta nupp ja eemalda loader
                        addToCartButton.prop('disabled', false).text('Add to cart');
                        addToCartButton.find('.loading-spinner').remove();
                        //alert("Tekkis viga toote ostukorvi lisamisel. Palun proovige uuesti.");
                    }
                });
            });

            // Esialgse valiku seadistamine (kui see on olemas)
            var initialVariationId = $('#attribute_pa_color').find(':selected').data('variation-id');
            if (initialVariationId) {
                $('.variation_id').val(initialVariationId);
            }
            $('.product-variation-image img').click(function(){
                $('.product-main-image > img').remove();
                $('.product-main-image').append($(this).clone());
                console.log(this);
                var parentId = $(this).parent().attr('id'); // Hankige vanema elemendi ID
                if (parentId == 'variation_7269') {
                    $('#attribute_pa_color').val('White');
                    setTimeout(function() {
                        $('.variation_id').val('7269');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_white').addClass('sel');

                } else if(parentId == 'variation_7268') {
                    $('#attribute_pa_color').val('Black');
                    setTimeout(function() {
                        $('.variation_id').val('7268');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_black').addClass('sel');

                } else if(parentId == 'variation_23159') {
                    $('#attribute_pa_color').val('White');
                    setTimeout(function() {
                        $('.variation_id').val('23159');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_white').addClass('sel');
                } else if(parentId == 'variation_23160') {
                    $('#attribute_pa_color').val('Black');
                    setTimeout(function() {
                        $('.variation_id').val('23160');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_black').addClass('sel');
                } else if(parentId == 'variation_27486') { //27486 white
                    $('#attribute_pa_color').val('White');
                    setTimeout(function() {
                        $('.variation_id').val('27486');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_white').addClass('sel');
                } else if(parentId == 'variation_27508') { //27508 white
                    $('#attribute_pa_color').val('White');
                    setTimeout(function() {
                        $('.variation_id').val('27508');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_white').addClass('sel');
                } else if(parentId == 'variation_30984') { //30984 white
                    $('#attribute_pa_color').val('White');
                    setTimeout(function() {
                        $('.variation_id').val('30984');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_white').addClass('sel');
                } else if(parentId == 'variation_32756') { //32756 white
                    $('#attribute_pa_color').val('White');
                    setTimeout(function() {
                        $('.variation_id').val('32756');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_white').addClass('sel');
                } else if(parentId == 'variation_32677') { //32677 white
                    $('#attribute_pa_color').val('White');
                    setTimeout(function() {
                        $('.variation_id').val('32677');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_white').addClass('sel');
                } else if(parentId == 'variation_27487') { //27487 Black
                    $('#attribute_pa_color').val('Black');
                    setTimeout(function() {
                        $('.variation_id').val('27487');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_black').addClass('sel');
                } else if(parentId == 'variation_27509') { //27509 Black
                    $('#attribute_pa_color').val('Black');
                    setTimeout(function() {
                        $('.variation_id').val('27509');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_black').addClass('sel');
                } else if(parentId == 'variation_30985') { //30985 Black
                    $('#attribute_pa_color').val('Black');
                    setTimeout(function() {
                        $('.variation_id').val('30985');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_black').addClass('sel');
                } else if(parentId == 'variation_32678') { //32678 Black
                    $('#attribute_pa_color').val('Black');
                    setTimeout(function() {
                        $('.variation_id').val('32678');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_black').addClass('sel');
                } else if(parentId == 'variation_32757') { //32757 Black
                    $('#attribute_pa_color').val('Black');
                    setTimeout(function() {
                        $('.variation_id').val('32757');
                    }, 500); // 500 millisekundi viivitus

                    $('.custom_white, .custom_black').removeClass('sel');
                    $('.custom_black').addClass('sel');
                }
            });

            $('.custom_white, .custom_black').click(function(){
                $('.custom_white, .custom_black').removeClass('sel');
                $(this).addClass('sel');
                //$('.product-main-image > img').remove();
                if ($(this).hasClass('custom_white')) {
                    $('#attribute_pa_color').val('White');
                    $('.product-main-image > img').remove();
                    $('.product-main-image').append($('#variation_7269 img').clone());
                    $('.product-main-image').append($('#variation_23159 img').clone());
                    $('.product-main-image').append($('#variation_27486 img').clone());
                    $('.product-main-image').append($('#variation_27508 img').clone());
                    $('.product-main-image').append($('#variation_30984 img').clone());
                    $('.product-main-image').append($('#variation_32756 img').clone());
                    $('.product-main-image').append($('#variation_32677 img').clone());
                } else if ($(this).hasClass('custom_black')) {
                    $('#attribute_pa_color').val('Black');
                    $('.product-main-image > img').remove();
                    $('.product-main-image').append($('#variation_7268 img').clone());
                    $('.product-main-image').append($('#variation_23160 img').clone());
                    $('.product-main-image').append($('#variation_27487 img').clone());
                    $('.product-main-image').append($('#variation_27509 img').clone());
                    $('.product-main-image').append($('#variation_30985 img').clone());
                    $('.product-main-image').append($('#variation_32757 img').clone());
                    $('.product-main-image').append($('#variation_32678 img').clone());
                }

                // Käivita change sündmus, et veenduda, et variatsioonid värskendatakse
                $('#attribute_pa_color').trigger('change');
            });
        });
    </script>



    <?php
}
add_action('wp_footer', 'add_variation_id_script');



add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');

function woocommerce_ajax_add_to_cart() {
    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
    $variation_id = $_POST['variation_id'];
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    $product_status = get_post_status($product_id);

    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {
        do_action('woocommerce_ajax_added_to_cart', $product_id);
        if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
            wc_add_to_cart_message(array($product_id => $quantity), true);
        }
        WC_AJAX :: get_refreshed_fragments();
    } else {
        $data = array(
            'error' => true,
            'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
        );
        echo $data;
    }

    wp_die();
}



add_filter( 'fkcart_item_image_size', function(){
    return 'woocommerce_thumbnail';
} );

/*
 * ADD Disabled for coupons checkbox to product edit view
*/

// Create and display the custom field in product general setting tab
add_action( 'woocommerce_product_options_general_product_data', 'add_custom_field_general_product_fields' );
function add_custom_field_general_product_fields(){
    global $post;

    echo '<div class="product_custom_field">';

    // Custom Product Checkbox Field
    woocommerce_wp_checkbox( array(
        'id'        => '_disabled_for_coupons',
        'label'     => __('Disabled for coupons', 'woocommerce'),
        'description' => __('Disable this products from coupon discounts', 'woocommerce'),
        'desc_tip'  => 'true',
    ) );

    echo '</div>';;
}

// Save the custom field and update all excluded product Ids in option WP settings
add_action( 'woocommerce_process_product_meta', 'save_custom_field_general_product_fields', 10, 1 );
function save_custom_field_general_product_fields( $post_id ){

    $current_disabled = isset( $_POST['_disabled_for_coupons'] ) ? 'yes' : 'no';

    $disabled_products = get_option( '_products_disabled_for_coupons' );
    if( empty($disabled_products) ) {
        if( $current_disabled == 'yes' )
            $disabled_products = array( $post_id );
    } else {
        if( $current_disabled == 'yes' ) {
            $disabled_products[] = $post_id;
            $disabled_products = array_unique( $disabled_products );
        } else {
            if ( ( $key = array_search( $post_id, $disabled_products ) ) !== false )
                unset( $disabled_products[$key] );
        }
    }

    update_post_meta( $post_id, '_disabled_for_coupons', $current_disabled );
    update_option( '_products_disabled_for_coupons', $disabled_products );
}

// Make coupons invalid at product level
add_filter('woocommerce_coupon_is_valid_for_product', 'set_coupon_validity_for_excluded_products', 12, 4);
function set_coupon_validity_for_excluded_products($valid, $product, $coupon, $values ){
    if( ! count(get_option( '_products_disabled_for_coupons' )) > 0 ) return $valid;

    $disabled_products = get_option( '_products_disabled_for_coupons' );
    if( in_array( $product->get_id(), $disabled_products ) )
        $valid = false;

    return $valid;
}

// Set the product discount amount to zero
add_filter( 'woocommerce_coupon_get_discount_amount', 'zero_discount_for_excluded_products', 12, 5 );
function zero_discount_for_excluded_products($discount, $discounting_amount, $cart_item, $single, $coupon ){
    if( ! count(get_option( '_products_disabled_for_coupons' )) > 0 ) return $discount;

    $disabled_products = get_option( '_products_disabled_for_coupons' );
    if( in_array( $cart_item['product_id'], $disabled_products ) )
        $discount = 0;

    return $discount;
}

/*
 * END of
 * ADD Disabled for coupons checkbox to product edit view
 */

/*
 * Black Friday discount rules
 *
 */



/* END
 * Black Friday discount rules
 *
 */


/*
 * Extra images for variable products
 *
 */

// Lisa uus galerii väli variatsioonide redigeerimise sektsiooni
add_action('woocommerce_variation_options_pricing', 'add_variation_gallery_field', 10, 3);
function add_variation_gallery_field($loop, $variation_data, $variation) {
    ?>
    <div class="form-row form-row-full">
        <label for="variation_gallery_<?php echo $variation->ID; ?>"><?php _e('Additional Gallery Images', 'woocommerce'); ?></label>
        <input type="hidden" class="variation_gallery_field" name="variation_gallery[<?php echo $variation->ID; ?>]" value="<?php echo esc_attr(get_post_meta($variation->ID, '_variation_gallery', true)); ?>" />
        <button type="button" class="button upload_gallery_button"><?php _e('Add Images', 'woocommerce'); ?></button>
        <div class="variation_gallery_preview">
            <?php
            // Laeme olemasolevad pildid, kui need on olemas
            $gallery_images = explode(',', get_post_meta($variation->ID, '_variation_gallery', true));
            if (!empty($gallery_images)) {
                foreach ($gallery_images as $image) {
                    if ($image) {
                        echo '<div class="gallery-item">';
                        echo '<img src="' . esc_url($image) . '" style="max-width: 100px; margin-right: 10px;" />';
                        echo '<button type="button" class="remove_gallery_image button">' . __('Remove', 'woocommerce') . '</button>';
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
    </div>
    <?php
}




// Salvesta lisatud galerii
add_action('save_post_product', 'save_variation_gallery_field', 10, 3);

function save_variation_gallery_field($post_id, $post, $update) {
    // Kontrollime, kas tegemist on WooCommerce tootega
    if ('product' !== $post->post_type || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Logime POST-i andmed
    error_log("Saving gallery for product ID: $post_id");
    error_log("POST data: " . print_r($_POST, true));

    // Kontrollime, kas on olemas variatsioonid ja nende galeriiandmed
    if (isset($_POST['variation_gallery']) && is_array($_POST['variation_gallery'])) {
        foreach ($_POST['variation_gallery'] as $variation_id => $gallery_data) {
            if (!empty($gallery_data)) {
                // Salvestame galerii metaandmed variatsioonile
                update_post_meta($variation_id, '_variation_gallery', sanitize_text_field($gallery_data));
                error_log("Gallery data saved for variation ID $variation_id: $gallery_data");
            } else {
                // Kustutame olemasolevad metaandmed, kui uut andmed pole
                delete_post_meta($variation_id, '_variation_gallery');
                error_log("No gallery data. Deleted meta for variation ID: $variation_id");
            }
        }
    } else {
        error_log("No variation gallery data found in POST.");
    }
}

// Laadi vajalik JavaScript ja CSS admin poolele
add_action('admin_enqueue_scripts', 'load_variation_gallery_scripts');
function load_variation_gallery_scripts($hook) {
    if ('post.php' === $hook || 'post-new.php' === $hook) {
        wp_enqueue_media();
        wp_enqueue_script('variation-gallery-script', get_template_directory_uri() . '/js/variationgallery.js?v=712', array('jquery'), null, true);
    }
}

// shop vaates variatsiooniga toodete piltide vahetamine
add_filter('woocommerce_available_variation', 'replace_variation_images_with_custom', 10, 3);

function replace_variation_images_with_custom($variation_data, $product, $variation) {
    // Kontrollime, kas oleme poe (shop) lehel
    if (!function_exists('is_shop') || !is_shop()) {
        return $variation_data; // Kui pole shop leht, tagastame algse andmed
    }

    // Otsime variatsiooni custom-pildi
    $custom_image = get_post_meta($variation->get_id(), '_variation_gallery', true);

    if (!empty($custom_image)) {
        // Asendame variatsiooni pildi custom-pildiga
        $variation_data['image']['src'] = $custom_image;
        $variation_data['image']['thumb_src'] = $custom_image;
        $variation_data['image']['gallery_thumbnail_src'] = $custom_image;
    }

    return $variation_data;
}





/*

// Kuvame variatsiooni galeriid administraatorivaates
add_action('woocommerce_variation_options_pricing', 'display_variation_gallery_preview', 10, 3);
function display_variation_gallery_preview($loop, $variation_data, $variation) {
    // Loeme metaandmed
    $gallery = get_post_meta($variation->ID, '_variation_gallery', true);
    ?>
    <div class="variation_gallery_preview">
        <?php if ($gallery) : ?>
            <img src="<?php echo esc_url($gallery); ?>" alt="Gallery Image" style="max-width: 100%; height: auto;" />
        <?php else : ?>
            <p><?php _e('No gallery image assigned.', 'woocommerce'); ?></p>
        <?php endif; ?>
    </div>
    <?php
}
*/

/* END
 * Extra images for variable products
 *
 */




/*
 *
 * Redirect FunnelKit checkout to custom URL for specific landing page
 */
function custom_checkout_url_script() {
    if ( !is_admin() ) {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                //const validPaths = ['/nf1/', '/product/respiray-neck-fan/'];
                const validPaths = ['/nf1/', '/nf2/', '/product/respiray-neck-fan/'];



                if (!validPaths.some(path => window.location.href.includes(path))) return;
                //if (!window.location.href.includes('/nf1/')) return;

                const targetHref = 'https://respiray.com/checkouts/nf/';
                const buttonSelector = '#fkcart-checkout-button';
                const containerSelector = '.fkcart-checkout-wrap.fkcart-panel';

                function updateCheckoutUrl(button) {
                    if (button && button.getAttribute('href') !== targetHref) {
                        button.setAttribute('href', targetHref);
                    }
                }

                function bindEvents(button) {
                    if (button.dataset.urlEventsBound === 'true') return;

                    button.addEventListener('mouseenter', () => updateCheckoutUrl(button));
                    button.addEventListener('touchstart', () => updateCheckoutUrl(button), { passive: true });
                    button.dataset.urlEventsBound = 'true';
                }

                function checkAndBindButton() {
                    const button = document.querySelector(buttonSelector);
                    if (button) {
                        updateCheckoutUrl(button);
                        bindEvents(button);
                    }
                }

                // Esmane kontroll kohe
                checkAndBindButton();

                // Jälgime kogu dokumenti pidevalt, kuna FunnelKit võib dünaamiliselt uuendada sisu
                const observer = new MutationObserver(() => {
                    checkAndBindButton();
                });

                observer.observe(document.body, { childList: true, subtree: true });
            });


        </script>
        <?php
    }
}
add_action('wp_footer', 'custom_checkout_url_script');


function add_nf1_custom_css() {
    if ( is_page('nf1') || is_page('nf2') ) {
        echo '<style>
            .fkcart-reward-panel.fkcart-panel { display: none !important; }
        </style>';
    }
}
add_action('wp_head', 'add_nf1_custom_css');




/* END
 * Redirect FunnelKit checkout to custom URL for specific landing page
 *
 */






/* END
 * Piirab webhookide saatmist MRP-sse kui tegu on US tellimusega
 *
 */

function restrict_webhooks_for_us_shipping_and_url($deliver, $webhook, $arg) {
    // Webhooki ID-d, mida tahame piirata
    $restricted_webhooks = ['customer.deleted', 'customer.updated', 'order.updated', 'order.created'];

    // Kontrollime, kas tegemist on piiratud webhookiga
    if (in_array($webhook->get_name(), $restricted_webhooks)) {
        // Võtame webhooki Delivery URL-i
        $delivery_url = $webhook->get_delivery_url();

        // Kui URL sisaldab 'app.mrpeasy.com', siis keelame webhooki saatmise
        if (strpos($delivery_url, 'app.mrpeasy.com') !== false) {
            return false;
        }

        // Kui andmed on tellimusega seotud, siis saame tellimuse ID
        if (isset($arg[0]) && is_a($arg[0], 'WC_Order')) {
            $order = $arg[0];

            // Võtame tellimuse tarne riigi (shipping country)
            $shipping_country = $order->get_shipping_country();

            // Kui tarnemaa on US, siis keelame webhooki saatmise
            if ($shipping_country === 'US') {
                return false;
            }
        }
    }
    return $deliver;
}
#add_filter('woocommerce_webhook_should_deliver', 'restrict_webhooks_for_us_shipping_and_url', 10, 3);
// 1️⃣ Registreerime uue tellimuse staatuse "Processing US"
function register_custom_order_status() {
    register_post_status('wc-processing-us', array(
        'label'                     => 'Processing US',
        'public'                    => true,
        'show_in_admin_status_list'  => true,
        'show_in_admin_all_list'     => true,
        'label_count'                => _n_noop('Processing US (%s)', 'Processing US (%s)')
    ));
}
add_action('init', 'register_custom_order_status');

function add_custom_order_status($order_statuses) {
    $order_statuses['wc-processing-us'] = 'Processing US';
    return $order_statuses;
}
add_filter('wc_order_statuses', 'add_custom_order_status');

// 2️⃣ Määrame tellimuse staatuse vastavalt tarneriigile
function change_order_status_based_on_country($status, $order_id) {
    $order = wc_get_order($order_id);
    $shipping_country = $order->get_shipping_country(); // Saame tellimuse tarneriigi

    // Kontrollime, kas tarneriik on USA, Kanada või Austraalia
    if (in_array($shipping_country, ['US', 'CA', 'AU'])) {
        return 'wc-processing-us'; // WooCommerce nõuab "wc-" eesliidet
    }

    return 'processing'; // Kõik muud tellimused saavad "Processing" staatuse
}
#add_filter('woocommerce_payment_complete_order_status', 'change_order_status_based_on_country', 10, 2);

function trigger_emails_for_processing_us_status($order_id, $order) {
    if ($order->get_status() === 'processing-us') {
        // Käivitame "Uus tellimus" (New Order) e-kirja adminile
        // Saadame "Uus tellimus" (New Order) e-kirja adminile
        $email_new_order = WC()->mailer()->get_emails()['WC_Email_New_Order'];
        $email_new_order->trigger( $order_id );

        // Käivitame "Tellimuse töötlemine" (Processing Order) e-kirja kliendile
       #WC()->mailer()->emails['WC_Email_Customer_Processing_Order']->trigger($order_id);

        $email_processing_order = $mailer->emails['WC_Email_Customer_Processing_Order'];
        if ($email_processing_order) {
            $email_processing_order->trigger($order_id);
        }
    }
}

// Seome funktsiooni WooCommerce'i tellimuse staatuse muutumise hookiga
add_action('woocommerce_order_status_wc-processing-us', 'trigger_emails_for_processing_us_status', 10, 2);

function force_send_new_order_email($order_id, $old_status, $new_status) {
    if ($new_status === 'processing-us') {
        $order = wc_get_order($order_id);
        $mailer = WC()->mailer();

        $email_new_order = WC()->mailer()->get_emails()['WC_Email_New_Order'];
        $email_new_order->trigger( $order_id );

        // Saadame "Tellimuse töötlemine" (Processing Order) e-kirja kliendile
        $email_processing_order = $mailer->emails['WC_Email_Customer_Processing_Order'];
        if ($email_processing_order) {
            $email_processing_order->trigger($order_id);
        }
    }
}
add_action('woocommerce_order_status_changed', 'force_send_new_order_email', 10, 3);

// Lubame WooCommerce'il saata "New Order" e-kirja rohkem kui üks kord
add_filter('woocommerce_new_order_email_allows_resend', '__return_true');

function add_processing_us_to_webhook_events($events) {
    $events['woocommerce_order_status_wc-processing-us'] = __('Order Status Processing-US', 'woocommerce');
    return $events;
}
#add_filter('woocommerce_webhook_events', 'add_processing_us_to_webhook_events');



/*

// Kui tellimus jõuab "processing-us" staatusesse, käivita kõik "processing" staatuse tegevused
add_action('woocommerce_order_status_wc-processing-us', function($order_id) {
    $order = wc_get_order($order_id);

    // Käivitame samad webhookid ja tegevused nagu "processing" puhul
    do_action('woocommerce_order_status_processing', $order_id);

    // Käivitame e-kirjad
    WC()->mailer()->emails['WC_Email_Customer_Processing_Order']->trigger($order_id);
    WC()->mailer()->emails['WC_Email_New_Order']->trigger($order_id);
}, 10, 1);




function add_processing_us_to_webhooks($order_id, $old_status, $new_status) {
    if ($new_status === 'processing-us') {
        do_action('woocommerce_order_status_processing', $order_id);
        do_action('woocommerce_order_status_changed', $order_id, $old_status, 'processing');
    }
}
add_action('woocommerce_order_status_changed', 'add_processing_us_to_webhooks', 10, 3);
*/
// 3️⃣ Lisame CSS-i, et muuta "Processing US" värvi WooCommerce'i adminis
function custom_order_status_css() {
    echo '<style>
        .status-wc-processing-us mark.status-processing-us {
            background-color: #c6e1c6 !important; /* Oranž */
            color: #2c4700 !important;
        }
    </style>';
}
add_action('admin_head', 'custom_order_status_css');



?>
