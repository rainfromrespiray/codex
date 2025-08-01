
/**
 * Run Everywhere: AJAX combo / minimal-funnel page / minimal-checkout
 */

// 1) AJAX combo endpoint
add_action('wp_ajax_add_bundle_combo','respiray_add_bundle_combo');
add_action('wp_ajax_nopriv_add_bundle_combo','respiray_add_bundle_combo');
add_action('wc_ajax_add_bundle_combo','respiray_add_bundle_combo');
add_action('wc_ajax_nopriv_add_bundle_combo','respiray_add_bundle_combo');

function respiray_set_combo($c){
    WC()->cart->empty_cart();
    $c = strtolower( trim($c) );
    if ( ! preg_match('/^[wb]{1,3}$/', $c) ) {
        return false;
    }

    $pid = 35379; // parent + variation map
    $map = [
      'b' => ['id'=>35380,'pa_color'=>'black'],
      'w' => ['id'=>35381,'pa_color'=>'white'],
    ];

    foreach( str_split($c) as $ch ) {
        if ( isset($map[$ch]) ) {
            WC()->cart->add_to_cart(
              $pid,
              1,
              $map[$ch]['id'],
              ['pa_color'=>$map[$ch]['pa_color']]
            );
        }
    }

    if ( WC()->session ) {
        WC()->session->set_customer_session_cookie(true);
        WC()->session->save_data();
    }
    WC()->cart->calculate_totals();
    wc_setcookie('woocommerce_cart_hash',      WC()->cart->get_cart_hash());
    wc_setcookie('woocommerce_items_in_cart', count(WC()->cart->get_cart()));
    return true;
}

function respiray_add_bundle_combo(){
    if ( empty($_POST['combo']) ) {
        wp_send_json_error('Missing combo');
    }
    if ( respiray_set_combo($_POST['combo']) ) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Invalid combo');
    }
}

// 2) Funnel landing → custom checkout URL
add_filter('woocommerce_get_checkout_url', function($url){
    return is_page('nf-test')
        ? site_url('/checkouts/nf/')
        : $url;
});

// 3) Enqueue ONLY offer1.js on nf-test
add_action('wp_enqueue_scripts','respiray_enqueue_offer1');
function respiray_enqueue_offer1(){
    if ( is_page('nf-test') ) {
        wp_enqueue_script(
            'respiray-offer1',
            get_stylesheet_directory_uri().'/js/offer1.js',
            ['jquery'],
            '1.0',
            true
        );
    }
}

// 4) Minimal assets on nf-test & real /checkouts/nf/
add_action('wp_enqueue_scripts','respiray_minimal_assets',20);
function respiray_minimal_assets(){
    // — on nf-test: strip Woo fragments + styles + side-cart
    if ( is_page('nf-test') ){
        wp_dequeue_script('wc-cart-fragments');
        wp_dequeue_script('woocommerce');
        wp_dequeue_style('woocommerce-*');
        // FunnelKit side-cart
        wp_dequeue_script('wfob');
        wp_dequeue_script('wfob-bump-wrapper');
        wp_dequeue_script('wfacp-smart-buttons');
        wp_dequeue_script('wfacp_checkout_js');
        wp_dequeue_style('wfacp-styles'); // if your theme added one
    }

    // — continue only on /checkouts/nf/
    if ( false === strpos($_SERVER['REQUEST_URI'],'/checkouts/nf/') ) {
        return;
    }

    global $wp_styles, $wp_scripts;

    // ● KEEP these CSS handles:
$keep_styles = [
  // WooCommerce core
  'woocommerce-layout','woocommerce-smallscreen','woocommerce-general','wc-blocks-style',
  // your theme
  'normalize','html5blank','newcvi',
  // FunnelKit bump + main form
  'wfob','wfob-bump-wrapper',
  'wfacp-style','wfacp-form-default','wfacp-form',   // catch the main FunnelKit form CSS
];
// also keep anything starting with wfob- or wfacp-
foreach( $wp_styles->registered as $h => $obj ){
  if (
    in_array($h, $keep_styles, true)
    || 0 === strpos($h, 'elementor-')
    || 0 === strpos($h, 'wfob-')
    || 0 === strpos($h, 'wfacp-')
  ) {
    continue;
  }
  wp_dequeue_style($h);
}

// ── KEEP these JS handles ──────────────────────────
$keep_js = [
  // core
  'jquery','jquery-migrate','html5blankscripts',
  'woocommerce','wc-cart-fragments','wc-checkout',
  'wc-country-select','wc-address-i18n','wc-eu-vat','wc-password-strength-meter',
  // Stripe + side-buttons
  'fkwcs-express-checkout-js','fkwcs-stripe-external','fkwcs-stripe-js',
  'wfacp-intlTelInput-js','wfacp-smart-buttons','wfacp_checkout_js',
  // FunnelKit bump
  'wfob','wfob-bump-wrapper',
];
// also keep any handle starting with wfob- or wfacp-
foreach( $wp_scripts->registered as $h => $obj ){
  if (
    in_array($h, $keep_js, true)
    || 0 === strpos($h, 'elementor-')
    || 0 === strpos($h, 'wfob-')
    || 0 === strpos($h, 'wfacp-')
  ) {
    continue;
  }
  wp_dequeue_script($h);
}

    // defer only the express-checkout script so it doesn’t block
    add_filter('script_loader_tag', function($tag,$handle){
        if ( 'fkwcs-express-checkout-js' === $handle ) {
            return str_replace('<script ','<script defer ',$tag);
        }
        return $tag;
    }, 20, 2);
}

