jQuery.noConflict();
jQuery( document ).ready(function( $ ) {
    $('#generate-woo-key').click(function () {
        var siteUrl = $('#site-url').val();
        var ajaxNonce = $('#ajax-nonce').val();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'text',
            data: {
                market: 'woocommerce',
                siteurl: siteUrl,
                action: 'gen_woo_key',
                ajaxNonce: ajaxNonce,
            },
            success: function(resp){
                if (resp == 'error') {
                    alert('Something went wrong! Contact us at hi@around.io');
                }
                else {
                    $('#key-box').css('display','block');
                    $('#generate-key-box').css('display','none');
                    $('#aroundKey').text(resp);
                }
            }
        });
    });
});