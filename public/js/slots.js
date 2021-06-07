// holds picked dates for each screen, package
if(window.picked_dates == null){
    window.picked_dates = {};
}

$('#add_slot').on('hidden.bs.modal', function(){
    window.dates = null;
});

$('#add_slot').on('shown.bs.modal', function(){
    window.dates = [];

    multiMode();
});

flatpickr('#play_date',{
    mode: 'multiple'
});

function checkAvailability(){
    var slot = getSlot();
    if(!slot) return;

    var slot_form = $('#slot_form');
    slot_form.addClass('loading');

    $.ajax({
        url: availability_url,
        type: 'post',
        data: new FormData(document.querySelector(('#slot_form'))),
        contentType: false,
        processData: false,
        success: function(response){
            console.log(response);
            slot_form.removeClass('loading');

            if(response.success){
                var data = response.data;

                var available = data.available;
                var unavailable = data.unavailable;
                var price = data.price;

                $('#new_slot_price').text(data.display_price);
                $('#new_slot_loops').text(data.loops);
                $('#new_slot_price_err').text(data.display_price);

                if(unavailable.length == 0 && available.length > 0){
                    // Available
                    $('#slot_form_input').addClass('d-none');
                    $('#slot_form_error').addClass('d-none');
                    $('#slot_form_success').removeClass('d-none');

                    $('#selected_play_dates').val(available.join(','));
                }else{
                    if(available.length == 0){
                        var txt = 'None of the slots you booked is available. Please select different dates or another screen or package and try again';
                    }else{
                        var txt = 'The selected slot is not available on the following dates: <br>';
                        for(i=0; i<unavailable.length; i++){
                            if(i != 0) txt = ', ' + txt;
                            txt += '<strong>' + data.unavailable[i] + '</strong>'
                        }

                        txt += '<br>Please select different screens and packages for these dates';
                    }

                    $('#slot_form_error .error-text').html(txt);

                    $('#slot_form_input').addClass('d-none');
                    $('#slot_form_success').addClass('d-none');
                    $('#slot_form_error').removeClass('d-none');

                    $('#date_error').text('').addClass('d-none');
                    $('#date_info').removeClass('d-none');
                }
            }else{
                $('#date_error').text(response.errors[0]).removeClass('d-none');
                $('#date_info').addClass('d-none');
            }
        },
        error: function(error){
            slot_form.removeClass('loading');
            $('#pay_form_input').addClass('d-none');
            $('#pay_form_error').removeClass('d-none');
            $('#payment_err_msg').text('Something went wrong. Please try again');
            console.log(error);
        }
    });
}

/**
 * Initialize time and duration
 */
function initOptions(){
    $('#package').attr('data-package', $('#package').attr('data-init-package'));

    document.querySelector('#package').selectedIndex = 0;
    document.querySelector('#screen_id').selectedIndex = 0;

    $('.nice-select').niceSelect('update');
}

function initSlotDialog(){
    $('#slot_form_input').removeClass('d-none');
    $('#slot_form_error').addClass('d-none');
    $('#slot_form_success').addClass('d-none');

    $('#slot_step1').removeClass('d-none');
    $('#slot_step2').addClass('d-none');

    $('#selected_dates .dates').html('');
    $('#selected_dates .none').removeClass('d-none');

    $('#date_error').addClass('d-none');
    $('#date_info').removeClass('d-none');

    $('#play_date').val($('#play_date').attr('data-init'));
}

function addToMainForm(){
    var slot = getSlot();

    // Note the booked dates
    var key = 's' + slot.screen_id + 'p' + slot.package;
    window.picked_dates[key] = window.picked_dates[key] != null ? window.picked_dates[key].concat(slot.play_dates) : slot.play_dates;

    // index
    var index = parseInt($('#slots').attr('data-index'));
    var id_prefix = "slot_"+index+"_";

    // Add slot to ui
    var slot_ui = `<div class="slot border rounded py-3 px-3 mb-3" style="box-shadow:none">
            <span class="mr-3 rounded-circle bg-warning text-white d-none align-items-center justify-content-center" style="width: 35px; height: 35px">
                <i class="fa fa-video-camera"></i>
            </span>
            <div class="" style="max-width: 100%>
                <h6 class="mb-2"><strong id="`+id_prefix+`screen_title">`+ slot['display_title'] +`</strong></h6>
                <div class="py-1 mb-2" style="white-space:nowrap; overflow-x: auto; scrollbar-width: thin; max-width: 100%">
                    `+ slot['display_dates'] +`
                </div>
                <div>
                    <strong>`+ slot['display_price'] +`</strong>
                    <button type="button" class="btn float-right ml-auto btn-link p-0" onclick="removeSlot(`+ index +`)">
                        <i class="fa fa-trash mr-1"></i>Remove
                    </button>
                </div>
            </div>
        </div>`;

    // Add form element
    var slot_inputs = `<div>
        <input type="hidden" name="slots[`+index+`][screen_id]" value="`+ slot.screen_id +`" id="`+id_prefix+`screen_id">
        <input type="hidden" name="slots[`+index+`][package]" value="`+ slot.package +`" id="`+id_prefix+`package">`;

    for(i = 0; i<slot.play_dates.length; i++){
        slot_inputs += `<input type="hidden" name="slots[`+index+`][play_date][`+ i +`]" value="`+ slot.play_dates[i] +`" id="`+id_prefix+`play_date_"`+i+`>`;
    }

    slot_inputs += `</div>`;

    var markup = "<div data-dates='" + key + "' id='" + id_prefix + "'>" + slot_ui + slot_inputs + "</div>";

    $('#slots').html($('#slots').html() + markup);

    // Hide modal
    $('#add_slot').modal('hide');

    // init
    initOptions();

    // Increment index
    $('#slots').attr('data-index', index+1);
}

