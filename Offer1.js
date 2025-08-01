jQuery(function($){
  var singleColor = 'black';

  // ─── 1) PRE-SELECT BLACK & SHOW IMAGE ───
  $('#sp_black, #sp_white').removeClass('sp-selected');
  $('#sp_black').addClass('sp-selected');
  updateSingleImage();

  // ─── 2) SWATCH CLICK ───
  $('#sp_black, #sp_white').on('click', function(){
    $('#sp_black, #sp_white').removeClass('sp-selected');
    $(this).addClass('sp-selected');
    singleColor = $(this).hasClass('sp-white') ? 'white' : 'black';
    updateSingleImage();
  });

  function updateSingleImage(){
    $('.single_pack_img').hide();
    $('#single_' + singleColor).show();
  }

  // ─── 3) SPINNER HELPERS ───
  function showLoadingSpinner($btn){
    const msgs = ['Loading','Hang tight','Almost there'], L = msgs.length;
    let i = 0;
    $btn.addClass('loading')
        .prop('disabled', true)
        .html('<span class="status-text">'+msgs[0]+'</span><span class="loading-spinner"></span>');
    var iv = setInterval(()=>{
      i = (i+1)%L;
      $btn.find('.status-text').text(msgs[i]);
    }, 2500);
    $btn.data('spinner-iv', iv);
  }
  function restoreClaimNow($btn){
    clearInterval($btn.data('spinner-iv'));
    $btn.removeClass('loading')
        .prop('disabled', false)
        .html('<span class="status-text">Claim now</span>');
  }

  // ─── 4) CLICK “CLAIM NOW” → AJAX → REDIRECT ───
  $('#single-pack-btn').off('click').on('click', function(e){
    e.preventDefault();
    var $btn   = $(this),
        combo  = singleColor.charAt(0),  // “b” or “w”
        ajaxURL = ( typeof wc_add_to_cart_params !== 'undefined'
                  ? wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%','add_bundle_combo')
                  : '/wp-admin/admin-ajax.php?action=add_bundle_combo' );

    showLoadingSpinner($btn);

    $.ajax({
      url:      ajaxURL,
      method:   'POST',
      data:     { combo: combo },
      dataType: 'json',
      xhrFields:{ withCredentials: true }
    })
    .done(function(resp){
      if ( resp && resp.success ) {
        // tiny delay for cookies
        setTimeout(()=> window.location.href = '/checkouts/nf/', 80);
      } else {
        throw new Error('bundle failed');
      }
    })
    .fail(function(xhr, txt, err){
      console.error('[AJAX] Error:', txt, err);
      alert('Something went wrong. Please refresh and try again.');
      restoreClaimNow($btn);
    });
  });
});
