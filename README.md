Before any task is started readme needs to be read and before updating any code these rules have to apply:
1. There cannot be any visible layout changes on the checkout page located at respiray.com/checkouts/nf - this needs to remain 100% as it currently is.
2. Funnelkit mini cart on the checkout page has to show the correct items selected on respiray.com/nf-test/ (including color, quantity and price).


The website logic description:
respiray.com/nf-test/ is a landing page that features a buy box with 3 offers: Offer 1 features 1 product with 2 color options (black and white). Offer 2 features 2 product with 2 color options (black and white). Offer 3 features 3 product with 2 color options (black and white).
Each offer has "Claim now" button. I have uploaded the Claim now button codes as:
claim now1.html
claim now2.html
claim now3.html

Currently we are working to get Offer 1 to work smoothly. Once this task is achived we continue working with Offer 2 and Offer 3 to work in a similar manner. However, have to keep in mind that later all offers need to be able to work depending on the customer's choice on the nf-test page.
In order for Offer 1 to work, we have created: 
Offer1.js

If customer clicks on the "Claim now" button they will be redirected to checkout page respiray.com/checkouts/nf