/* Edit existing slot */
function editSlot(index){
    var id_prefix = "slot_"+index+"_";

    var d, initial_d, inputs = ['package', 'screen_id'];

    // Update selects
    for(x=0; x<inputs.length; x++){
        d = document.querySelector('#' + inputs[x]);
        initial_d = $('#'+id_prefix+inputs[x]).val();

        for(i=0; i<d.children.length; i++){
            if(d.children.item(i).value == initial_d){
                d.selectedIndex = i;
                break;
            }
        }
    }

    // play date
    $('#play_date').val($('#'+id_prefix+'play_date').val());

    $('.nice-select').niceSelect('update');

    $('#submit_slot_btn').attr('onclick','updateEditedSlot('+index+')');

    $('#add_slot').modal({
        backdrop: 'static'
    });
}

/* Update edited slot */
function updateEditedSlot(index){
    var slot = getSlot();

    if(!slot) return;

    checkAvailability(function(){
        // index
        var id_prefix = "slot_"+index+"_";

        // update ui
        $('#'+id_prefix+'screen_title').text(slot.screen_title);
        $('#'+id_prefix+'time').text(slot.display_time);

        // update form elements
        $('#'+id_prefix+'screen_id').val(slot.screen_id);
        $('#'+id_prefix+'duration').val(slot.duration);
        $('#'+id_prefix+'play_date').val(slot.play_date);
        $('#'+id_prefix+'package').val(slot.package);

        // Hide modal
        $('#add_slot').modal('hide');
    });
}

/* Remove an existing slot */
function removeSlot(index){
    window.picked_dates[$("#slot_"+index+"_").attr('data-dates')] = [];
    $("#slot_"+index+"_").remove();
}

/* Get slot data from form */
function getSlot(){
    var package = $('#package').val();

    var screen = document.querySelector('#screen_id');

    var screen_id = screen.value;
    var screen_title = screen.children.item(screen.selectedIndex).innerText;

    var play_dates = [];

    $('#screen_error').text('');
    $('#package_error').text('');
    $('#date_error').text('');

    if(screen_id == ''){
        $('#screen_error').text('You need to select a screen');
        $('#slot_step1').removeClass('d-none');
        $('#slot_step2').addClass('d-none');
        return;
    }

    $('#screen_error').text('');

    if(package == ''){
        $('#package_error').text('You need to select a package');
        $('#slot_step1').removeClass('d-none');
        $('#slot_step2').addClass('d-none');
        return;
    }

    $('#package_error').text('');

    if($('#play_date').val() == null){
        $('#date_error').text('You need to select at least one date').removeClass('d-none');
        $('#date_info').addClass('d-none');
        $('#slot_step1').addClass('d-none');
        $('#slot_step2').removeClass('d-none');
        return;
    }

    var p = $('#package').attr('data-package');

    var selected_play_dates = $('#selected_play_dates').val();
    selected_play_dates = selected_play_dates.split(',');

    var d = '';
    for(i = 0; i< selected_play_dates.length; i++){
        play_dates.push(selected_play_dates[i]);

        d +=
        `<div id="slot_date_`+i+`" class="d-inline-block mr-2 date-chip" data-date="`+ play_dates[i] +`">
            <div class="py-1 px-3 bg-lighter d-inline-flex align-items-center" style="border-radius: 25px">
                <span>`+ play_dates[i] +`</span>

            </div>
        </div>`;
    }

    return {
        'play_dates': play_dates,
        'play_dates_value': $('#play_date').val(),
        'package': package,
        'screen_id': screen_id,
        'display_title': screen_title + ' - ' + p,
        'display_dates': d,
        'display_price': $('#new_slot_price').text()
    };
}

function validateStep(){
    $('#screen_error').text('');
    $('#package_error').text('');

    if($('#screen_id').val() == ''){
        $('#screen_error').text('You need to select a screen');
        return false;
    }

    $('#screen_error').text('');

    if($('#package').val() == ''){
        $('#package_error').text('You need to select a package');
        return false;
    }

    $('#package_error').text('');

    multiMode();

    return true;
}
