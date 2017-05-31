<div class="page-header-menu">
                <div class="container">
                    <!-- BEGIN MEGA MENU -->
                    <!-- DOC: Apply "hor-menu-light" class after the "hor-menu" class below to have a horizontal menu with white background -->
                    <!-- DOC: Remove data-hover="dropdown" and data-close-others="true" attributes below to disable the dropdown opening on mouse hover -->
                    <div class="hor-menu ">
                        <ul class="nav navbar-nav">
                            <li id="li_admin_management" class="menu-dropdown classic-menu-dropdown">
                                <a href="/Admin/SuperAdminDashboard"> Admin Management
                                </a>
                            </li>
                            <li id="li_zone_management" class="menu-dropdown mega-menu-dropdown">
                                <a href="/Admin/SuperAdminZone"> Zone Management
                                </a>
                            </li>
                        </ul>
                    </div>

                    @if(Session::has('token_mismatch_message'))
                        <div class="alert alert-danger">
                            {{ Session::get('token_mismatch_message') }}
                            <span class="close close-alert" id="close-msg" data-dismiss="alert" aria-hidden="true">x</span>
                        </div>
                    @endif
                    <!-- END MEGA MENU -->
                </div>
            </div>