<!DOCTYPE html>
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <title>PPL</title>
        @include ('layouts.meta')
        @include ('layouts.auth.css')
    </head>
    <!-- END HEAD -->
    <body class=" login">
        <!-- BEGIN LOGO -->
        @include ('layouts.logo')
        <!-- END LOGO -->
        <!-- BEGIN MFA AUTH -->
        <div class="content">
            @if(Session::has('error_message'))
               <div class="alert alert-danger">
                   <button class="close" data-close="alert"></button>
                   <span>
                       {{ Session::get('error_message') }}
                   </span>
               </div>
            @endif 
            <!-- BEGIN MFA AUTH FORM -->
            @if (Session::get('admin_type') == 'superadmin')
                <div class="title1">
                    Please scan QR code using Google Authenticator app
                </div>
                <BR/>
                <form class="login-form" id='frmsuadmin' action="/Admin/verifysuadminmfa" method="POST">
                    <input type ="hidden" id="super_admin_id" name="super_admin_id" value="{{ Session::has('user_id') ? Session::get('user_id') : '' }}" >
                    @if(Session::get('showurl'))
                        <center><img src="{{ Session::has('showurl') ? Session::get('showurl') : '' }}"></center>
                    @endif
            @else
                    <div class="title1">
                        Please scan QR code using Google Authenticator app
                    </div>
                    <BR/>
                    <form id='frmsuadmin' action="/Admin/verifyadminmfa" method="POST">
                        <input type ="hidden" id="admin_id" name="admin_id" value="{{ Session::has('user_id') ? Session::get('user_id') : '' }}" >
                        @if(Session::get('showurl'))
                            <center><img src="{{ Session::has('showurl') ? Session::get('showurl') : '' }}"></center>
                        @endif
            @endif
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label class="control-label">Enter MFA Key</label>
                            <input type="text" name="user_input_secret" class="form-control" autocomplete="off" autofocus="on" />
                        </div>
                        <div class="row">
                            <div class="margin-top-10 pull-right">
                                <button type="submit" class="btn green"> Submit </button>
                            </div>
                        </div>
                    </form>
            <!-- END MFA AUTH FORM -->
        </div>
        <!-- END MFA AUTH -->
        @include ('layouts.auth.js')

    </body>

</html>