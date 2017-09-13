<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="A project management tools collection for Coordinating Ministry for Maritime Affairs">
        <meta name="author" content="Slurp">
        <link rel="icon" type="image/png" href="<?php echo base_url('image.png');?>" />

        <title>MariTeam | Admin Panel</title>

        <!-- Bootstrap Core CSS -->
        <?php echo css_asset('bootstrap/css/bootstrap.css'); ?>

        <!-- MetisMenu CSS -->
        <?php echo css_asset('metisMenu/metisMenu.min.css'); ?>
        
        <!-- Noty -->
        <?php echo css_asset('noty/noty.css'); ?>

        <!-- Custom CSS -->
        <link href="<?php echo base_url(); ?>/dist/css/sb-admin-2.css" rel="stylesheet">
        <style>
            body {
                padding-top: 60px;
                /* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
            }
            @media only screen and (min-width: 768px) {
                body{
                    padding-top: 30px;
                }
            }
        </style>
        <!-- Custom Fonts -->
        <?php echo css_asset('font-awesome/css/font-awesome.min.css'); ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script>var $$ = [], base_url = '<?php echo base_url(); ?>';</script>
    </head>

    <body>

        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo site_url(); ?>">MariTeam <i class="fa fa-group"></i></a>
                </div>
                <!-- /.navbar-header -->

                <ul class="nav navbar-top-links navbar-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li>
                                <i><?php echo $_loggeduser->person_name; ?></i>
                            </li>
                            <li>
                                <a href="<?php echo site_url('user/profile'); ?>"><i class="fa fa-user fa-fw"></i> User Profile</a>
                            </li>

                            <li class="divider"></li>
                            <li><a href="<?php echo site_url('auth/logout'); ?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                            </li>
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                    <!-- /.dropdown -->
                </ul>
                <!-- /.navbar-top-links -->

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                            <li class="sidebar-search">
                                <!--div class="input-group custom-search-form">
                                    <input id="searchBox" type="text" class="form-control" placeholder="Search..." onkeypress="return runScript(event)">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div-->
                                <!-- /input-group -->
                            </li>
                            <li>
                                <a href="<?php echo site_url('dashboard'); ?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                            </li>
                            <!-- dynamic menus -->
                            <?php
                            foreach ($menus as $menu) {
                                echo '<li>' . anchor(strtolower($menu->name), '<i class="fa fa-' . $menu->icon . ' fa-fw"></i> ' . ucfirst($menu->name)) . '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
                <!-- /.navbar-static-side -->
            </nav>

            <!-- Page Content -->
            <div id="page-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header"><?php echo $pagetitle; ?></h1>
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>
                    <!-- /.row -->
                    <div class="row">
                        <?php echo $_content; ?>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /#page-wrapper -->

        </div>
        <!-- /#wrapper -->

        <!-- jQuery -->
        <?php echo js_asset('jquery/jquery.min.js'); ?>

        <!-- Noty -->
        <?php echo js_asset('noty/noty.min.js'); ?>
        
        <!-- Bootstrap Core JavaScript -->
        <?php echo js_asset('bootstrap/js/bootstrap.min.js'); ?>

        <!-- Metis Menu Plugin JavaScript -->
        <?php echo js_asset('metisMenu/metisMenu.min.js'); ?>
        <?php echo js_asset('moment/moment.min.js'); ?>

        <!-- Page-specific, jQuery dependent JS -->
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
        <!-- Custom Theme JavaScript -->
        <script src="<?php echo base_url(); ?>/dist/js/sb-admin-2.js"></script>
    </body>

</html>
