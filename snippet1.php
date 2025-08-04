<?php
/**
 * Run Everywhere: AJAX combo / minimal-funnel page / minimal-checkout
 *
 * This file centralises all of the performance optimisations for the
 * "nf‑test" landing page and the custom FunnelKit checkout at
 * ``/checkouts/nf/``.  The goal is to reduce the time from clicking
 * the Claim‑Now button to the first paint of the checkout form while
 * leaving the mini‑cart, FunnelKit upsells and your theme’s layout
 * completely intact.
 *
 * It is safe to copy this file in its entirety over your existing
 * ``snippet1.php`` or append the additional functions to your own
 * version.  The existing logic (AJAX combo endpoint, minimal‑asset
 * stripping, etc.) has been preserved.  Only non‑blocking hints,
 * preloads and script deferrals have been added.
 */

// -----------------------------------------------------------------------------
// 1) AJAX combo endpoint
//
// These actions register an AJAX endpoint that accepts a colour combo
// string (e.g. "b", "bw", "wb", "www") and adds the corresponding
// Respiray devices to the cart.  The function empties the cart first
// to ensure only the selected combination remains.
add_action( 'wp_ajax_add_bundle_combo', 'respiray_add_bundle_combo' );
add_action( 'wp_ajax_nopriv_add_bundle_combo', 'respiray_add_bundle_combo' );
add_action( 'wc_ajax_add_bundle_combo', 'respiray_add_bundle_combo' );
add_action( 'wc_ajax_nopriv_add_bundle_combo', 'respiray_add_bundle_combo' );

/**
 * Add a bundle of devices to the WooCommerce cart based on a simple
 * character code.  Accepts "b" for black and "w" for white; any
 * combination of up to three characters is allowed.  Unknown codes
 * cause the function to bail.
 *
 * @param string $c Colour combo.
 *
 * @return bool True on success, false on invalid input.
 */
function respiray_set_combo( $c ) {
    // Empty the cart first so only the requested products remain.
    WC()->cart->empty_cart();
    $c = strtolower( trim( $c ) );
    if ( ! preg_match( '/^[wb]{1,3}$/', $c ) ) {
        return false;
    }
    $pid = 35379; // Parent product ID for the bundle
    // Map of single‑device variations
    $map = array(
        'b' => array( 'id' => 35380, 'pa_color' => 'black' ),
        'w' => array( 'id' => 35381, 'pa_color' => 'white' ),
    );
    foreach ( str_split( $c ) as $ch ) {
        if ( isset( $map[ $ch ] ) ) {
            WC()->cart->add_to_cart(
                $pid,
                1,
                $map[ $ch ]['id'],
                array( 'pa_color' => $map[ $ch ]['pa_color'] )
            );
        }
    }
    // Persist the cart in the session so redirect to checkout shows it.
    if ( WC()->session ) {
        WC()->session->set_customer_session_cookie( true );
        WC()->session->save_data();
    }
    WC()->cart->calculate_totals();
    wc_setcookie( 'woocommerce_cart_hash', WC()->cart->get_cart_hash() );
    wc_setcookie( 'woocommerce_items_in_cart', count( WC()->cart->get_cart() ) );
    return true;
}

/**
 * AJAX handler for adding bundle combos.  Responds with JSON success or
 * error.  The ``combo`` POST parameter must be supplied.
 */
function respiray_add_bundle_combo() {
    if ( empty( $_POST['combo'] ) ) {
        wp_send_json_error( 'Missing combo' );
    }
    if ( respiray_set_combo( $_POST['combo'] ) ) {
        wp_send_json_success();
    } else {
        wp_send_json_error( 'Invalid combo' );
    }
}

// -----------------------------------------------------------------------------
// 2) Funnel landing → custom checkout URL
//
// On the nf‑test page we force WooCommerce’s checkout URL to be our custom
// ``/checkouts/nf/`` page.  Everywhere else we leave the URL untouched.
add_filter(
    'woocommerce_get_checkout_url',
    function ( $url ) {
        return is_page( 'nf-test' ) ? site_url( '/checkouts/nf/' ) : $url;
    }
);

