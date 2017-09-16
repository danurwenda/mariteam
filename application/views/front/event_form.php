<!-- Page-specific CSS -->
<?php echo css_asset('eonasdan-bootstrap-datetimepicker/bootstrap-datetimepicker.min.css'); ?>
<?php echo css_asset('select2/select2.min.css'); ?>
<?php echo css_asset('select2/themes/select2-bootstrap.min.css'); ?>
<?php echo css_asset('fine-uploader/fine-uploader-new.min.css'); ?>
<?php echo css_asset('datatables-responsive/responsive.dataTables.css'); ?>
<script>
    $$.push(
            '<?php echo js_asset_url('datatables/js/jquery.dataTables.min.js') ?>'
            , '<?php echo js_asset_url('datatables/js/dataTables.bootstrap.min.js') ?>'
            , '<?php echo js_asset_url('datatables-responsive/dataTables.responsive.js') ?>'
            , '<?php echo js_asset_url('moment/moment.min.js') ?>'
            , '<?php echo js_asset_url('eonasdan-bootstrap-datetimepicker/bootstrap-datetimepicker.min.js') ?>'
            , '<?php echo js_asset_url('select2/select2.min.js') ?>'
            , '<?php echo js_asset_url('jquery-validation/jquery.validate.min.js') ?>'
            , '<?php echo js_asset_url('bootbox/bootbox.js') ?>'
            , '<?php echo js_asset_url('fine-uploader/jquery.fine-uploader.min.js') ?>'
            , '<?php echo js_asset_url('bootstrap-wysiwyg/bootstrap-wysiwyg.min.js') ?>'
            , '<?php echo base_url('dist/js/event-form.js') ?>');
</script>
<?php if ($admin) { ?>
    <form action="<?php echo site_url('people/create_user_simple'); ?>" class="row form-horizontal hide new-person-form" method="post" accept-charset="utf-8">
        <div class="col-xs-12">
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Name </label>

                <div class="col-sm-9">
                    <input required minlength="5" type="text" placeholder="Person name" class="form-control" name="person_name">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Institution </label>

                <div class="col-sm-9">
                    <input required minlength="3" type="text" placeholder="Institution/Company" class="form-control" name="institusi">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Position </label>

                <div class="col-sm-9">
                    <input required minlength="3" type="text" placeholder="Position" class="form-control" name="jabatan">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-9 col-sm-offset-3">
                    <input type="checkbox" name="is_user"> Create user account for this person?
                </div>
            </div>
            <div class="form-group" id="new-person-form-email" style="display: none">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Email </label>

                <div class="col-sm-9">
                    <input type="email" placeholder="Email" class="form-control" name="email">
                </div>
            </div>
        </div>
    </form>

<?php } ?>

