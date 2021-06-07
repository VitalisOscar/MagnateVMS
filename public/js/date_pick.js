// var fp = flatpickr('#play_date');
window.dates = '';

initPicker();

$('#add_slot').on('shown.bs.modal', function(){
    initPicker();
});

function getPickedDates(){
    var key = 's' + $('#screen_id').val() + 'p' + $('#package').val();
    console.log(window.picked_dates);
    return window.picked_dates[key] != null ? window.picked_dates[key]:[];
}

function initPicker(){
    $('#play_date_from').val(null);
    $('#play_date_to').val(null);
    $('#play_date_multi').val(null);

    multiMode();
}

function multiMode(){
    $('#for_multi').addClass('active');
    $('#for_range').removeClass('active');

    flatpickr('#play_date',{
        mode: 'multiple',
        minDate: min_date,
        disable: getPickedDates(),
        onChange: function(d){
            $('#play_date_from').val(null);
            $('#play_date_to').val(null);
            $('#play_date_multi').val($('#play_date').val());
        }
    });
}

function rangeMode(){
    $('#for_range').addClass('active');
    $('#for_multi').removeClass('active');

    flatpickr('#play_date',{
        mode: 'range',
        minDate: min_date,
        disable: getPickedDates(),
        onChange: function(d){
            var d = $('#play_date').val();

            d= d.split(' to ');

			$('#play_date_from').val(d[0]);
            $('#play_date_to').val(d[1]);
            $('#play_date_multi').val(null);
        }
    });
}