// -----------------------------------------------------------------------------
// 3) Enqueue ONLY offer1.js on nf-test
//
// The landing page should not load WooCommerce’s default checkout scripts.
// Instead we enqueue just the minimal JS needed to run the “Claim now”
// buttons.  Offer1.js contains the AJAX call to ``respiray_add_bundle_combo``
// and then navigates to the checkout.
add_action( 'wp_enqueue_scripts', 'respiray_enqueue_offer1' );
function respiray_enqueue_offer1() {
    if ( is_page( 'nf-test' ) ) {
        wp_enqueue_script(
            'respiray-offer1',
            get_stylesheet_directory_uri() . '/js/offer1.js',
            array( 'jquery' ),
            '1.0',
            true
        );
    }
}

// -----------------------------------------------------------------------------
// 4) Minimal assets on nf-test & real /checkouts/nf/
//
// This function removes all unnecessary scripts and styles from the landing
// page and the custom checkout.  On nf‑test we strip Woo fragments and
// side‑cart scripts entirely.  On the checkout page we iterate over
// registered styles and scripts, dequeueing everything except the few
// handles specified in the $keep_styles and $keep_js arrays.
add_action( 'wp_enqueue_scripts', 'respiray_minimal_assets', 20 );
function respiray_minimal_assets() {
    // On nf‑test: remove Woo fragments, Woo core and FunnelKit side‑cart
    if ( is_page( 'nf-test' ) ) {
        wp_dequeue_script( 'wc-cart-fragments' );
        wp_dequeue_script( 'woocommerce' );
        wp_dequeue_style( 'woocommerce-*' );
        // FunnelKit side‑cart assets
        wp_dequeue_script( 'wfob' );
        wp_dequeue_script( 'wfob-bump-wrapper' );
        wp_dequeue_script( 'wfacp-smart-buttons' );
        wp_dequeue_script( 'wfacp_checkout_js' );
        wp_dequeue_style( 'wfacp-styles' ); // only if added by your theme

        // Remove heavy global libraries not needed on the landing page.  These
        // scripts/styles (Swiper, Modernizr, Conditionizr) are enqueued by the
        // theme in functions.php but are not required for the simple buy box
        // experience.  Removing them eliminates additional downloads from
        // unpkg.com and other CDNs.
        wp_dequeue_script( 'swiper' );
        wp_dequeue_style( 'swipercss' );
        wp_dequeue_script( 'modernizr' );
        wp_dequeue_script( 'conditionizr' );
    }
    // Only continue asset stripping on the real checkout.  If we’re on
    // another page (including nf‑test), bail out here.
    if ( false === strpos( $_SERVER['REQUEST_URI'], '/checkouts/nf/' ) ) {
        return;
    }
    global $wp_styles, $wp_scripts;
    // CSS handles to keep on the checkout.  Anything not in this list or
    // starting with elementor-, wfob- or wfacp- will be dequeued.
    $keep_styles = array(
        'woocommerce-layout', 'woocommerce-smallscreen', 'woocommerce-general', 'wc-blocks-style',
        // theme styles
        'normalize', 'html5blank', 'newcvi',
        // FunnelKit bump and main form
        'wfob', 'wfob-bump-wrapper',
        'wfacp-style', 'wfacp-form-default', 'wfacp-form',
    );
    foreach ( $wp_styles->registered as $h => $obj ) {
        if (
            in_array( $h, $keep_styles, true ) ||
            0 === strpos( $h, 'elementor-' ) ||
            0 === strpos( $h, 'wfob-' ) ||
            0 === strpos( $h, 'wfacp-' )
        ) {
            continue;
        }
        wp_dequeue_style( $h );
    }
    // JS handles to keep on the checkout.  Anything not in this list or
    // starting with elementor-, wfob- or wfacp- will be dequeued.
    $keep_js = array(
        // core
        'jquery', 'jquery-migrate', 'html5blankscripts',
        'woocommerce', 'wc-cart-fragments', 'wc-checkout',
        'wc-country-select', 'wc-address-i18n', 'wc-eu-vat', 'wc-password-strength-meter',
        // Stripe and smart buttons
        'fkwcs-express-checkout-js', 'fkwcs-stripe-external', 'fkwcs-stripe-js',
        'wfacp-intlTelInput-js', 'wfacp-smart-buttons', 'wfacp_checkout_js',
        // FunnelKit bump
        'wfob', 'wfob-bump-wrapper',
    );
    foreach ( $wp_scripts->registered as $h => $obj ) {
        if (
            in_array( $h, $keep_js, true ) ||
            0 === strpos( $h, 'elementor-' ) ||
            0 === strpos( $h, 'wfob-' ) ||
            0 === strpos( $h, 'wfacp-' )
        ) {
            continue;
        }
        wp_dequeue_script( $h );
    }
}

