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
        <title>SPTulsian.com | Super Admin | Admin Management</title>
        @include ('layouts.meta')
        @include ('layouts.super_admin.css')
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
						<!-- BEGIN PAGE CONTENT INNER -->
						<div class="page-content-inner">
							<div class="row">
								<div class="col-md-12">
									<button type="button" class="btn btn-default" onclick="window.location.href='/Admin/SuperAdminZone'">
										<i class="glyphicon glyphicon-chevron-left"></i>
										Back
									</button>
								</div>
							</div>	
							<BR/>
							@if(Session::has('error_message'))
				               <div class="alert alert-success">
				                   <button class="close" data-close="alert"></button>
				                   <span>
				                       {{ Session::get('error_message') }}
				                   </span>
				               </div>
				            @endif 
				            @if(Session::has('error_message_danger'))
				               <div class="alert alert-danger">
				                   <button class="close" data-close="alert"></button>
				                   <span>
				                       {{ Session::get('error_message_danger') }}
				                   </span>
				               </div>
				            @endif 
							<div class="row">
								<div class="col-md-12">
									<div class="portlet light ">
										<div class="portlet-title">
											<div class="caption">
											@if(Session::has('edit_zone'))
												<i class="icon-equalizer font-red-sunglo"></i>
												<span class="caption-subject font-red-sunglo bold uppercase">Edit Zone</span>
											@else
												<i class="icon-equalizer font-red-sunglo"></i>
												<span class="caption-subject font-red-sunglo bold uppercase">Add Zone</span>
											@endif
											</div>
										</div>
										<div class="portlet-body form">
											<!-- BEGIN FORM-->
										@if(Session::has('edit_zone'))
											<form id="add_zone_form" method="POST" action="/Admin/SuperAdmin.modifyZone" class="form-horizontal">
											<input type="hidden" value="{{Session::get('edit_zone_id')}}" name="zone_id" id="zone_id">
										@else
											<form id="add_zone_form" method="POST" action="/Admin/SuperAdmin.createZone" class="form-horizontal">
										@endif
												{!! Form::token() !!}
												<div class="form-body">
													<div class="form-group">
														<label class="col-md-3 control-label">Zone Code</label>
														<div class="col-md-6">
															<div class="err">
																@if(Session::has('edit_zone_code'))
																	<input type="hidden" min='0' name="zone_code_old" value="{{ Session::get('edit_zone_code') }}">
																	<input type="number" min='0' id="zone_code" name="zone_code" value="{{ Session::get('edit_zone_code') }}" class="form-control" placeholder="Enter Code (e.g. 2)">
																@else
																	<input type="number" min='0' id="zone_code" name="zone_code" class="form-control" placeholder="Enter Code (e.g. 2)">
																@endif
															</div>
														</div>
													</div>
													<div class="form-group">
														<label class="col-md-3 control-label">Zone Name</label>
														<div class="col-md-6">
															<div class="err">
																@if(Session::has('edit_zone_name'))
																	<input type="hidden" id="zone_name" name="zone_name_old" value="{{ Session::get('edit_zone_name') }}">
																	<input type="text" id="zone_name" name="zone_name" value="{{ Session::get('edit_zone_name') }}" class="form-control" placeholder="Enter Name without spaces"><!-- pattern="^\S+$" -->
																@else
																	<input type="text" id="zone_name" name="zone_name" message="Spaces not allowed" class="form-control" placeholder="Enter Name without spaces"><!-- pattern="^\S+$" -->
																@endif
															</div>
														</div>
													</div>
													<div class="form-group">
														<label class="col-md-3 control-label">Zone Controller</label>
														<div class="col-md-6">
															<div class="err">
																@if(Session::has('edit_zone_controller'))
																<input type="hidden" id="zone_controller" name="zone_controller_old" pattern="^\S+$" value="{{ Session::get('edit_zone_controller') }}" class="form-control" placeholder="Enter Controller">
																	<input type="text" id="zone_controller" name="zone_controller" pattern="^\S+$" value="{{ Session::get('edit_zone_controller') }}" class="form-control" placeholder="Enter Controller">
																@else
																	<input type="text" id="zone_controller" name="zone_controller" pattern="^\S+$" class="form-control" placeholder="Enter Controller">
																@endif
															</div>
														</div>
													</div>
													<div class="form-group">
														<label class="col-md-3 control-label">Zone Description</label>
														<div class="col-md-6">
															<div class="err">
																@if(Session::has('edit_zone_description'))
																	<textarea id="zone_description" name="zone_description" class="form-control" placeholder="Enter Description">{{ Session::get('edit_zone_description') }}</textarea>
																@else
																	<textarea id="zone_description" name="zone_description" class="form-control" placeholder="Enter Description"></textarea>
																@endif
															</div>
														</div>
													</div>
													<div class="form-actions">
														<div class="row">
															<div class="col-md-offset-3 col-md-4">
																<button type="submit" class="btn green">Submit</button>
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
        {{ Html::script('admin/assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}
        {{ Html::script('admin/assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}
        {{ Html::script('admin/assets/global/plugins/select2/js/select2.full.min.js') }}
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN CUSTOM SCRIPT -->
	        <script type="text/javascript">
	            var AddZone = function() {
	                var handleAddZone = function() {
	                    $('#add_zone_form').validate({
	                        errorElement: 'span', //default input error message container
	                        errorClass: 'help-block', // default input error message class
	                        focusInvalid: false, // do not focus the last invalid input
	                        rules: {
	                            zone_code: {
	                                required: true
	                            },
	                            zone_controller: {
	                                required: true
	                            },
	                            zone_name: {
	                                required: true
	                            }
	                        },

	                        messages: {
	                            zone_code: {
	                                required: "This field is required. Only Digits"
	                            },
	                            zone_controller: {
	                                required: "This field is required."
	                            },
	                            zone_name: {
	                                required: "This field is required. Spaces are not allowed in Zone Name"
	                            }
	                        },

	                        invalidHandler: function(event, validator) { //display error alert on form submit   
	                            $('.alert-danger', $('add_zone_form')).show();
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

	                    $('#add_zone_form input').on('keypress', 'change', function(e) {
	                        if (e.which == 13) {
	                            if ($('#add_zone_form').validate().form()) {
	                                $('#add_zone_form').submit(); //form validation success, call ajax form submit
	                            }
	                            return false;
	                        }
	                    });
	                }

	                return {
	                    //main function to initiate the module
	                    init: function() {
	                        handleAddZone();
	                    }
	                };
	            }();

	            jQuery(document).ready(function() {
	                AddZone.init();
	                $('#li_zone_management').addClass('active');
	            });
	        </script>
	    <!--END CUSTOM SCRIPT -->
    </body>
</html>