<div class="col-lg-12">
    <div class="panel panel-default main-panel" data-event="<?php echo isset($event) ? $event->event_id : null; ?>">
        <div class="panel-heading">
            Event Information
        </div>
        <div class="panel-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#basic" data-toggle="tab">Basic</a>
                </li>
                <?php if (isset($event)) { ?>
                    <li><a href="#materials" data-toggle="tab">Materials</a>                    </li>
                    <li><a href="#reports" data-toggle="tab">Reports</a>
                    </li>                    
                <?php } ?>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="basic">
                    <?php
                    if ($admin) {
                        echo form_open(isset($event) ? 'event/update' : 'event/submit', ['id' => 'event_form'], isset($event) ? ['event_id' => $event->event_id] : []);
                        ?>
                        <div class="row">
                            <?php if (isset($updated) && $updated) { ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    Event updated.
                                </div>
                            <?php }
                            ?>
                            <div class="col-lg-6">    
                                <div class="form-group">
                                    <label>Name</label>
                                    <input required minlength="5" name="name" type="text" class="form-control" value="<?php echo set_value('name', isset($event) ? $event->event_name : ''); ?>">
                                </div>
                                <div class="form-group ">
                                    <label for="description">Description</label>
                                    <textarea class="form-control " name="description" cols="50" rows="10" id="description"><?php echo set_value('description', isset($event) ? $event->description : ''); ?></textarea>
                                </div>  


                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>PIC</label>
                                    <div class="input-group">
                                        <?php
                                        $options = [];
                                        foreach ($users as $u) {
                                            $options[$u->person_id] = $u->person_name;
                                        }

                                        $js = 'required id = "event_assign_to" class="form-control select2"'
                                        ;
                                        echo form_dropdown(
                                                'pic', $options, set_value('pic', isset($event) ? $event->pic : null
                                                )
                                                , $js);
                                        ?>
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm btn-default add-person-btn" type="button" >
                                                <span class="glyphicon glyphicon-plus-sign"></span>
                                            </button>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="event_date">Time</label>
                                    <div class="input-daterange input-group">
                                        <input required type="text" class="input-sm form-control" name="start_time" id="event_start_time" value="<?php echo set_value('start_time', isset($event) ? date_format(date_create($event->start_time), "d-F-Y H:i") : ''); ?>">
                                        <span class="input-group-addon">
                                            <i class="fa fa-exchange"></i>
                                        </span>

                                        <input required type="text" class="input-sm form-control" name="end_time" id="event_end_time" value="<?php echo set_value('end_time', isset($event) ? date_format(date_create($event->end_time), "d-F-Y H:i") : ''); ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Location</label>
                                    <input required name="location" type="text" class="form-control" value="<?php echo set_value('location', isset($event) ? $event->location : ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="multi-append" class="control-label">Linked Projects</label>
                                    <?php
                                    $js = [
                                        'id' => 'linked-projects',
                                        'class' => 'form-control select2'
                                    ];
                                    echo form_multiselect('projects[]', isset($event) ? $event->projects : [], set_value('projects[]', isset($event) ? $event->project_ids : null ), $js);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($admin) { ?>
                            <button type="submit" class="btn btn-default">Submit</button>
                            <?php if (isset($event)) { ?>
                                <a class="btn btn-danger" data-event_name="<?php echo $event->event_name; ?>" data-event_id="<?php echo $event->event_id; ?>">Remove</a>
                                <?php
                            }
                        }
                        ?>
                        </form>
                    <?php } else { ?>
                        <!-- label and text only, non-editable -->    
                        <dl>
                            <dt>Event Name</dt>
                            <dd><?php echo $event->event_name; ?></dd>

                            <dt>Description</dt>
                            <dd><?php echo $event->description; ?></dd>

                            <dt>PIC</dt>
                            <dd><?php echo $event->person_name; ?></dd>
                            <dt>Location</dt>
                            <dd><?php echo $event->location; ?></dd>


                            <dt>Time</dt>
                            <dd>
                                <span id="event-start-date"><?php echo date_format(date_create($event->start_time), "d-F-Y H:i"); ?> to <?php echo date_format(date_create($event->end_time), "d-F-Y H:i"); ?></span>
                            </dd>

                            <dt>Submitted on</dt>
                            <dd><?php echo date_format(date_create($event->created_at), "d-F-Y H:i"); ?></dd>

                        </dl>
                    <?php } ?>
                </div>
                <?php if (isset($event)) { ?>
                    <div class="tab-pane fade" id="materials">
                        <div class='row'>
                            <div class='col-lg-12'>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Reports
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <table width="100%" class="table table-striped table-bordered table-hover event-docs-table" data-type="materials">
                                            <thead>
                                                <tr>
                                                    <th>File name</th>
                                                    <th>Size</th>
                                                    <th>Uploaded</th>
                                                    <th>Remove</th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <!-- /.table-responsive -->
                                    </div>
                                    <!-- /.panel-body -->
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-lg-12'>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Upload Document
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <div class="uploaderz" data-event=<?php echo $event->event_id; ?>></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="reports">
                        <div class='row'>
                            <div class='col-lg-12'>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Reports
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <table width="100%" class="table table-striped table-bordered table-hover event-docs-table" data-type="reports">
                                            <thead>
                                                <tr>
                                                    <th>File name</th>
                                                    <th>Size</th>
                                                    <th>Uploaded</th>
                                                    <th>Remove</th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <!-- /.table-responsive -->
                                    </div>
                                    <!-- /.panel-body -->
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-lg-12'>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Upload Document
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <div class="uploaderz" data-event=<?php echo $event->event_id; ?>></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
</div>
<!-- /.col-lg-12 -->
<?php if (isset($event)) { ?>
    <!-- Fine Uploader Thumbnails template w/ customization
    ====================================================================== -->
    <script type="text/template" id="qq-template-manual-trigger">
        <div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop files here">
        <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
        </div>
        <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
        <span class="qq-upload-drop-area-text-selector"></span>
        </div>
        <div class="buttons">
        <div class="qq-upload-button-selector qq-upload-button">
        <div>Select files</div>
        </div>
        <button type="button" class="btn btn-primary trigger-upload btn-minier">
        <i class="icon-upload icon-white"></i> Upload
        </button>
        </div>
        <span class="qq-drop-processing-selector qq-drop-processing">
        <span>Processing dropped files...</span>
        <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
        </span>
        <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
        <li>
        <div class="qq-progress-bar-container-selector">
        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
        </div>
        <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
        <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
        <span class="qq-upload-file-selector qq-upload-file"></span>
        <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
        <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
        <span class="qq-upload-size-selector qq-upload-size"></span>
        <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
        <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>
        <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button>
        <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
        </li>
        </ul>

        <dialog class="qq-alert-dialog-selector">
        <div class="qq-dialog-message-selector"></div>
        <div class="qq-dialog-buttons">
        <button type="button" class="qq-cancel-button-selector">Close</button>
        </div>
        </dialog>

        <dialog class="qq-confirm-dialog-selector">
        <div class="qq-dialog-message-selector"></div>
        <div class="qq-dialog-buttons">
        <button type="button" class="qq-cancel-button-selector">No</button>
        <button type="button" class="qq-ok-button-selector">Yes</button>
        </div>
        </dialog>

        <dialog class="qq-prompt-dialog-selector">
        <div class="qq-dialog-message-selector"></div>
        <input type="text">
        <div class="qq-dialog-buttons">
        <button type="button" class="qq-cancel-button-selector">Cancel</button>
        <button type="button" class="qq-ok-button-selector">Ok</button>
        </div>
        </dialog>
        </div>
    </script>
    <style>
        .trigger-upload {
            color: white;
            background-color: #00ABC7;
            font-size: 14px;
            padding: 7px 20px;
            background-image: none;
        }

        #fine-uploader-manual-trigger .qq-upload-button {
            margin-right: 15px;
        }

        #fine-uploader-manual-trigger .buttons {
            width: 36%;
        }

        #fine-uploader-manual-trigger .qq-uploader .qq-total-progress-bar-container {
            width: 60%;
        }
        .deldocbtn{
            cursor: pointer;
            color:red;
            margin-left:10px;
        }
    </style>

    <?php
}?>