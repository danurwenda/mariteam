<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta charset="utf-8" />
        <title>MariTeam | Admin Panel</title>

        <meta name="description" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

        <!-- bootstrap & fontawesome -->
        <?php echo css_asset('bootstrap/css/bootstrap.min.css'); ?>
        <?php echo css_asset('font-awesome/css/font-awesome.min.css'); ?>

        <!-- page specific plugin styles -->

        <!-- text fonts -->
        <?php echo css_asset("ace2/css/fonts.googleapis.com.css"); ?>
        <!-- ace styles -->
        <?php echo css_asset('ace2/css/ace.min.css', null, ['class' => "ace-main-stylesheet", 'id' => "main-ace-style"]); ?>
        <!--[if lte IE 9]>
        <?php echo css_asset('ace2/css/ace-part2.min.css', null, ['class' => "ace-main-stylesheet", 'id' => "main-ace-style"]); ?>
        <![endif]-->
        <?php echo css_asset("ace2/css/ace-skins.min.css"); ?>
        <?php echo css_asset("ace2/css/ace-rtl.min.css"); ?>

        <!--[if lte IE 9]>
        <?php echo css_asset("/ace-ie.min.css"); ?>
        <![endif]-->

        <!-- inline styles related to this page -->
        <link href="<?php echo base_url(); ?>/dist/css/ace-custom.css" rel="stylesheet">
        <!-- ace settings handler -->
        <?php echo js_asset('ace2/js/ace-extra.min.js'); ?>

        <?php
        if (!empty($js_assets)) {
            foreach ($js_assets as $value) {
                echo js_asset($value['asset'], $value['module']);
            }
        }
        ?>
        <script>var $$ = [], base_url = '<?php echo base_url(); ?>';</script>
    </head>

    <body class="no-skin">
        <div id="navbar" class="navbar navbar-default ace-save-state">
            <div class="navbar-container ace-save-state" id="navbar-container">
                <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
                    <span class="sr-only">Toggle sidebar</span>

                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>
                </button>

                <div class="navbar-header pull-left">
                    <!-- #section:basics/navbar.layout.brand -->
                    <a href="<?php echo site_url(); ?>" class="navbar-brand">
                        <small>
                            <i class="fa fa-users"></i>
                            MariTeam
                        </small>
                    </a>
                </div>

                <div class="navbar-buttons navbar-header pull-right" role="navigation">
                    <ul class="nav ace-nav">
                        <li class="light-blue dropdown-modal">
                            <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                            <!--<img class="nav-user-photo" src="../assets/avatars/user.jpg" alt="Jason's Photo" />-->
                                <span class="user-info">
                                    <small>Welcome,</small>
                                    <?php
                                    $names = explode(' ', $_loggeduser->person_name);
                                    echo $names[0];
                                    ?>
                                </span>

                                <i class="ace-icon fa fa-caret-down"></i>
                            </a>

                            <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">


                                <li>
                                    <a href="<?php echo site_url('user/profile'); ?>">
                                        <i class="ace-icon fa fa-user"></i>
                                        Profile
                                    </a>
                                </li>

                                <li class="divider"></li>

                                <li>
                                    <a href="<?php echo site_url('auth/logout'); ?>">
                                        <i class="ace-icon fa fa-power-off"></i>
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div><!-- /.navbar-container -->
        </div>

        <div class="main-container ace-save-state" id="main-container">
            <script type="text/javascript">
                try {
                    ace.settings.loadState('main-container')
                } catch (e) {
                }
            </script>

            <div id="sidebar" class="sidebar responsive ace-save-state">
                <script type="text/javascript">
                    try {
                        ace.settings.loadState('sidebar')
                    } catch (e) {
                    }
                </script>

                <div class="sidebar-shortcuts" id="sidebar-shortcuts">
                    <!--div class="nav-search" id="nav-search">
                        <form class="form-search">
                            <span class="input-icon">
                                <input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
                                <i class="ace-icon fa fa-search nav-search-icon"></i>
                            </span>
                        </form>
                    </div><!-- /.nav-search -->
                </div><!-- /.sidebar-shortcuts -->

                <ul class="nav nav-list">
                    <li class="<?php echo 0 == $active_menu ? 'active' : ''; ?>">
                        <a href="<?php echo site_url('dashboard'); ?>">
                            <i class="menu-icon fa fa-dashboard"></i>
                            <span class="menu-text"> Dashboard </span>
                        </a>

                        <b class="arrow"></b>
                    </li>
                    <!-- dynamic menus -->
                    <?php
                    foreach ($menus as $menu) {
                        echo '<li class="' . ($menu->module_id == $active_menu ? 'active' : '') . '">' . anchor(strtolower($menu->name), '<i class="menu-icon fa fa-' . $menu->icon . '"></i><span class="menu-text"> ' . ucfirst($menu->name)) . '</span></li>';
                    }
                    ?>
                </ul><!-- /.nav-list -->

                <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
                    <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
                </div>
            </div>

            <div class="main-content">
                <div class="main-content-inner">
                    <div class="breadcrumbs ace-save-state" id="breadcrumbs">

                    </div>

                    <div class="page-content">
                        <div class="row">
                            <div class="col-xs-12">
                                <!-- PAGE CONTENT BEGINS -->
                                <?php echo $_content; ?>
                                <!-- PAGE CONTENT ENDS -->
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.page-content -->
                </div>
            </div><!-- /.main-content -->

            <div class="footer">
                <div class="footer-inner">
                    <div class="footer-content">
                        <span class="bigger-120">
                            <span class="blue bolder">Ace</span>
                            Application &copy; 2013-2014
                        </span>

                        &nbsp; &nbsp;
                        <span class="action-buttons">
                            <a href="#">
                                <i class="ace-icon fa fa-twitter-square light-blue bigger-150"></i>
                            </a>

                            <a href="#">
                                <i class="ace-icon fa fa-facebook-square text-primary bigger-150"></i>
                            </a>

                            <a href="#">
                                <i class="ace-icon fa fa-rss-square orange bigger-150"></i>
                            </a>
                        </span>
                    </div>
                </div>
            </div>

            <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
                <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
            </a>
        </div><!-- /.main-container -->

        <!-- basic scripts -->

        <!--[if !IE]> -->
        <script type="text/javascript">
            window.jQuery || document.write("<script src='" + base_url + "vendor/jquery/jquery.min.js'>" + "<" + "/script>");
        </script>

        <!-- <![endif]-->

        <!--[if IE]>
        <?php echo js_asset("/jquery-1.11.3.min.js"); ?>
       <![endif]-->
        <script type="text/javascript">
            if ('ontouchstart' in document.documentElement)
                document.write("<script src='assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
        </script>
        <?php echo js_asset('bootstrap/js/bootstrap.min.js'); ?>

        <!-- page specific plugin scripts -->
        <?php echo js_asset('noty/noty.min.js'); ?>
        <?php echo js_asset('moment/moment.min.js'); ?>
        <!-- ace scripts -->
        <?php echo js_asset("ace2/js/ace-elements.min.js"); ?>
        <?php echo js_asset("ace2/js/ace.min.js"); ?>

        <!-- inline scripts related to this page -->
        <script>
            function doSearch() {
                var str = document.getElementById('searchBox').value;
                if (str !== '') {
                    location.href = base_url + 'dashboard/search/' + encodeURIComponent(str)
                }
            }
            function runScript(e) {
                if (e.keyCode === 13) {
                    doSearch()
                    return false;
                }
            }
            $(document).ready(function () {
                $$.forEach(function (src) {
                    var script = document.createElement('script');
                    script.src = src;
                    script.async = false;// <-- important
                    document.head.appendChild(script);
                });

                // search on lup logo after search box
                $('#searchBox').next().click(function () {
                    doSearch()
                });
            })
        </script>
    </body>
</html>