// 5) Defer Elementor/jQuery-Migrate on nf-test
add_filter('script_loader_tag', function($tag,$h){
    if ( is_page('nf-test') && in_array($h,['elementor-frontend','jquery-migrate'],true) ) {
        return str_replace(' src',' defer src',$tag);
    }
    return $tag;
},10,2);

// 6) Prefetch hints on nf-test
add_action('wp_head', function(){
    if ( is_page('nf-test') ){
        echo '<link rel="preconnect" href="'.esc_url(home_url()).'" crossorigin>'."\n";
        echo '<link rel="prefetch"   href="'.esc_url(site_url('/checkouts/nf/')).'" as="document">'."\n";
    }
});


// 7) Redis sessions for WC
add_action('woocommerce_init', function(){
    if ( class_exists('WC_Session_Handler_Cache') ) {
        add_filter('woocommerce_session_handler', fn()=> 'WC_Session_Handler_Cache');
    }
});

// 8) Disable LinkedIn pixel on landing and checkout
add_filter('script_loader_src','respiray_block_linkedin',10,2);
function respiray_block_linkedin($src,$handle){
    if (
        ( is_page('nf-test') || false !== strpos($_SERVER['REQUEST_URI'],'/checkouts/nf/') ) &&
        false !== strpos($src,'ads.linkedin.com')
    ){
        return '';
    }
    return $src;
}

// 9) Extra preconnects on nf-test
add_action('wp_head', function(){
    if ( is_page('nf-test') ){
        echo '<link rel="preconnect" href="https://static.hotjar.com" crossorigin>'."\n";
        echo '<link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>'."\n";
        echo '<link rel="preconnect" href="https://js.stripe.com" crossorigin>'."\n";
    }
});

// 10) Server-side warm-up of the real checkout
add_action('template_redirect', function(){
    if ( is_page('nf-test') ) {
        wp_remote_get(
            site_url('/checkouts/nf/'),
            [
                'blocking' => false,
                'timeout'  => 0.1,
            ]
        );
    }
    // Handle direct "combo" parameter to skip AJAX roundtrip
    if (
        false !== strpos($_SERVER['REQUEST_URI'], '/checkouts/nf/') &&
        ! empty($_GET['combo'])
    ){
        respiray_set_combo( sanitize_text_field($_GET['combo']) );
        wp_safe_redirect( remove_query_arg('combo') );
        exit;
    }
});

// 11) Preload critical fonts and Stripe script
add_action('wp_head', function(){
    if (
        is_page('nf-test') ||
        false !== strpos($_SERVER['REQUEST_URI'],'/checkouts/nf/')
    ){
        $theme = get_stylesheet_directory_uri();
        echo '<link rel="preload" href="'.esc_url($theme.'/fonts/OpenSans-Regular.woff2').'" as="font" type="font/woff2" crossorigin>'."\n"; 
        echo '<link rel="preload" href="'.esc_url($theme.'/fonts/OpenSans-Medium.woff2').'" as="font" type="font/woff2" crossorigin>'."\n"; 
        echo '<link rel="preload" href="'.esc_url($theme.'/fonts/Retail_Text-Medium.woff2').'" as="font" type="font/woff2" crossorigin>'."\n"; 
        echo '<link rel="preload" href="'.esc_url(site_url('/wp-content/plugins/elementor/assets/lib/font-awesome/webfonts/fa-solid-900.woff2')).'" as="font" type="font/woff2" crossorigin>'."\n"; 
        echo '<link rel="preload" href="'.esc_url(site_url('/wp-content/plugins/elementor/assets/lib/eicons/fonts/eicons.woff2')).'" as="font" type="font/woff2" crossorigin>'."\n"; 
        echo '<link rel="preload" href="https://js.stripe.com/v3/" as="script" crossorigin>'."\n"; 
    }
});

// 12) Defer non-critical tracking scripts
add_filter('script_loader_tag','respiray_defer_third_party',10,3);
function respiray_defer_third_party($tag,$handle,$src){
    $hosts = [
        'analytics.tiktok.com',
        'connect.facebook.net',
        'cdn.taboola.com','psb.taboola.com','trc.taboola.com','trc-events.taboola.com',
        'static.hotjar.com','script.hotjar.com','content.hotjar.io',
        'api.goaffpro.com',
        'www.googletagmanager.com','region1.google-analytics.com','stats.g.doubleclick.net','pagead2.googlesyndication.com',
        'stats.wp.com','pixel.wp.com','static.cloudflareinsights.com',
        'snap.licdn.com','unpkg.com','pay.google.com'
    ];
    foreach($hosts as $h){
        if ( false !== strpos($src,$h) ) {
            return str_replace('<script ','<script defer ',$tag);
        }
    }
    return $tag;
}
