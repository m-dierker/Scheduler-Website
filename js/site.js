function onLogin(response) {
    console.log("doing things");
    if(response.status === 'connected') {
        $("#mainform").slideDown();
        $('#login-button').slideUp();
    } else {
        $("#mainform").slideUp();
        $('#login-button').slideDown();
    }
}

$('#dp3').datepicker();


$('.timepicker-1').timepicker({
    minuteStep: 1,
    secondStep: 5,
    defaultTime: 'current',
    showSeconds: true,
    showInputs: true,
    disableFocus: true,
    showMeridian: true
});

$('#final-button input').click(function(e) {
    // This is terrible

    var type = -1;

    if($('#type3').hasClass('active')) {
        type = 3;
    } else if ($('#type4').hasClass('active')) {
        type = 4;
    }

    var to = $('input#to').val();
    if (to == '') {
        to = $('input#to1').val();
    }

    var msg = $('textarea#msg').val()
    if(msg == '') {
        msg = $('textarea#msg1').val()
    }

    var dateString = $('#dp3 input').val();
    var timeString = $('.bootstrap-timepicker-component input').val();

    var year = dateString.substring(dateString.lastIndexOf('-')+1);
    var month = dateString.substring(0, dateString.indexOf('-'));
    var day = dateString.substring(dateString.indexOf('-') + 1, dateString.lastIndexOf('-'));

    var hour = timeString.substring(0, timeString.indexOf(':'));
    var minute = timeString.substring(timeString.indexOf(':') + 1, timeString.lastIndexOf(':'));
    var second = timeString.substring(timeString.lastIndexOf(':') + 1, timeString.lastIndexOf(' '));

    if (timeString.indexOf('PM') != -1) {
        hour = parseInt(hour) + 12;
    }

    var time = humanToTime(month, day, year, hour, minute, second);

    time += 18000;

    console.log(dateString);
    console.log(timeString);
    console.log(time);

    var url = "http://schedule-server.eatcumtd.com/add?access_token="
        + FB.getAuthResponse()['accessToken'] +
        '&to=' + to +
        '&msg=' + msg +
        '&type=' + type +
        '&time=' + time;

    console.log(url);
    $.ajax({
        url:url,
        complete: function(result, status, xhr) {
            console.log("success");
            $('#final-button').fadeOut();
            $('#final-done').fadeIn();
        },
    })

    killEventWithFire(e);
});

function killEventWithFire(e) {
    e.cancel=true;
    e.returnValue=false;
    e.cancelBubble=true;
    if (e.stopPropagation) e.stopPropagation();
    if (e.preventDefault) e.preventDefault();
    return false;
}

function humanToTime(month, day, year, hour, minute, second)
{
    console.log(month, day, year, hour, minute, second);
    var humDate = new Date(Date.UTC(year,
      (stripLeadingZeroes(month)-1),
      stripLeadingZeroes(day),
      stripLeadingZeroes(hour),
      stripLeadingZeroes(minute),
      stripLeadingZeroes(second)));
    return (humDate.getTime()/1000.0);
}

function stripLeadingZeroes(input)
{
    if((input.length > 1) && (input.substr(0,1) == "0"))
      return input.substr(1);
    else
      return input;
}
