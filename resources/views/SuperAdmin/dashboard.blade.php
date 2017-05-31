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
        {{ Html::style('admin/assets/css/datatables.min.css') }}
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
                                    <button type="button" class="btn btn-default" onclick="window.location.href='/Admin/SuperAdmin/AddAdmin'">
                                        <i class="fa fa-plus"></i>
                                        Add New Admin
                                    </button>
                                </div>
                            </div>
                            <BR/>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BEGIN ADMIN TABLE PORTLET-->
                                    <div class="portlet box blue">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>
                                                Admin Users Listing
                                            </div>
                                        </div>
                                        <div class="portlet-body flip-scroll">
                                            <table id="adminTable" class="table table-bordered table-striped table-condensed flip-content">
                                                <thead class="flip-content">
                                                    <tr>
                                                        <th> First Name </th>
                                                        <th> Last Name </th>
                                                        <th> Email </th>
                                                        <th> Contact No </th>
                                                        <th> Status </th>
                                                        <th> Admin Role </th>
                                                        <th> Created At </th>
                                                        <th> Updated At </th>
                                                        <th class="no-sort"> Edit </th>
                                                        <th class="no-sort"> Delete </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                     @foreach ($users as $admin_user)
                                                        <tr>
                                                            <td>{{ $admin_user->first_name }}</td>
                                                            <td>{{ $admin_user->last_name }}</td>
                                                            <td>{{ $admin_user->email }}</td>
                                                            <td>{{ $admin_user->contact_no }}</td>
                                                            <td>{{ $admin_user->is_active ? "Active" : "Inactive" }}</td>
                                                            
                                                            <td>{{ $admin_user->admin_role }}</td>
                                                            
                                                            <td>{{ $admin_user->created_at }}</td>
                                                            <td>{{ $admin_user->updated_at }}</td>
                                                            <td>
                                                                <a href='/Admin/SuperAdmin.editAdmin/{{ $admin_user->id }}'>Edit</a>
                                                            </td>
                                                            <td>
                                                                <a data-toggle="modal" href="#deleteAdmin{{ $admin_user->id }}">Delete</a>
                                                                <div class="modal fade" id="deleteAdmin{{ $admin_user->id }}" tabindex="-1" role="basic" aria-hidden="true" style="display: none;">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                                                <h4 class="modal-title">Delete record?</h4>
                                                                            </div>
                                                                            <div class="modal-body"> Do you want delete {{ $admin_user->email }}? </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                                                                                <a class="btn green" href='/Admin/SuperAdmin.deleteAdmin/{{ $admin_user->id }}'>Yes</a>
                                                                            </div>
                                                                        </div>
                                                                    <!-- /.modal-content -->
                                                                    </div>
                                                                <!-- /.modal-dialog -->
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- END ADMIN TABLE PORTLET-->
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
        {{ Html::script('admin/assets/js/datatables.min.js') }}
        {{ Html::script('admin/assets/js/datatables.bootstrap.js') }}
        {{ Html::script('admin/assets/js/table-datatables-responsive.min.js') }}
        <!-- BEGIN CUSTOM SCRIPT -->
        <script type="text/javascript">
            $(document).ready(function () {
                $('#li_admin_management').addClass('active');
                $('#adminTable').DataTable( {
                    responsive: true,
                    "bPaginate": false,
                    columnDefs: [
                        { targets: 'no-sort', orderable: false }
                    ],
                    "aaSorting": []
                });
            });
        </script>
        <!-- END CUSTOM SCRIPT -->
    </body>
</html>