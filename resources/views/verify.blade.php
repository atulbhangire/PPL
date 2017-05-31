<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.6
Version: 4.5.6
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
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
                <form class="login-form" id='frmsuadmin' action="/Admin/verifysuadminmfa" method="POST">
                    <input type ="hidden" id="super_admin_id" name="super_admin_id" value="{{ Session::has('user_id') ? Session::get('user_id') : '' }}" >
            @endif
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label class="control-label">Enter MFA Key</label>
                        <input type="text" name="user_input_secret" class="form-control" autocomplete="off" autofocus="on" />
                    </div>
                    <div class="form-group">
                        <div class="margin-top-10">
                            <button type="submit" class="btn blue"> Submit </button>
                        </div>
                    </div>
                </form>
            <!-- END MFA AUTH FORM -->
        </div>
        <!-- END MFA AUTH -->
        @include ('layouts.auth.js')

    </body>

</html>