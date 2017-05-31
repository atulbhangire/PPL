<!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        {{ Html::style('admin/assets/font-awesome/css/font-awesome.min.css') }}
        {{ Html::style('admin/assets/css/bootstrap.min.css') }}
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <!-- {{ Html::style('admin/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }} -->
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        {{ Html::style('admin/assets/css/components.min.css') }}
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        {{ Html::style('admin/assets/layouts/layout3/css/layout.min.css') }}
        {{ Html::style('admin/assets/layouts/layout3/css/themes/default.min.css') }}
        {{ Html::style('admin/assets/layouts/layout3/css/custom.min.css') }}
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="{{{ asset('admin/favicon.ico') }}}" type="image/x-icon">
        <link rel="icon" href="{{{ asset('admin/favicon.ico') }}}" type="image/x-icon">
        <style type="text/css">
                .bott{
                    position:fixed;
                    bottom:0px;
                    left:0px;
                    right:0px;
                    margin-bottom:0px;
                    width: 100%;
                        z-index: 9;
                }
                body{
                    margin-bottom:50px;
                }
                .page-content{
                    margin-bottom:50px;
                }
        </style>