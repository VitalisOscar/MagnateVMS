function valid(){
    if($('#category').val() == ''){
        $('#category_error').text('Select a category!', 'Error');
        showAlert('Select a category!');
        return false;
    }
    $('#category_error').text('');

    if($('#description').val() == ''){
        $('#description_error').text('Please enter a description!', 'Errror', 'Error');
        showAlert('Please enter a description!');
        return false;
    }
    $('#description_error').text('');

    if(document.querySelectorAll('#slots .slot').length == 0){
        $('#slots_error').text('You need to book at least one slot!', 'Error');
        showAlert('You need to book at least one slot!', 'Book a slot');
        return false;
    }
    $('#slots_error').text('');

    return true;
}

function showTerms(){
    if(valid()){
        $('#tac').modal({
            backdrop: 'static'
        });
    }
}

function submit_data(){
    if(valid()){
        if(!document.querySelector('#agree_terms').checked){
            $('#terms_error').removeClass('d-none');
            return;
        }

        $('#terms_error').addClass('d-none');

        $('#tac').modal('hide');

        ad_form.addClass('loading');
        bar.css('width: 0');
        progress.text('0%');

        $.ajax({
            url: ad_url,
            type: 'post',
            data: new FormData(document.querySelector('#ad_form')),
            contentType: false,
            processData: false,
            xhr: function(){
                //upload Progress
                var xhr = $.ajaxSettings.xhr();

                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;

                        if (event.lengthComputable)
                        {
                            percent = Math.ceil((position / total) * 100);
                        }
                        //update progressbar
                        bar.css('width', + percent +'%');
                        progress.text(percent +'%');
                    }, true);
                }
                return xhr;
            },
            success: function(response){
                ad_form.removeClass('loading');

                // Ad created
                if(response.success){
                    window.location.replace(exit_url);
                }else{
                    showAlert(response.errors[0], 'Error');
                }
            },
            error: function(error){
                console.log(error);
                ad_form.removeClass('loading');
                showAlert('Something went wrong. Please try again', 'Oops');
            }
        });
    }

}
