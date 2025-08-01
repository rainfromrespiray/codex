Project README – Checkout Speed Optimization
Read this file before making any code changes. Re-read before every update.
If any part of this file is unclear, ask for clarification before proceeding.

🎯 GENERAL EXPECTATION
The goal is to produce bulletproof, robust code that reorganizes the loading order from the moment a user clicks “Claim now” on the landing page to when the checkout page (/checkouts/nf) is visually loaded above the fold.

Key focus:

Reduce or eliminate all possible render-blocking assets and any assets that delay either:

The redirect to the checkout page, or

The loading of above-the-fold items on the checkout page (including the mini cart and upsells).

When editing snippet1.php:

Ensure all required assets are loaded correctly, in the optimal order.

It is your responsibility to deeply research and verify all dependencies and asset requirements, so that your changes do not break any functionality or UI.

This is why you must carefully read and understand all referenced files (see list below) before each change. This includes reviewing all logic, scripts, and asset calls related to the checkout flow and cart.

🔒 CRITICAL RULES
No Visible Layout Changes
Any code update must not result in visible layout or UI/UX changes on the checkout page at
respiray.com/checkouts/nf.

Mini Cart Accuracy
The FunnelKit mini cart on the checkout page must always show the correct items (selected color, quantity, price) based on the customer’s selection at
respiray.com/nf-test/.

This includes any offer or upsell items added to the cart.

🌐 WEBSITE LOGIC OVERVIEW
Landing Page: respiray.com/nf-test/

Features a buy box with three offers:

Offer 1: 1 product, 2 color options (black, white)

Offer 2: 2 products, 2 color options (black, white)

Offer 3: 3 products, 2 color options (black, white)

Each offer has its own “Claim now” button

Code for each button:

claim now1.html, claim now2.html, claim now3.html

Current Focus:
Optimize Offer 1 flow first. Once Offer 1 is working correctly and fast, extend to Offers 2 and 3.

Redirection:
Clicking any "Claim now" button must redirect to the custom checkout at respiray.com/checkouts/nf/ without opening the Funnelkit side cart.

Global (default) WooCommerce checkout must NOT be used.

Checkout Page:
Custom checkout source is provided as checkout_source.html.

JS Logic:

All Offer 1 cart and redirect logic is handled in Offer1.js.

There is likely JavaScript and AJAX involved in the cart handoff between /nf-test and /checkouts/nf.

Upsells:
FunnelKit checkout upsells also affect the mini cart and are the last items to appear above the fold on the checkout page.

Performance optimizations must not break upsell logic.

🚀 PERFORMANCE GOAL
Current: From "Claim now" click (Offer 1) to above-the-fold load on /checkouts/nf is ~10 seconds.

Target: 3–4 seconds max to above-the-fold visible content on /checkouts/nf—including mini cart and upsells.

🔧 OPTIMIZATION SCOPE & FILES
Main file to optimize:

snippet1.php (this is used via a WordPress snippets plugin; do not add a <?php tag)

Other files for reference and context:

claim now1.html / claim now2.html / claim now3.html – buy button markup and triggers

Offer1.js – cart logic for Offer 1 (including AJAX/cart handoff)

checkout_source.html – snapshot of /checkouts/nf page

functions.php – for any hooks or custom logic relevant to cart/session/checkout

Latest HAR file (e.g. respiray.com_nf-test5) – for performance review

You may suggest improvements in other files if you find opportunities.
All testing and optimizations must be performed on the live site.

🛑 CACHING & PLUGINS
The site uses WP Rocket, Cloudflare CDN, and WPX XDN.

The checkout at /checkouts/nf is a FunnelKit-based custom page built with Elementor.

Important:
Caching the checkout page is not recommended and may break cart/upsell logic.
Any optimizations must not rely on caching the checkout page.

🧾 TESTING, HAR FILES, & ANALYSIS
Testing must be done live; there is no staging environment.

Each optimization attempt is evaluated using a HAR file (e.g. respiray.com_nf-test5):

HAR is started on "Claim now" (Offer 1) click, ends once all above-the-fold content (including upsells/mini cart) is visible on /checkouts/nf.

Use the highest numbered HAR file for the latest results.

Request count may vary due to manual HAR ending—use HAR for comparative analysis.

Analyze all relevant files and the latest HAR before making any changes.

📋 WORKFLOW (FOR EVERY ATTEMPT)
Re-read this README in full.

Review and understand all listed files, especially the latest HAR file and snippet1.php.

Implement optimization in snippet1.php (unless another file is explicitly targeted).

Never break the mini cart or cause visual/layout changes.

Test, document, and submit changes with clear reasoning and what was tried/changed.

Await new HAR upload and iterate as needed.

⚠️ IMPORTANT NOTES
If an approach (e.g., preloading checkout with a fetch) improves speed but breaks mini cart/upsell logic or layout, it must not be used.

Any change that improves speed but breaks cart logic is unacceptable.

If in doubt, ask for clarification before proceeding.

📁 FILE SUMMARY
File	Purpose
claim now1.html	HTML for Offer 1 "Claim now" button
claim now2.html	HTML for Offer 2 "Claim now" button
claim now3.html	HTML for Offer 3 "Claim now" button
Offer1.js	JS logic for Offer 1 cart/redirect
checkout_source.html	Custom checkout source snapshot
snippet1.php	Main file to optimize for speed
functions.php	Reference for hooks and WP logic
respiray.com_nf-testN	Latest HAR file for performance comparison

💡 CLARIFICATIONS
Mini Cart and Upsell Logic:
AJAX and JavaScript (FunnelKit/Offer1.js) are involved. Keep all logic working.

Checkout Caching:
Never cache /checkouts/nf—doing so breaks dynamic cart/upsell logic.

For any uncertainty about requirements, file purpose, or optimization safety, seek clarification before proceeding.
Adherence to this README is mandatory.
