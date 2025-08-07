jQuery(function($){
  var singleColor = 'black';
  
  // ─── FUNCTION DEFINITION ───
  function updateSingleImage(){
    $('.single_pack_img').hide();
    $('#single_' + singleColor).show();
  }
  
  // ─── CALL IMMEDIATELY (fixes positioning issue) ───
  updateSingleImage();
  
  // ─── SET UP UI STATE ───
  $('#sp_black, #sp_white').removeClass('sp-selected');
  $('#sp_black').addClass('sp-selected');
  
  // ─── SWATCH CLICK HANDLERS ───
  $('#sp_black, #sp_white').on('click', function(){
    $('#sp_black, #sp_white').removeClass('sp-selected');
    $(this).addClass('sp-selected');
    singleColor = $(this).hasClass('sp-white') ? 'white' : 'black';
    updateSingleImage();
  });
  
  // ─── OPTIMIZED SPINNER (better UX) ───
  function showLoadingSpinner($btn){
    const msgs = ['Loading','Processing','Hang tight','Almost there'], L = msgs.length;
    let i = 0;
    $btn.addClass('loading')
        .prop('disabled', true)
        .html('<span class="status-text">'+msgs[0]+'</span><span class="loading-spinner"></span>');
    // Faster message rotation for better perceived performance
    var iv = setInterval(()=>{
      i = (i+1)%L;
      $btn.find('.status-text').text(msgs[i]);
    }, 2000);
    $btn.data('spinner-iv', iv);
  }
  
  function restoreClaimNow($btn){
    clearInterval($btn.data('spinner-iv'));
    $btn.removeClass('loading')
        .prop('disabled', false)
        .html('<span class="status-text">Claim now</span>');
  }
  
  // ─── SIMPLE PRECONNECT (user leaves page after clicking anyway) ───
  const link = document.createElement('link');
  link.rel = 'preconnect';
  link.href = window.location.origin;
  document.head.appendChild(link);
  
  // ─── AJAX CALL (simple and reliable) ───
  $('#single-pack-btn').off('click').on('click', function(e){
    e.preventDefault();
    
    var $btn = $(this);
    
    // Prevent double-clicks on same button
    if ($btn.hasClass('loading')) {
      return false;
    }
    
    var combo  = singleColor.charAt(0),  // "b" or "w"
        ajaxURL = ( typeof wc_add_to_cart_params !== 'undefined'
                  ? wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%','add_bundle_combo')
                  : '/wp-admin/admin-ajax.php?action=add_bundle_combo' );
    
    showLoadingSpinner($btn);
    
    $.ajax({
      url:      ajaxURL,
      method:   'POST',
      data:     { combo: combo },
      dataType: 'json',
      timeout:  10000,
      xhrFields:{ withCredentials: true }
    })
    .done(function(resp){
      if ( resp && resp.success ) {
        // Immediate redirect - user leaves page here
        window.location.href = '/checkouts/nf/';
      } else {
        console.error('Server response:', resp);
        throw new Error(resp?.data || 'Bundle addition failed');
      }
    })
    .fail(function(xhr, txt, err){
      console.error('[AJAX] Error:', xhr.status, txt, err);
      let errorMsg = 'Something went wrong. ';
      if (xhr.status === 0) {
        errorMsg += 'Please check your connection and try again.';
      } else if (xhr.status >= 500) {
        errorMsg += 'Server error. Please try again in a moment.';
      } else if (xhr.status === 404) {
        errorMsg += 'Service temporarily unavailable. Please refresh the page.';
      } else {
        errorMsg += 'Please refresh and try again.';
      }
      alert(errorMsg);
      restoreClaimNow($btn);
    });
  });
});
