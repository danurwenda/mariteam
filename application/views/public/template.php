<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MariTeam | Maritim Project Management Tools</title>
        <meta name="description" content="A project management tools collection for Coordinating Ministry for Maritime Affairs">
        <link rel="icon" type="image/png" href="<?php echo base_url('image.png'); ?>" />
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:400,300|Raleway:300,400,900,700italic,700,300,600">
        <!--<link rel="stylesheet" type="text/css" href="css/jquery.bxslider.css">-->
        <?php echo css_asset('font-awesome/css/font-awesome.min.css'); ?>
        <?php echo css_asset('bootstrap/css/bootstrap.min.css'); ?>
        <!-- Noty -->
        <?php echo css_asset('noty/noty.css'); ?>
        <link href="<?php echo base_url(); ?>/dist/css/public/style.css" rel="stylesheet">
        <!-- =======================================================
            Theme Name: Baker
            Theme URL: https://bootstrapmade.com/baker-free-onepage-bootstrap-theme/
            Author: BootstrapMade.com
            Author URL: https://bootstrapmade.com
        ======================================================= -->
        <script>var $$ = [], base_url = '<?php echo base_url(); ?>';</script>
    </head>
    <body>

        <div class="loader"></div>
        <div id="myDiv">
            <!--HEADER-->
            <div class="header">
                <div class="bg-color">
                    <header id="main-header">
                        <nav class="navbar navbar-default navbar-fixed-top">
                            <div class="container">
                                <div class="navbar-header">
                                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button>
                                    <a class="navbar-brand" href="<?php echo site_url(); ?>">
                                        Mari<span class="logo-dec">team</span>
                                        <div style="clear:both"></div>
                                        <span class="subs">Maritim Project Management Tools</span>
                                    </a>
                                </div>
                                <div class="collapse navbar-collapse" id="myNavbar">
                                    <ul class="nav navbar-nav navbar-right">
                                        <li class="<?php echo $page == 'home' ? 'active' : ''; ?>"><a href="<?php echo site_url(); ?>">Home</a></li>
                                        <li class="<?php echo $page == 'projects' ? 'active' : ''; ?>"><a href="<?php echo site_url('publik/projects'); ?>">Projects</a></li>
                                        <!--<li class="<?php echo $page == 'events' ? 'active' : ''; ?>"><a href="<?php echo site_url('publik/events'); ?>">Events</a></li>-->
                                        <li><a href="<?php echo site_url('dashboard'); ?>">Login</a></li>
                                    </ul>
                                </div>
                            </div>
                        </nav>
                    </header>
                </div>
            </div>
            <!--/ HEADER-->
            <!-- Page Content -->
            <section id="content" class="section-padding wow fadeIn delay-05s animated" style="visibility: visible; animation-name: fadeIn;">
                <div class="container">
                    <div class="row">
                        <?php echo $_content; ?>
                    </div>
                </div>
            </section>
            <!-- /#page-wrapper -->
            <!---->
            <footer id="footer">
                <div class="container">
                    <div class="row text-center">
                        <p>&copy; Baker Theme & Dimas Danurwenda. All Rights Reserved.</p>
                        <div class="credits">
                            <!-- 
                                All the links in the footer should remain intact. 
                                You can delete the links only if you purchased the pro version.
                                Licensing information: https://bootstrapmade.com/license/
                                Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/buy/?theme=Baker
                            -->
                            Designed  by <a href="https://bootstrapmade.com/">Bootstrap Themes</a>
                        </div>
                    </div>
                </div>
            </footer>
            <!---->
        </div>
        <!-- jQuery -->
        <?php echo js_asset('jquery/jquery.min.js'); ?>
        <!-- Noty -->
        <?php echo js_asset('noty/noty.min.js'); ?>
        <!-- easing -->
        <?php echo js_asset('jquery-ui/ui/effect.js'); ?>
        <!-- Bootstrap Core JavaScript -->
        <?php echo js_asset('bootstrap/js/bootstrap.min.js'); ?>
        <!-- Moment.js -->
        <?php echo js_asset('moment/moment.min.js'); ?>
        <!-- Page-specific, jQuery dependent JS -->
        <script>
            $(document).ready(function () {
                $$.forEach(function (src) {
                    var script = document.createElement('script');
                    script.src = src;
                    script.async = false;// <-- important
                    document.head.appendChild(script);
                });
            })
        </script>
        <!-- Custom Theme JavaScript -->
        <script src="<?php echo base_url(); ?>/dist/js/public/custom.js"></script>

    </body>
</html>