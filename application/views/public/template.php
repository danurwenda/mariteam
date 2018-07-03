<!DOCTYPE html>
<html lang="en">
    <head>
        <base target="_parent">
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
        <link href="<?php echo base_url(); ?>/dist/css/public/custom.css" rel="stylesheet">
        <!-- =======================================================
            Theme Name: Baker
            Theme URL: https://bootstrapmade.com/baker-free-onepage-bootstrap-theme/
            Author: BootstrapMade.com
            Author URL: https://bootstrapmade.com
        ======================================================= -->
        <script>var $$ = [], base_url = '<?php echo base_url(); ?>'; parent_url = '<?php echo $this->config->item('parent_url'); ?>';</script>
    </head>
    <body>

        <div class="loader"></div>
        <div id="myDiv">
        
            <!-- Page Content -->
            <section id="content" class="wow fadeIn delay-05s animated" style="visibility: visible; animation-name: fadeIn;">
                <div><!-- don't set this div with .container class since it will be loaded inside an iframe-->
                    <div class="row">
                        <?php echo $_content; ?>
                    </div>
                </div>
            </section>
            <!-- /#content -->
           
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