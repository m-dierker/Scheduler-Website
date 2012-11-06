function Schedule() {
    this.datepickerVisible = false;

    this.setupDatepicker();
    this.setupTimepicker();
    this.setupSubmitButton();
}

/**
 * On Facebook login
 */
Schedule.prototype.onFacebookLogin = function(response) {
    if(response.status === 'connected') {
        $("#mainform").fadeIn();
        $('#login-button').hide();
    } else {
        $("#mainform").slideUp();
        $('#login-button').slideDown();
    }
};

/**
 * Sets up the datepicker
 */
Schedule.prototype.setupDatepicker = function() {
    // Setup datepicker format + show + hide
    $('#datepicker').datepicker({
        format: 'mm-dd-yyyy',
        weekStart: 0
    }).on('hide', function(e) {
        console.log("on hide");
        this.datepickerVisible = false;
    }).focus(function(e) {
        this.datepickerVisible = false;
        $('#datepicker').datepicker('show');
    }.bind(this));

    // The icon needs to be clickable too
    $('#datepicker').parent().find('.icon-calendar').click(function(e) {
        $('#datepicker').datepicker('show');
        this.datepickerVisible = true;
        e.stopPropagation();
    }.bind(this));

    // The icon can show, but now it should hide when the user clicks somewhere else
    $("body").click(function(e) {
        if(this.datepickerVisible) {
            console.log("hiding");
            $('#datepicker').datepicker('hide');
        }
    }.bind(this));

    // Fill in the default value for datepicker
    var date = new Date();
    var dateString = "" + padWithZeros(date.getMonth()+1, 2) + '-' + padWithZeros(date.getDate(), 2) + '-' + date.getFullYear();
    $('#datepicker').val(dateString);
};


Schedule.prototype.setupTimepicker = function() {
    // Set up the timepicker
    $('#timepicker').timepicker({
        minuteStep: 1,
        secondStep: 5,
        defaultTime: 'current',
        showSeconds: true,
        showInputs: true,
        disableFocus: true,
        showMeridian: true
    });
};

Schedule.prototype.setupSubmitButton = function() {
    $('#final-button input').click(function(e) {
        this.submitEvent(e);
    }.bind(this));
}

Schedule.prototype.submitEvent = function(e) {
    // This is terrible

    var type = -1;

    if($('#type3').hasClass('active')) {
        type = 3;
    } else if ($('#type4').hasClass('active')) {
        type = 4;
    } else if ($('#type5').hasClass('active')) {
        type = 5;
    }

    var to = $('input#to').val();
    if (to == '') {
        to = $('input#to1').val();
    }
    if (to == '') {
        to = $('input#to2').val();
    }


    var msg = $('textarea#msg').val()
    if(msg == '') {
        msg = $('textarea#msg1').val()
    }
    if(msg == '') {
        msg = $('textarea#msg2').val()
    }

    var subj = $('input#subj2').val();

    var dateString = $('#datepicker').val();
    var timeString = $('#timepicker').val();

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

    time += 21600;

    console.log(dateString);
    console.log(timeString);
    console.log(time);

    var url = "http://schedule-server.eatcumtd.com/add?access_token="
        + FB.getAuthResponse()['accessToken'] +
        '&to=' + to +
        '&msg=' + msg +
        '&type=' + type +
        '&subj=' + subj +
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

    e.stopPropagation();
};

window.onload = function(e) {
    var schedule = new Schedule();
    window.schedule = schedule;

    FB.getLoginStatus(function(response) {
        schedule.onFacebookLogin(response);
    }.bind(this));

    // Additional initialization code such as adding Event Listeners goes here
    FB.Event.subscribe('auth.authResponseChange', function(response) {
        schedule.onFacebookLogin(response);
    }.bind(this));
}
