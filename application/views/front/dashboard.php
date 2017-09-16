<!-- Page-specific CSS -->
<?php echo css_asset('fullcalendar/fullcalendar.min.css'); ?>
<?php echo css_asset('fullcalendar/fullcalendar.print.min.css', null, ['media' => 'print']); ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    $$.push(
            '<?php echo js_asset_url('fullcalendar/fullcalendar.min.js') ?>'
             ,'<?php echo base_url('dist/js/dashboard.js') ?>'
            );
</script>
<style>
    .fc-content {
        cursor:pointer;
    }
</style>
<div class='center-block row' id="piechart" ></div>
<div class="col-md-12">

    <h2>Calendar</h2>
    <div id='calendar'></div>
</div>
