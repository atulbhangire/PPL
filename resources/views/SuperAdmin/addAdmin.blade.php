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
        <title>PPL | Super Admin | Admin Management</title>
        @include ('layouts.meta')
        @include ('layouts.super_admin.css')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <!-- {{ Html::style('admin/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }} -->
        <!-- END PAGE LEVEL PLUGINS -->
    </head>

    <body class="page-container-bg-solid">
        <!-- BEGIN HEADER -->
        <div class="page-header">
            <!-- BEGIN HEADER TOP -->
            @include ('layouts.super_admin.page-header-top')
            <!-- END HEADER TOP -->
            <!-- BEGIN HEADER MENU -->
            @include ('layouts.super_admin.page-header-menu')
            <!-- END HEADER MENU -->
        </div>
        <!-- END HEADER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
                <!-- BEGIN PAGE CONTENT BODY -->
                <div class="page-content">
                    <div class="container-fluid">
                        @if(Session::has('admin_message'))
                            <div class="alert alert-success">
                                <button class="close" data-close="alert"></button>
                                <span>
                                    {{ Session::get('admin_message') }}
                                </span>
                            </div>
                        @endif
                        @if(Session::has('admin_danger'))
                            <div class="alert alert-danger">
                                <button class="close" data-close="alert"></button>
                                <span>
                                    {{ Session::get('admin_danger') }}
                                </span>
                            </div>
                        @endif
						<!-- BEGIN PAGE CONTENT INNER -->
						<div class="page-content-inner">
							<div class="row">
								<div class="col-md-12">
									<button type="button" class="btn btn-default" onclick="window.location.href='/Admin/SuperAdminDashboard'">
										<i class="glyphicon glyphicon-chevron-left"></i>
										Back
									</button>
								</div>
							</div>
							<BR/>
							<div class="row">
								<div class="col-md-12">
									<div class="portlet light ">
										<div class="portlet-title">
											<div class="caption">
												<i class="icon-equalizer font-red-sunglo"></i>
												<span class="caption-subject font-red-sunglo bold uppercase">{{ Session::has('edit_admin') ? "Edit" : 'Add' }} Admin</span>
											</div>
										</div>
										<div class="portlet-body form">
											<!-- BEGIN FORM-->
											@if (Session::has('edit_admin'))
												<form id="add_admin_form" method="POST" action="/Admin/SuperAdmin.updateAdmin" class="form-horizontal">
											@else
												<form id="add_admin_form" method="POST" action="/Admin/SuperAdmin.getAdmin" class="form-horizontal">
											@endif
												{!! Form::token() !!}
												<input type="hidden" name="admin_id" value="{{ Session::has('edit_id') ? Session::get('edit_id') : '' }}">
												<div class="form-body">
													<div class="form-group ">
														<label class="col-md-3 control-label">First Name</label>
														<div class="col-md-6">
                                                            <div class="err">
    															<input type="text" id="first_name" name="first_name" class="form-control" placeholder="Enter First Name " value="{{ Session::has('edit_first_name') ? Session::get('edit_first_name') : '' }}">
                                                            </div>
														</div>
													</div>

                                                    <div class="form-group ">
                                                        <label class="col-md-3 control-label">Last Name</label>
                                                        <div class="col-md-6">
                                                            <div class="err">
                                                                <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Enter Last Name " value="{{ Session::has('edit_last_name') ? Session::get('edit_last_name') : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group ">
                                                        <label class="col-md-3 control-label">Email</label>
                                                        <div class="col-md-6">
                                                            <div class="err">
                                                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter Last Name " value="{{ Session::has('edit_email') ? Session::get('edit_email') : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>

													<div class="form-group">
														<label class="col-md-3 control-label">Password</label>
														<div class="col-md-6">
                                                            <div class="err">
                                                                <input type="text" id="password" name="password" class="form-control" placeholder="Enter password (E.g. 7fjbeu7f)" value="{{ Session::has('edit_password') ? Session::get('edit_password') : '' }}">
                                                            </div>
														</div>
													</div>

													<div class="form-group">
														<label class="col-md-3 control-label">Contact Number</label>
														<div class="col-md-6">
                                                            <div class="err">
                                                                <input type="text" id="contact_no" name="contact_no" class="form-control" placeholder="Enter Contact No." value="{{ Session::has('edit_contact_no') ? Session::get('edit_contact_no') : '' }}">
                                                            </div>
														</div>
													</div>
													
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Address</label>
                                                        <div class="col-md-6">
                                                            <div class="err">
                                                                <input type="text" id="address" name="address" class="form-control" placeholder="Enter Address" value="{{ Session::has('edit_address') ? Session::get('edit_address') : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">User Role</label>
                                                        <div class="col-md-6">
                                                            <div class="err">
                                                                <select id="admin_role" name="admin_role" class="form-control" value="{{ Session::has('edit_admin_role') ? Session::get('edit_admin_role') : '' }}">
                                                                    <option value="HEAD MANAGER">HEAD MANAGER</option>
                                                                    <option value="HEAD OPERATOR">HEAD OPERATOR</option>
                                                                    <option value="REGION HEAD">REGION HEAD</option>
                                                                    <option value="REGION OPERATOR">REGION OPERATOR</option>
                                                                    <option value="BRANCH MANAGER">BRANCH MANAGER</option>
                                                                    <option value="BRANCH OPERATOR">BRANCH OPERATOR</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Admin Status</label>
                                                        <div class="col-md-6">
                                                            <div class="err">
                                                                <div class="mt-radio-inline">
                                                                    <label class="mt-radio">
                                                                        <input type="radio" name="admin_status" id="admin_status_1" value="1" {{ Session::has('edit_is_active') ? (Session::get('edit_is_active') == 1) ? 'checked' : '' : '' }}> Active
                                                                        <span></span>
                                                                    </label>
                                                                    <label class="mt-radio">
                                                                        <input type="radio" name="admin_status" id="admin_status_0" value="0" {{ Session::has('edit_is_active') ? (Session::get('edit_is_active') == 0) ? 'checked' : '' : '' }}> Inactive
                                                                        <span></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if (Session::has('edit_admin'))
                                                        <div class="form-group">
                                                            <div class="col-md-6">
                                                                <label class="control-label">Last Password Change</label><BR/>
                                                                {{ Session::has('edit_last_password_changed') ? Session::get('edit_last_password_changed') : '' }}
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="control-label">Last Login IP</label><BR/>
                                                                {{ Session::has('edit_login_ip') ? Session::get('edit_login_ip') : '' }}
                                                            </div>
                                                        </div>
                                                    @endif
													<div class="form-actions">
														<div class="row">
															<div class="col-md-offset-3 col-md-4">
																<button type="submit" class="btn blue">Submit</button>
															</div>
														</div>
													</div>
												</div>
											</form>
											<!-- END FORM-->
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- END PAGE CONTENT INNER -->
                    </div>
                </div>
                <!-- END PAGE CONTENT BODY -->
                <!-- END CONTENT BODY -->
            </div>
            <!-- END CONTENT -->
        </div>
        <!-- END CONTAINER -->
        <!-- BEGIN FOOTER -->
        <!-- BEGIN INNER FOOTER -->
        @include ('layouts.super_admin.page-footer')
        <!-- END INNER FOOTER -->
        <!-- END FOOTER -->
        @include ('layouts.super_admin.js')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        {{ Html::script('admin/assets/js/jquery.validate.min.js') }}
        <!-- {{ Html::script('admin/assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}
        {{ Html::script('admin/assets/global/plugins/select2/js/select2.full.min.js') }}
        {{ Html::script('admin/assets/global/plugins/moment.min.js') }}
        {{ Html::script('admin/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}
        {{ Html::script('admin/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}
        {{ Html::script('admin/assets/pages/scripts/components-date-time-pickers.min.js') }} -->
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN CUSTOM SCRIPT -->
        <script type="text/javascript">
            var AddAdmin = function() {
                var handleAddAdmin = function() {
                    $('#add_admin_form').validate({
                        errorElement: 'span', //default input error message container
                        errorClass: 'help-block', // default input error message class
                        focusInvalid: false, // do not focus the last invalid input
                        rules: {
                            username: {
                                required: true,
                                minlength: 2
                            },
                            password: {
                                required: true,
                                minlength: 6
                            },
                            change_password: {
                                required: true,
                                min: 0
                            },
                            permissible_ip: {
                                required: true
                            },
                            'checkbox[]': {
                                required: function(elem) {
                                	return $("input:checked").length > 0;
                                }
                            },
                            permissible_time_start: {
                            	required: true
                            },
                            permissible_time_end: {
                            	required: true
                            },
                            admin_status: {
                                required: function(elem) {
                                    return $("input:checked").length > 0;
                                }
                            }
                        },

                        messages: {
                            username: {
                                required: "This field is required.",
                                minlength: "Enter minimum 2 characters."
                            },
                            password: {
                                required: "This field is required.",
                                minlength: "Enter minimum 6 characters."
                            },
                            change_password: {
                                required: "This field is required.",
                                min: "Enter positive value."
                            },
                            permissible_ip: {
                                required: "This field is required."
                            },
                            'checkbox[]': {
                                required: "Check atleast one checkbox."
                            },
                            permissible_time_start: {
                            	required: "This field is required."
                            },
                            permissible_time_end: {
                            	required: "This field is required."
                            },
                            admin_status: {
                                required: "Select atleast one value."
                            }
                        },

                        invalidHandler: function(event, validator) { //display error alert on form submit   
                            $('.alert-danger', $('add_admin_form')).show();
                        },

                        highlight: function(element) { // hightlight error inputs
                            $(element)
                                .closest('.form-group').addClass('has-error'); // set error class to the control group
                        },

                        success: function(label) {
                            label.closest('.form-group').removeClass('has-error');
                            label.remove();
                        },

                        errorPlacement: function(error, element) {
                            error.insertAfter(element.closest('.err'));
                        },

                        submitHandler: function(form) {
                            form.submit(); // form validation success, call ajax form submit
                        }
                    });

                    $('#add_admin_form input').on('keypress', 'change', function(e) {
                        if (e.which == 13) {
                            if ($('#add_admin_form').validate().form()) {
                                $('#add_admin_form').submit(); //form validation success, call ajax form submit
                            }
                            return false;
                        }
                    });
                }

                return {
                    //main function to initiate the module
                    init: function() {
                        handleAddAdmin();
                    }
                };
            }();

            jQuery(document).ready(function() {
                AddAdmin.init();
                $('#li_admin_management').addClass('active');
            });
        </script>
	    <!--END CUSTOM SCRIPT -->
    </body>
</html>