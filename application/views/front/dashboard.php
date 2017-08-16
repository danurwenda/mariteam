<!-- Page-specific CSS -->
<?php echo css_asset('bootstrap-calendar/css/calendar.min.css'); ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    function loadJS(){
        var script = document.createElement('script');
                    script.src = '<?php echo base_url('dist/js/dashboard.js') ?>';
                    script.async = false;// <-- important
                    document.head.appendChild(script);
    }
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(loadJS)
</script>
<script type="text/javascript">
    $$.push(
            '<?php echo js_asset_url('underscore/underscore-min.js') ?>',
            '<?php echo js_asset_url('bootstrap-calendar/js/calendar.min.js') ?>',
            //'<?php echo base_url('dist/js/dashboard.js') ?>',
            );
</script>
    
<div class='center-block row' id="piechart" ></div>
<h2>Calendar</h2>
<div class='row' id='calendar'></div>
