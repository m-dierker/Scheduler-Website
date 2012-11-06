
<?php

require_once("config.php");

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Scheduler - Facebook Hackathon 2012</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="/css/bootstrap/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="/css/bootstrap/datepicker.css" rel="stylesheet">
    <link href="/css/bootstrap/timepicker.css" rel="stylesheet">
    <link href="/css/site.css" rel="stylesheet">

    <style type="text/css">
      body {
        padding-top: 20px;
        padding-bottom: 40px;
      }

      /* Custom container */
      .container-narrow {
        margin: 0 auto;
        max-width: 700px;
      }
      .container-narrow > hr {
        margin: 30px 0;
      }

      /* Main marketing message and sign up button */
      .jumbotron {
        margin: 60px 0;
        text-align: center;
      }
      .jumbotron h1 {
        font-size: 72px;
        line-height: 1;
      }
      .jumbotron .btn {
        font-size: 21px;
        padding: 14px 24px;
      }

      /* Supporting marketing content */
      .marketing {
        margin: 60px 0;
      }
      .marketing p + h4 {
        margin-top: 28px;
      }
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="/js/bootstrap/bootstrap.min.js"></script>
    <script src="/js/bootstrap/bootstrap-datepicker.js"></script>
    <script src="/js/bootstrap/bootstrap-timepicker.js"></script>
    <script src="js/global.js"></script>
    <script src="js/Schedule.js"></script>
  </head>

  <body>

    <!-- Start Facebook Code -->
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        // Init the FB JS SDK
        FB.init({
          appId      : '<?php echo FACEBOOK_APP_ID; ?>', // App ID from the App Dashboard
          channelUrl : '//schedule.eatcumtd.com/channel.php', // Channel File for x-domain communication
          status     : true, // check the login status upon init?
          cookie     : true, // set sessions cookies to allow your server to access the session?
          xfbml      : true  // parse XFBML tags on this page?
        });

        FB.getLoginStatus(function(response) {
          schedule.onFacebookLogin(response);
        }.bind(this));

        // Additional initialization code such as adding Event Listeners goes here
        FB.Event.subscribe('auth.authResponseChange', function(response) {
          schedule.onFacebookLogin(response);
        }.bind(this));
      };



      // Load the SDK's source Asynchronously
      (function(d){
         var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement('script'); js.id = id; js.async = true;
         js.src = "//connect.facebook.net/en_US/all.js";
         ref.parentNode.insertBefore(js, ref);
       }(document));
    </script>
    <!-- End Facebook Code -->

    <div class="container-narrow">

      <div class="masthead">
        <ul class="nav nav-pills pull-right">
          <li class="active"><a href="#">Home</a></li>
          <li><a href="http://www.github.com/mvd7793/">GitHub</a></li>
          <li><a href="http://www.dierkers.com">Contact</a></li>
        </ul>
        <h3 class="muted" style="margin-bottom: 0px;">Scheduler</h3><h5 class="muted" style="margin-top: 0px; margin-bottom: 0px;">By Matthew Dierker</h5>
      </div>

      <hr>

      <div class="jumbotron">
        <h1>Schedule anything</h1>
        <p class="lead">Sign in below to schedule texts, calls, and more. Anytime.</p>
        <div class="hideAtStart" id="login-button">
          <fb:login-button id="login-button" size="large" show-faces="false">Schedule with Facebook</fb:login-button>
        </div>
      </div>

      <hr>

      <div class="row-fluid marketing" id="mainMarketing">
        <div class="span12 hideAtStart" id="mainform">
          <form method="GET" action="http://google.com">
            <p class="lead" id="#welcome-text">Start here by picking what you'd like to schedule</p>
            <div class="tabbable" style="margin-bottom: 18px;">
              <ul class="nav nav-tabs">
                <li class="active" id="type4"><a href="#call" data-toggle="tab">Schedule a Call</a></li>
                <li class="" id="type3"><a href="#sms" data-toggle="tab">Schedule a Text</a></li>
                <li class="" id="type5"><a href="#mail" data-toggle="tab">Schedule an Email</a></li>
              </ul>
              <div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
                <div class="tab-pane active" id="call">
                  <input type="text" class="input-block-level" placeholder="Phone number" name="to" id="to">
                  <textarea placeholder="What would you like the call to say?" style="width: 98%; height: 4em" name="msg" id="msg"></textarea>
                </div>
                <div class="tab-pane" id="sms">
                  <input type="text" class="input-block-level" placeholder="Phone number" name="to" id="to1">
                  <textarea placeholder="What would you like the text to say?" style="width: 98%; height: 4em" name="msg" id="msg1"></textarea>
                </div>
                <div class="tab-pane" id="mail">
                  <input type="text" class="input-block-level" placeholder="Email address" name="to" id="to2">
                  <input type="text" class="input-block-level" placeholder="Subject" name="subj" id="subj2">
                  <textarea placeholder="What would you like the email to say?" style="width: 98%; height: 4em" name="msg" id="msg2"></textarea>
                </div>
              </div>
            </div>
            <p class="lead">And now pick when to schedule it</p>

            <div class="input-append date" style="display: inline;">
              <input class="span2" size="16" type="text" id="datepicker" >
              <span class="add-on"><i class="icon-calendar"></i></span>
            </div>

            <p style="display:inline; margin: 0 25px; font-size: 16px" class="lead">at</p>

            <div class="input-append bootstrap-timepicker-component" style="display:inline;">
              <input class="input-small" type="text" id="timepicker">
                <span class="add-on">
                  <i class="icon-time"></i>
                </span>
            </div>

             <p style="display:inline; margin: 0 10px; font-size: 16px" class="lead"> (time in CST)</p>

            <div style="margin-top: 10px;" id="final-button">
              <input type="submit" class="btn btn-large btn-success"></input>
            </div>
            <div style="margin-top: 10px" id="final-done" class="hideAtStart">
              <p class="lead">Done! Your event has been scheduled.</p>
            </div>
          </form>
        </div>
      </div>

      <hr style="margin: 10px 0">

      <p class="lead" style="font-size: 12px">Please note that this is a breakable demo, and events not from Matthew's Facebook will not be procesed. <br>If you would like test access, <a href="http://facebook.com/mdierker">send me a message</a>.</p>

    <!--   <div class="footer">
        <p>&copy; Matthew Dierker 2012</p>
      </div>
 -->


      <!-- End Content -->
    </div>
  </body>
</html>
