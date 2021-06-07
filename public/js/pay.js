function initPayment(){
    $('#pay_form_input').removeClass('d-none');
    $('#pay_form_error').addClass('d-none');
    $('#pay_form_success').addClass('d-none');

    $('#payment').modal({
        backdrop: 'static'
    });

    // Onsubmit
    var pay_form = $('#pay_form');
    pay_form.on('submit', function(e){
        e.preventDefault();

        pay_form.addClass('loading');

        $.ajax({
            url: mpesa_url,
            type: 'post',
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function(response){
                console.log(response);
                pay_form.removeClass('loading');

                if(response.success){
                    // Initiated
                    $('#pay_form_input').addClass('d-none');
                    $('#pay_form_success').removeClass('d-none');
                }else{
                    $('#pay_form_input').addClass('d-none');
                    $('#pay_form_error').removeClass('d-none');
                    $('#payment_err_msg').text(response.errors[0]);
                }
            },
            error: function(error){
                pay_form.removeClass('loading');
                $('#pay_form_input').addClass('d-none');
                $('#pay_form_error').removeClass('d-none');
                $('#payment_err_msg').text('Something went wrong. Please try again');
                console.log(error);
            }
        });
    });
}
