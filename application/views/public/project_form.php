<!-- Page-specific CSS -->
<?php echo css_asset('datatables-plugins/dataTables.bootstrap.css'); ?>
<?php echo css_asset('datatables-responsive/responsive.dataTables.css'); ?>
<script>
    var ge;
    $$.push(
            '<?php echo js_asset_url('datatables/js/jquery.dataTables.min.js') ?>'
            , '<?php echo js_asset_url('datatables/js/dataTables.bootstrap.min.js') ?>'
            , '<?php echo js_asset_url('datatables-responsive/dataTables.responsive.js') ?>'
            , '<?php echo js_asset_url('moment/moment.min.js') ?>'

            , '<?php echo base_url('dist/js/public/project-form.js') ?>');
    // send data to parent frame, only when this frame is fully loaded
    function load() {
        window.parent.postMessage(document.body.offsetHeight, '*');
    }
    window.onload = load;
</script>
<div class="col-lg-12">
    <button onclick="window.history.back();" class="btn btn-white btn-info btn-round">
        <i class="ace-icon fa fa-long-arrow-left"></i>
        Back
    </button>
    <a href="<?php echo $this->config->item('parent_url') . 'inquiry/?projectName=' . rawurlencode($project->project_name); ?>" style="float:right;" class="btn btn-white btn-warning btn-round">
        <i class="ace-icon fa fa-info-circle"></i>
        Inquiry for funding
    </a>
    <div class="help-block"></div>
    <div class="panel panel-default main-panel" data-project="<?php echo isset($project) ? $project->project_id : null; ?>">
        <div class="panel-heading">
            <b><?php echo $project->project_name; ?></b>
        </div>
        <div class="panel-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#basic" data-toggle="tab">Profile</a>
                </li>
                <li><a href="#task" data-toggle="tab">Tasks</a>
                </li>
                <li id="docs-tab"><a href="#documents" data-toggle="tab">Documents</a>
                </li>
                <li id="events-tab"><a href="#events" data-toggle="tab">Events</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="basic">
                    <dl>
                        <dt>Project</dt>
                        <dd><?php echo $project->project_name; ?></dd>

                        <dt>Project Owner</dt>
                        <dd><?php echo $project->owner; ?></dd>

                        <dt>Offtaker</dt>
                        <dd><?php echo $project->offtaker; ?></dd>

                        <dt>Project Description</dt>
                        <dd><?php echo $project->description; ?></dd>

                        <dt>Estimated Project Cost (USD)</dt>
                        <dd><?php echo number_format($project->cost, 0); ?></dd>

                        <dt>Indicative IRR</dt>
                        <dd><?php echo $project->IRR; ?></dd>

                        <dt>Latest Status</dt>
                        <dd><?php echo $project->latest_status; ?></dd>

                        <dt>SDG Goals</dt>
                        <dd>
                            <ul class="sdglist">
                                <?php

if (count($project->groups) > 0) {
    foreach ($project->groups as $id) {
        echo '<li class="sdg-' . ($id < 10 ? '0' . $id : $id) . '"></li>';
    }
} else {
    echo "<li>-</li>";
}
?>
                            </ul>
                        </dd>
                    </dl>
                </div>
                <div class="tab-pane fade" id="task">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Task List
                                </div>
                                <!-- /.panel-heading -->
                                <div class="panel-body">
                                    <table width="100%" class="table table-striped table-bordered table-hover" id="tasks-datatable" data-url="<?php echo site_url('publik/tasks_dt'); ?>">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Task</th>
                                                <th>Target</th>
                                                <th>Status</th>
                                                <th>Order</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <!-- /.table-responsive -->
                                </div>
                                <!-- /.panel-body -->
                            </div>
                            <!-- /.panel -->
                        </div>

                    </div>
                </div>
                <div class="tab-pane fade" id="documents">
                        <div class='row'>
                            <div class='col-xs-12'>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Document List
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <table width="100%" class="table table-striped table-bordered table-hover" id="docs-datatable" data-url="<?php echo site_url('publik/docs_dt'); ?>">
                                            <thead>
                                                <tr>
                                                    <th>File name</th>
                                                    <th>Size</th>
                                                    <th>Uploaded</th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <!-- /.table-responsive -->
                                    </div>
                                    <!-- /.panel-body -->
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="tab-pane fade" id="events">
                    <div class='row'>
                        <div class='col-lg-12'>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Related Events
                                </div>
                                <!-- /.panel-heading -->
                                <div class="panel-body">
                                    <table width="100%" class="table table-striped table-bordered table-hover" id="events-datatable">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>PIC</th>
                                                <th>Date & Time</th>
                                                <th>Location</th>
                                                <th>Description</th>
                                                <th>End</th>

                                            </tr>
                                        </thead>
                                    </table>
                                    <!-- /.table-responsive -->
                                </div>
                                <!-- /.panel-body -->
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->

    <!-- Fine Uploader Thumbnails template w/ customization
    ====================================================================== -->


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

                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div id="task-modal-form" class="modal " tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="blue bigger">Task</h4>
                </div>
                <div class="modal-body">
                    <div class='row'>
                        <dl class='col-sm-6'>
                            <dt>Task Name</dt>
                            <dd id="task-name"></dd>

                            <dt>PIC</dt>
                            <dd id="task-assign"></dd>

                            <dt>Status</dt>
                            <dd id="task-status"></dd>
                        </dl>
                        <dl class='col-sm-6'>
                            <dt>Start date</dt>
                            <dd>
                                <span id="task-start-date"></span>
                            </dd>
                            <dt>End date</dt>
                            <dd>
                                <span id="task-end-date"></span>
                                (<span id="task-due-date-remain"></span>)
                            </dd>

                            <dt>Weight</dt>
                            <dd id="task-weight"></dd>


                        </dl>
                    </div>
                    <div class='row'>
                        <dl class='col-sm-12'>
                            <dt>Description</dt>
                            <dd id="task-desc"></dd>
                        </dl>
                    </div>
                    <div class='row'>
                        <div class='col-sm-12'>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Uploaded Document
                                </div>
                                <!-- /.panel-heading -->
                                <div class="panel-body">
                                    <ul id="task-docs">

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="comment-panel panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-comments fa-fw"></i> Comments
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <ul class="chat"></ul>
                        </div>
                        <!-- /.panel-body -->
                        <div class="panel-footer">

                        </div>
                        <!-- /.panel-footer -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
