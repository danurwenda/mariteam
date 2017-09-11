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



<div id="event-modal-form" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="blue bigger">Event Detail</h4>
            </div>
            <div class="modal-body">
                <!-- label and text only, non-editable -->    
                <dl>
                    <dt>Event Name</dt>
                    <dd class="event-name"></dd>

                    <dt>Description</dt>
                    <dd class="event-description"></dd>

                    <dt>PIC</dt>
                    <dd class="event-pic"></dd>
                    <dt>Location</dt>
                    <dd class="event-location"></dd>


                    <dt>Time</dt>
                    <dd>
                        <span class="event-start"></span> to <span class="event-end"></span>
                    </dd>

                    <div class="projects">
                        <dt>Related Projects</dt>
                        <dd>
                            <ul class="event-projects" style="list-style: circle;">

                            </ul>
                        </dd>
                    </div>

                </dl>
            </div>
        </div>
    </div>
</div>