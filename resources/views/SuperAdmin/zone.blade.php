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
        <title>SPTulsian.com | Super Admin | Zone Management</title>
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
                        <!-- BEGIN PAGE CONTENT INNER -->
                        <div class="page-content-inner">
                            <div class="row">
                                <div class="col-md-12">                                    
                                    <a href="/Admin/SuperAdmin.AddZone">
                                        <button type="button" class="btn btn-default">
                                            <i class="fa fa-plus"></i>
                                            Add New Zone
                                        </button>
                                    </a>
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
                                    <!-- BEGIN ZONE TABLE PORTLET-->
                                    <div class="portlet box green">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>
                                                Zone Listing
                                            </div>
                                        </div>
                                        <div class="portlet-body flip-scroll">
                                         <table class="table table-striped table-hover table-bordered" id="myTable">
                                                <thead>
                                                    <tr>
                                                        <th class="all"> Code </th>
                                                        <th width="20%"> Name </th>
                                                        <th> Controller </th>
                                                        <th class="none"> Description </th>
                                                        <th class="numeric"> Created At </th>
                                                        <th class="numeric"> Updated At </th>
                                                        <th class="no-sort"> Edit </th>
                                                        <th class="no-sort"> Delete </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($admin_zones as $admin_zone)
                                                        <tr>
                                                            <td>{{ $admin_zone->zn_zone_code }}</td>
                                                            <td>{{ $admin_zone->zn_name }}</td>
                                                            <td>{{ $admin_zone->zn_controller }}</td>
                                                            <td>{{ $admin_zone->zn_description }}</td>
                                                            <td>{{ $admin_zone->created_at }}</td>
                                                            <td>{{ $admin_zone->updated_at }}</td>
                                                            <td>
                                                                <a class="edit" href="/Admin/SuperAdmin.updateZone/{{ $admin_zone->zn_id }}"> Edit </a>
                                                            </td>
                                                            <td>
                                                               <a data-toggle="modal" href="#deleteZone{{ $admin_zone->zn_id }}">Delete</a>
                                                               <div class="modal fade" id="deleteZone{{ $admin_zone->zn_id }}" tabindex="-1" role="basic" aria-hidden="true" style="display: none;">
                                                                   <div class="modal-dialog">
                                                                       <div class="modal-content">
                                                                           <div class="modal-header">
                                                                               <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                                               <h4 class="modal-title">Delete Zone?</h4>
                                                                           </div>
                                                                           <div class="modal-body"> Do you want delete {{ $admin_zone->zn_name }}? </div>
                                                                           <div class="modal-footer">
                                                                               <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                                                                               <button type="button" class="btn green" onclick="window.location.href='/Admin/SuperAdmin.deleteZone/{{ $admin_zone->zn_id }}'">Yes</button>
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
                                    <!-- END ZONE TABLE PORTLET-->
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
                $('#li_zone_management').addClass('active');
                 $('#myTable').DataTable( {
                    responsive: true,
                    "bPaginate": false,
                    columnDefs: [
                        { targets: 'no-sort', orderable: false }
                    ],
                    "aaSorting": []
                } );
            });
        </script>
        <!-- END CUSTOM SCRIPT -->
    </body>
</html>