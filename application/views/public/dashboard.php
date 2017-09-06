<!-- Page-specific CSS -->
<?php echo css_asset('fullcalendar/fullcalendar.min.css'); ?>
<?php echo css_asset('fullcalendar/fullcalendar.print.min.css', null, ['media' => 'print']); ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    $$.push(
            '<?php echo js_asset_url('fullcalendar/fullcalendar.min.js') ?>'
            , '<?php echo base_url('dist/js/public/dashboard.js') ?>'
            );
</script>

<div class='center-block row' id="piechart" ></div>
<div class="col-md-12">

    <h2>Calendar</h2>
    <div id='calendar'></div>
</div>
