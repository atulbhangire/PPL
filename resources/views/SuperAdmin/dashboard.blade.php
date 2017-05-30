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
        {{ Html::style('admin/assets/global/plugins/datatables/datatables.min.css') }}
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
                                    <div class="portlet box green">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>
                                                Admin Listing
                                            </div>
                                        </div>
                                        <div class="portlet-body flip-scroll">
                                            <table id="adminTable" class="table table-bordered table-striped table-condensed flip-content">
                                                <thead class="flip-content">
                                                    <tr>
                                                        <th> Username </th>
                                                        <th> Last Password Changed </th>
                                                        <th> Change Password </th>
                                                        <th> Permissible IP </th>
                                                        <th> Permissible Days </th>
                                                        <th class="no-sort"> Reset MFA Key </th>
                                                        <th class="no-sort"> Edit </th>
                                                        <th class="no-sort"> Delete </th>
                                                        <th class="none"> Login IP </th>
                                                        <th class="none"> Status </th>
                                                        <th class="none"> Permissible Timerange </th>
                                                        <th class="none"> Created At </th>
                                                        <th class="none"> Updated At </th>
                                                        <th class="none"> Permissible Zone </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                     @foreach ($admin_users as $admin_user)
                                                        <tr>
                                                            <td>{{ $admin_user->adm_username }}</td>
                                                            <td>{{ $admin_user->last_password_changed }}</td>
                                                            <td>{{ $admin_user->change_password }} days</td>
                                                            <td>{{ $admin_user->permissible_ip }}</td>
                                                            <td>{{ $admin_user->permissible_days }}</td>
                                                            <td>
                                                                <a data-toggle="modal" href="#resetAdmin{{ $admin_user->adm_user_id }}">Reset</a>
                                                                <div class="modal fade" id="resetAdmin{{ $admin_user->adm_user_id }}" tabindex="-1" role="basic" aria-hidden="true" style="display: none;">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                                                <h4 class="modal-title">Reset MFA key?</h4>
                                                                            </div>
                                                                            <div class="modal-body"> Do you want reset MFA key for {{ $admin_user->adm_username }}? </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                                                                                <a class="btn green" href='/Admin/SuperAdmin.resetAdmin/{{ $admin_user->adm_user_id }}'>Yes</a>
                                                                            </div>
                                                                        </div>
                                                                    <!-- /.modal-content -->
                                                                    </div>
                                                                <!-- /.modal-dialog -->
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a href='/Admin/SuperAdmin.editAdmin/{{ $admin_user->adm_user_id }}'>Edit</a>
                                                            </td>
                                                            <td>
                                                                <a data-toggle="modal" href="#deleteAdmin{{ $admin_user->adm_user_id }}">Delete</a>
                                                                <div class="modal fade" id="deleteAdmin{{ $admin_user->adm_user_id }}" tabindex="-1" role="basic" aria-hidden="true" style="display: none;">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                                                <h4 class="modal-title">Delete record?</h4>
                                                                            </div>
                                                                            <div class="modal-body"> Do you want delete {{ $admin_user->adm_username }}? </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                                                                                <a class="btn green" href='/Admin/SuperAdmin.deleteAdmin/{{ $admin_user->adm_user_id }}'>Yes</a>
                                                                            </div>
                                                                        </div>
                                                                    <!-- /.modal-content -->
                                                                    </div>
                                                                <!-- /.modal-dialog -->
                                                                </div>
                                                            </td>
                                                            <td>{{ $admin_user->login_ip }}</td>
                                                            <td>{{ $admin_user->is_active ? "Active" : "Inactive" }}</td>
                                                            <td>{{ $admin_user->permissible_timerange }}</td>
                                                            <td>{{ $admin_user->created_at }}</td>
                                                            <td>{{ $admin_user->updated_at }}</td>
                                                            <td>
                                                            <?php
                                                                $cnt = 1;
                                                                foreach($admin_zones as $zone)
                                                                {
                                                                    $column_name = 'Zone_' . $zone->zn_zone_code;
                                                                    if($admin_user->$column_name)
                                                                    {
                                                                        echo "<BR/>" . $cnt . ") " . $zone->zn_name . " - " . $zone->zn_description;
                                                                        $cnt++;
                                                                    }
                                                                }
                                                            ?>
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
        {{ Html::script('admin/assets/global/plugins/datatables/datatables.min.js') }}
        {{ Html::script('admin/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}
        {{ Html::script('admin/assets/pages/scripts/table-datatables-responsive.min.js') }}
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