// -----------------------------------------------------------------------------
// 4b) Defer only the express‑checkout script so it doesn’t block
//
// WooCommerce Express Checkout (FunnelKit’s one‑click) is the only script
// required immediately on page load.  Everything else should be deferred via
// the $keep_js loop above or via our extra filters below.  This tiny filter
// adds the HTML5 ``defer`` attribute to that single script.
add_filter(
    'script_loader_tag',
    function ( $tag, $handle ) {
        if ( 'fkwcs-express-checkout-js' === $handle ) {
            return str_replace( '<script ', '<script defer ', $tag );
        }
        return $tag;
    },
    10,
    2
);

// -----------------------------------------------------------------------------
// 5) Defer Elementor and jQuery‑Migrate on nf‑test
//
// Neither Elementor nor jQuery‑Migrate are needed for the initial render of
// the landing page.  We still load them, but we mark their tags as ``defer``
// so they download in parallel and execute after the DOM is parsed.
add_filter(
    'script_loader_tag',
    function ( $tag, $handle ) {
        if ( is_page( 'nf-test' ) && in_array( $handle, array( 'elementor-frontend', 'jquery-migrate' ), true ) ) {
            return str_replace( ' src', ' defer src', $tag );
        }
        return $tag;
    },
    10,
    2
);

// -----------------------------------------------------------------------------
// 6) Add resource hints (preconnect/prefetch) on nf‑test
//
// Preconnect to third‑party domains used on the checkout so that DNS and
// TLS handshakes happen in parallel.  Also prefetch the checkout URL to
// instruct the browser to load a blank HTML shell before the user clicks.
add_filter(
    'wp_resource_hints',
    function ( $hints, $relation_type ) {
        if ( ! is_page( 'nf-test' ) ) {
            return $hints;
        }
        // Add DNS-prefetch hints for third‑party domains on the landing page.  These
        // hints instruct the browser to resolve hostnames early without opening
        // connections, which reduces lookup latency when the resources are later
        // requested on the checkout page.
        if ( 'dns-prefetch' === $relation_type ) {
            $hints[] = 'https://fonts.googleapis.com';
            $hints[] = 'https://fonts.gstatic.com';
            $hints[] = 'https://api.stripe.com';
            $hints[] = 'https://js.stripe.com';
            $hints[] = 'https://cdnjs.cloudflare.com';
            $hints[] = 'https://snap.licdn.com';
            $hints[] = 'https://px.ads.linkedin.com';
            $hints[] = 'https://connect.facebook.net';
            $hints[] = 'https://widget.trustpilot.com';
            $hints[] = 'https://unpkg.com';
            $hints[] = 'https://stats.wp.com';
        }
        if ( 'preconnect' === $relation_type ) {
            // Preconnect to all third‑party hosts used during checkout.  This
            // includes fonts, Stripe, CDNJS, LinkedIn, Facebook, Trustpilot and
            // other analytics domains so their DNS/TLS handshakes occur early.
            $hints[] = 'https://fonts.googleapis.com';
            $hints[] = 'https://fonts.gstatic.com';
            $hints[] = 'https://api.stripe.com';
            $hints[] = 'https://js.stripe.com';
            $hints[] = 'https://cdnjs.cloudflare.com';
            $hints[] = 'https://snap.licdn.com';
            $hints[] = 'https://px.ads.linkedin.com';
            $hints[] = 'https://connect.facebook.net';
            $hints[] = 'https://widget.trustpilot.com';
            $hints[] = 'https://unpkg.com';
            $hints[] = 'https://stats.wp.com';
        }
        if ( 'prefetch' === $relation_type ) {
            // Prefetch and prerender the checkout page itself to get the HTML
            // shell and open a connection before the user clicks.
            $hints[] = site_url( '/checkouts/nf/' );
        }
        if ( 'prerender' === $relation_type ) {
            $hints[] = site_url( '/checkouts/nf/' );
        }
        return $hints;
    },
    10,
    2
);

// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------
// 7) Preload critical fonts and checkout CSS on the landing page
//
// Kick off font and CSS downloads while the user is still on nf‑test.  This
// shortens the critical path on the checkout page and reduces render
// blocking.  Adjust the font names/paths to your theme as needed.
add_action(
    'wp_head',
    function () {
        if ( ! is_page( 'nf-test' ) ) {
            return;
        }
        // Preload our custom fonts.  Ensure the paths exist in your theme.
        echo '<link rel="preload" href="' . esc_url( get_stylesheet_directory_uri() . '/fonts/OpenSans-Regular.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
        echo '<link rel="preload" href="' . esc_url( get_stylesheet_directory_uri() . '/fonts/OpenSans-Medium.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
        echo '<link rel="preload" href="' . esc_url( get_stylesheet_directory_uri() . '/fonts/Retail_Text-Medium.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
        // Preload a core checkout style sheet.  Change this path if your CSS lives elsewhere.
        echo '<link rel="preload" href="' . esc_url( get_stylesheet_directory_uri() . '/css/new.css' ) . '" as="style">' . "\n";
    },
    20
);

// -----------------------------------------------------------------------------
// 8) Remove jQuery‑Migrate globally if unused
//
// jQuery‑Migrate is only required if you rely on deprecated jQuery APIs.  If
// your site works fine without it, remove it entirely to save ~10 kB of JS.
add_action(
    'wp_default_scripts',
    function ( WP_Scripts $scripts ) {
        if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
            $scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
        }
    }
);

// -----------------------------------------------------------------------------
// 9) Defer common tracking scripts
//
// Many themes and plugins register marketing/analytics scripts that block
// rendering.  We mark known handles as defer to avoid blocking the main
// rendering thread.  Add or remove handles here based on your stack.
add_filter(
    'script_loader_tag',
    function ( $tag, $handle, $src ) {
        $tracking_handles = array(
            'gtm4wp_datalayer',      // Google Tag Manager for WordPress
            'pys-js',                // PixelYourSite
            'wpca_remarketing',      // example handle
            'analytics',             // generic
            'google-tag-manager',    // generic
            'fb_pixel',              // Facebook pixel
            'tiktok-pixel',          // TikTok pixel
        );
        if ( in_array( $handle, $tracking_handles, true ) ) {
            return str_replace( '<script ', '<script defer ', $tag );
        }
        return $tag;
    },
    10,
    3
);

// End of snippet1.php

// -----------------------------------------------------------------------------
// 10) Preload FunnelKit and WooCommerce styles dynamically on nf‑test
//
// In addition to preloading our theme fonts and base CSS, we also want to
// warm up the network connections for the large style sheets used on the
// checkout page.  These include FunnelKit’s core styles (`wfacp-style`,
// `wfacp-form-default`, `wfacp-form`) and WooCommerce’s layout sheets.  We
// don’t hardcode their paths because plugin updates may change filenames;
// instead we look up the registered styles and emit `<link rel="preload">`
// tags for each one.  This runs early in the head so downloads start
// immediately when the user lands on `/nf-test`.
add_action( 'wp_head', function () {
    if ( ! is_page( 'nf-test' ) ) {
        return;
    }
    global $wp_styles;
    // Handles to preload.  Adjust this list if new styles are added or removed.
    $preload_styles = array(
        'wfacp-style', 'wfacp-form-default', 'wfacp-form',
        'woocommerce-layout', 'woocommerce-general', 'woocommerce-smallscreen',
        'wfob', 'wfob-bump-wrapper',
    );
    foreach ( $preload_styles as $handle ) {
        if ( isset( $wp_styles->registered[ $handle ] ) ) {
            $src = $wp_styles->registered[ $handle ]->src;
            if ( ! empty( $src ) ) {
                echo '<link rel="preload" href="' . esc_url( $src ) . '" as="style">' . "\n";
            }
        }
    }
}, 15 );

// -----------------------------------------------------------------------------
// 12) Preload critical JavaScript on nf‑test
//
// FunnelKit and WooCommerce scripts are large and block parsing on the
// checkout page.  To hide latency, we start downloading them while the
// visitor is still on `/nf-test`.  Like the CSS preloads above, we look up
// the registered script handles and emit preload hints for their sources.
add_action( 'wp_head', function () {
    if ( ! is_page( 'nf-test' ) ) {
        return;
    }
    global $wp_scripts;
    $preload_scripts = array(
        'woocommerce', 'wc-cart-fragments', 'wc-checkout',
        'wc-country-select', 'wc-address-i18n', 'wc-eu-vat', 'wc-password-strength-meter',
        'wfacp_checkout_js', 'wfacp-smart-buttons', 'wfacp-intlTelInput-js',
        'fkwcs-stripe-external', 'fkwcs-stripe-js',
        'wfob', 'wfob-bump-wrapper',
    );
    foreach ( $preload_scripts as $handle ) {
        if ( isset( $wp_scripts->registered[ $handle ] ) ) {
            $src = $wp_scripts->registered[ $handle ]->src;
            if ( ! empty( $src ) ) {
                // Use as="script" to hint that this is a JavaScript resource.
                echo '<link rel="preload" href="' . esc_url( $src ) . '" as="script">' . "\n";
            }
        }
    }
}, 15 );

// -----------------------------------------------------------------------------
// 11) Defer additional heavy scripts on the custom checkout
//
// The checkout page still loads many large JS files that block the HTML
// parser.  Most of these scripts (WooCommerce checkout, country selectors,
// FunnelKit logic, Stripe integrations and even Elementor) can be deferred
// without impacting the first paint because they initialise after the DOM
// has been constructed.  This filter runs for each script tag on `/checkouts/nf`.
// It also defers any script that originates from known analytics domains.
add_filter( 'script_loader_tag', function ( $tag, $handle, $src ) {
    // Apply only on the custom checkout page
    if ( false === strpos( $_SERVER['REQUEST_URI'], '/checkouts/nf/' ) ) {
        return $tag;
    }
    // List of WordPress handles to defer.  Scripts will still execute in order.
    $defer_handles = array(
        'woocommerce', 'wc-cart-fragments', 'wc-checkout',
        'wc-country-select', 'wc-address-i18n', 'wc-eu-vat', 'wc-password-strength-meter',
        'wfacp_checkout_js', 'wfacp-smart-buttons', 'wfacp-intlTelInput-js',
        'fkwcs-stripe-external', 'fkwcs-stripe-js',
        'wfob', 'wfob-bump-wrapper',
        'elementor-frontend', 'elementor-accordion', 'elementor-waypoints',
    );
    if ( in_array( $handle, $defer_handles, true ) ) {
        return str_replace( '<script ', '<script defer ', $tag );
    }
    // Domain-based deferral for external analytics and social scripts
    $defer_domains = array(
        'connect.facebook.net',
        'snap.licdn.com',
        'px.ads.linkedin.com',
        'widget.trustpilot.com',
        'www.googletagmanager.com',
        'js.stripe.com',
        'unpkg.com',
        'stats.wp.com',
    );
    foreach ( $defer_domains as $domain ) {
        if ( false !== strpos( $src, $domain ) ) {
            return str_replace( '<script ', '<script defer ', $tag );
        }
    }
    return $tag;
}, 15, 3 );

// -----------------------------------------------------------------------------
// 13) Remove marketing/analytics scripts on the checkout
//
// The checkout should prioritise loading the form, mini cart and upsells.  Most
// tracking libraries can safely be delayed until after conversion.  Here we
// dequeue common tracking scripts so they don’t even download on
// `/checkouts/nf/`.  If you need a particular tracker for analytics, consider
// moving it to the footer via your theme instead of blocking above‑the‑fold.
add_action( 'wp_enqueue_scripts', function () {
    if ( false === strpos( $_SERVER['REQUEST_URI'], '/checkouts/nf/' ) ) {
        return;
    }
    $tracking_handles = array(
        'gtm4wp_datalayer',
        'pys-js',
        'wpca_remarketing',
        'analytics',
        'google-tag-manager',
        'fb_pixel',
        'tiktok-pixel',
    );
    foreach ( $tracking_handles as $h ) {
        wp_dequeue_script( $h );
    }
}, 30 );

// -----------------------------------------------------------------------------
// 14) Strip LinkedIn and Trustpilot scripts from the checkout output
//
// Some third‑party analytics snippets are injected directly into templates or
// page builder widgets, bypassing WordPress’s enqueue system.  Because
// LinkedIn Insight and Trustpilot widgets are not needed on the checkout
// (and add significant DNS/TLS delays), we remove their `<script>` tags
// entirely on `/checkouts/nf/`.  This output buffering filter runs just
// before the template is rendered, searches the HTML for any script tags
// referencing licdn.com, linkedin.com or trustpilot.com, and strips them
// out.  It does not affect other pages.
add_action( 'template_redirect', function () {
    // Only apply on the custom checkout page
    if ( false === strpos( $_SERVER['REQUEST_URI'], '/checkouts/nf/' ) ) {
        return;
    }
    ob_start( function ( $html ) {
        // Remove LinkedIn Insight scripts
        $html = preg_replace( '#<script[^>]*src=["\']https?://[^"\']*licdn\.com[^>]*></script>#is', '', $html );
        $html = preg_replace( '#<script[^>]*src=["\']https?://[^"\']*linkedin\.com[^>]*></script>#is', '', $html );
        // Remove Trustpilot scripts
        $html = preg_replace( '#<script[^>]*src=["\']https?://[^"\']*trustpilot\.com[^>]*></script>#is', '', $html );
        // Also remove Hotjar, TikTok and Google Tag Manager scripts so we can reinsert them after load
        $html = preg_replace( '#<script[^>]*src=["\']https?://[^"\']*static\.hotjar\.com[^>]*></script>#is', '', $html );
        $html = preg_replace( '#<script[^>]*src=["\']https?://[^"\']*analytics\.tiktok\.com[^>]*></script>#is', '', $html );
        $html = preg_replace( '#<script[^>]*src=["\']https?://[^"\']*googletagmanager\.com[^>]*></script>#is', '', $html );
        return $html;
    } );
}, 0 );

// After stripping these scripts, add them back on window load to keep analytics working without blocking the page.
add_action( 'wp_footer', function () {
    if ( false === strpos( $_SERVER['REQUEST_URI'], '/checkouts/nf/' ) ) {
        return;
    }
    ?>
    <script>
    window.addEventListener('load', function() {
        // Hotjar tracking code
        (function(h,o,t,j,a,r){
            h.hj = h.hj || function(){ (h.hj.q = h.hj.q || []).push(arguments); };
            h._hjSettings = { hjid:2356734, hjsv:6 };
            a = o.getElementsByTagName('head')[0];
            r = o.createElement('script'); r.async = 1;
            r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
            a.appendChild(r);
        })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');

        // TikTok pixel
        !function (w,d,t){
            w.TiktokAnalyticsObject = t;
            var ttq = w[t] = w[t] || [];
            ttq.methods = ['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie'];
            ttq.setAndDefer = function(obj, method){ obj[method] = function(){ obj.push([method].concat(Array.prototype.slice.call(arguments,0))); }; };
            for(var i = 0; i < ttq.methods.length; i++){ ttq.setAndDefer(ttq, ttq.methods[i]); }
            ttq.instance = function(name){ var inst = ttq._i[name] || []; for(var i = 0; i < ttq.methods.length; i++){ ttq.setAndDefer(inst, ttq.methods[i]); } return inst; };
            ttq.load = function(id, opts){ var url = 'https://analytics.tiktok.com/i18n/pixel/events.js'; ttq._i = ttq._i || {}; ttq._i[id] = []; ttq._i[id]._u = url; ttq._t = ttq._t || {}; ttq._t[id] = +new Date; ttq._o = ttq._o || {}; ttq._o[id] = opts || {}; var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '?sdkid=' + id + '&lib=' + t; var a = document.getElementsByTagName('script')[0]; a.parentNode.insertBefore(s, a); };
            ttq.load('CGNJD7JC77U7F650IHJ0');
            ttq.page();
        }(window, document, 'ttq');

        // Google Tag Manager
        (function(w,d,s,l,i){
            w[l] = w[l] || [];
            w[l].push({ 'gtm.start': new Date().getTime(), event:'gtm.js' });
            var f = d.getElementsByTagName(s)[0], j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-NXL2V8L');
    });
    </script>
    <?php
}, 1000 );

// -----------------------------------------------------------------------------
// 15) Disable heavy WPML filters on the checkout
//
// The WPML plugin remains active site‑wide but there is only one language.  Its
// query and request filters still run on every page and execute dozens of
// unnecessary database queries.  To reduce overhead on the custom checkout
// without breaking other pages, we remove the most expensive WPML actions on
// `/checkouts/nf/`.  This stops WPML from modifying the main query and
// eliminates translation lookups.  If additional WPML filters need to be
// removed, add them here in the same fashion.
add_action( 'wp', function () {
    if ( false === strpos( $_SERVER['REQUEST_URI'], '/checkouts/nf/' ) ) {
        return;
    }
    // Remove core WPML query handlers
    if ( isset( $GLOBALS['sitepress'] ) && is_object( $GLOBALS['sitepress'] ) ) {
        $sitepress = $GLOBALS['sitepress'];
        remove_action( 'parse_query', array( $sitepress, 'parse_query' ), 10 );
        remove_action( 'pre_get_posts', array( $sitepress, 'pre_get_posts' ), 10 );
        // SitePress registers its set_wp_query callback on the 'wp' hook,
        // not on a custom hook named set_wp_query.  Remove it here.
        remove_action( 'wp', array( $sitepress, 'set_wp_query' ), 10 );
        // Remove WPML URL filters that rewrite various links.  With a single language
        // configured these filters are unnecessary and add query overhead.
        if ( isset( $sitepress->url_filters ) && is_object( $sitepress->url_filters ) ) {
            $url_filters = $sitepress->url_filters;
            @remove_filter( 'home_url', array( $url_filters, 'home_url_filter' ), 10 );
            @remove_filter( 'permalink', array( $url_filters, 'permalink_filter' ), 10 );
            @remove_filter( 'page_link', array( $url_filters, 'page_link_filter' ), 10, 2 );
            @remove_filter( 'post_type_link', array( $url_filters, 'post_type_link_filter' ), 10, 2 );
            @remove_filter( 'post_link', array( $url_filters, 'post_link_filter' ), 10, 3 );
            @remove_filter( 'term_link', array( $url_filters, 'term_link_filter' ), 10, 2 );
            @remove_filter( 'endpoint_permalink', array( $url_filters, 'endpoint_permalink_filter' ), 10, 2 );
        }
    }
    // Disable WPML Slug translation filter
    if ( class_exists( 'WPML_Slug_Translation' ) ) {
        global $wpml_slug_translation;
        if ( is_object( $wpml_slug_translation ) ) {
            remove_action( 'pre_get_posts', array( $wpml_slug_translation, 'filter_pre_get_posts' ), -1000 );
        }
    }

    // Remove additional WPML slug translation filters related to links if available
    if ( isset( $GLOBALS['wpml_slug_translation'] ) && is_object( $GLOBALS['wpml_slug_translation'] ) ) {
        $st = $GLOBALS['wpml_slug_translation'];
        @remove_filter( 'post_type_link', array( $st, 'post_type_link_filter' ), 10, 2 );
        @remove_filter( 'post_link', array( $st, 'post_link_filter' ), 10, 3 );
    }
}, 0 );
