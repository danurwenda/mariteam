<!-- Page-specific CSS -->
<?php
echo css_asset('fullcalendar/fullcalendar.min.css');
echo css_asset('fullcalendar/fullcalendar.print.min.css', null, ['media' => 'print']);
echo js_asset('highcharts/highcharts.js');
echo js_asset('highcharts/highcharts-3d.js');
?>
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
<div class="row">
    <div class="col-sm-6" id="piechart" style="min-height: 300px">
    </div>

    <div class="col-sm-6" id="columnchart" style="min-height: 300px">

    </div>
</div>
<script>
    Highcharts.chart('piechart', {
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Projects by Status'
        },
        tooltip: {
            pointFormat: '{point.y}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                colors:['#a9ff96','#95ceff','#990000'],
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
                type: 'pie',
                data: <?php echo json_encode($project_status);?>
            }]
    });
    Highcharts.chart('columnchart', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Projects by Deputy'
        },
        subtitle: {
            text: '2017-2018'
        },
        xAxis: {
            categories: [
                '2017-2018'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Projects'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-weight:bold;;padding:0">{series.name}</span><table>',
            pointFormat: '<tr><td style="padding:0">#Projects: </td>' +
                    '<td style="padding:0"><b>{point.y}</b></td></tr>',
            footerFormat: '</table>',
            shared: false,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: <?php echo json_encode($project_deps);?>
    });
</script>
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