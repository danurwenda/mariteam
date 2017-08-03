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
            , '<?php echo js_asset_url('jquery-knob/jquery.knob.min.js') ?>'
            , '<?php echo js_asset_url('jquery-validation/jquery.validate.min.js') ?>'
            , '<?php echo js_asset_url('bootbox/bootbox.js') ?>'
            , '<?php echo js_asset_url('fine-uploader/jquery.fine-uploader.min.js') ?>'
            , '<?php echo js_asset_url('bootstrap-wysiwyg/bootstrap-wysiwyg.min.js') ?>'
            , '<?php echo base_url('dist/js/project-form.js') ?>');
</script>
<div class="col-lg-12">
    <div class="panel panel-default main-panel" data-project="<?php echo isset($project) ? $project->project_id : null; ?>">
        <div class="panel-heading">
            Project Information
        </div>
        <div class="panel-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#basic" data-toggle="tab">Basic</a>
                </li>
                <?php if (isset($project)) { ?>
                    <li><a href="#task" data-toggle="tab">Task</a>
                    </li>
                    <li><a href="#documents" data-toggle="tab">Documents</a>
                    </li>
                <?php } ?>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="basic">
                    <?php
                    if ($admin) {
                        echo form_open(isset($project) ? 'project/update' : 'project/create', ['id' => 'project_form'], isset($project) ? ['project_id' => $project->project_id] : []);
                        ?>
                        <div class="row">
                            <?php if (isset($updated) && $updated) { ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    Project updated.
                                </div>
                            <?php }
                            ?>
                            <div class="col-lg-6">    
                                <div class="form-group">
                                    <label>Name</label>
                                    <input name="name" type="text" class="form-control" value="<?php echo set_value('name', isset($project) ? $project->project_name : ''); ?>">
                                    <?php echo form_error('name', '<div class="has-error"><label class="control-label">', '</label></div>'); ?>
                                </div>
                                <div class="form-group ">
                                    <label for="description">Description</label>
                                    <textarea class="form-control " name="description" cols="50" rows="10" id="description"><?php echo set_value('description', isset($project) ? $project->description : ''); ?></textarea>
                                </div>  
                                <div class="form-group ">
                                    <label for="project_date">Due Date</label>
                                    <input class="form-control datetimepicker" name="due_date" type="text" id="project_date" value="<?php echo set_value('due_date', isset($project) ? date_format(date_create($project->due_date), "d-F-Y H:i:s") : ''); ?>">
                                    <?php echo form_error('due_date', '<div class="has-error"><label class="control-label">', '</label></div>'); ?>
                                </div>

                            </div>
                            <div class="col-lg-6">
                                <?php if ($admin) { ?>
                                    <div class="form-group">
                                        <label>Assign to</label>
                                        <?php
                                        $options = [];
                                        foreach ($users as $u) {
                                            $options[$u->user_id] = $u->user_name;
                                        }

                                        $js = [
                                            'id' => 'project_assign_to',
                                            'class' => 'form-control select2'
                                        ];
                                        echo form_dropdown('assigned_to', $options, set_value('assigned_to', isset($project) ? $project->assigned_to : null
                                                )
                                                , $js);
                                        echo form_error('assigned_to', '<div class="has-error"><label class="control-label">', '</label></div>');
                                        ?>
                                    </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="multi-append" class="control-label">Topics</label>
                                    <div class="input-group">
                                        <?php
                                        $options = [];
                                        foreach ($topics as $u) {
                                            $options[$u->topic_id] = $u->topic_name;
                                        }

                                        $js = [
                                            'id' => 'topics',
                                            'class' => 'form-control select2'
                                        ];
                                        echo form_multiselect('topics[]', $options, set_value('topics[]', isset($project) ? $project->topics : null ), $js);
                                        ?>
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" data-toggle="modal" data-target="#topic-modal-form">
                                                <span class="glyphicon glyphicon-plus-sign"></span>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <?php if (isset($project)) { ?>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <?php foreach ($statuses as $role) { ?>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="status" value="<?php echo $role->status_id ?>" <?php echo set_radio('status', $role->status_id, isset($project) ? $project->project_status == $role->status_id : false); ?>><?php echo $role->name; ?>
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if ($admin) { ?>
                            <button type="submit" class="btn btn-default">Submit</button>
                            <?php if (isset($project)) { ?>
                                <a class="btn btn-danger" data-project_name="<?php echo $project->project_name; ?>" data-project_id="<?php echo $project->project_id; ?>">Remove</a>
                                <?php
                            }
                        }
                        ?>
                        </form>
                    <?php } else { ?>
                        <!-- label and text only, non-editable -->    
                        <dl>
                            <dt>Project Name</dt>
                            <dd><?php echo $project->project_name; ?></dd>
                            <dt>Submitted on</dt>
                            <dd><?php echo date_format(date_create($project->created_at), "d-F-Y H:i"); ?></dd>
                            <dt>Project Leader</dt>
                            <dd><?php echo $project->user_name; ?></dd>
                            <dt>Description</dt>
                            <dd><?php echo $project->description; ?></dd>
                            <dt>Due date</dt>
                            <dd>
                                <span id="project-due-date"><?php echo date_format(date_create($project->due_date), "d-F-Y H:i"); ?></span>
                                <span id="project-due-date-remain"></span>
                            </dd>
                        </dl>
                    <?php } ?>
                </div>
                <?php if (isset($project)) { ?>
                    <div class="tab-pane fade" id="task">
                        <div class="row">
                            <div class="col-lg-12">
                                <?php if ($admin || $owner) { ?>
                                    <div class="pull-right">
                                        <span><a class="btn btn-primary btn-raised pull-right" data-toggle="modal" data-target="#task-modal-form"><i class="fa fa-tasks"></i> Create</a></span>
                                    </div>
                                <?php } ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Task List
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <table width="100%" class="table table-striped table-bordered table-hover" id="tasks-datatable" data-url="<?php echo site_url('project/tasks_dt'); ?>">
                                            <thead>
                                                <tr>
                                                    <th>Task</th>
                                                    <th>PIC</th>
                                                    <th>Due date</th>
                                                    <th>Status</th>
                                                    <th>Weight</th>
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
                            <div class='col-lg-12'>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Document List
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <table width="100%" class="table table-striped table-bordered table-hover" id="docs-datatable" data-url="<?php echo site_url('project/docs_dt'); ?>">
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
                                        <div id="fine-uploader-manual-trigger" data-project=<?php echo $project->project_id; ?>></div>
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
    <!-- /.col-lg-12 -->
    <div id="topic-modal-form" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="blue bigger">Add New Topic</h4>
                </div>

                <div class="modal-body">
                    <?php echo form_open(base_url('project/create_topic'), ['class' => 'row form-horizontal']); ?>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Topic </label>

                            <div class="col-sm-9">
                                <input type="text" placeholder="Topic name" class="form-control" name="topic_name" />
                            </div>
                        </div>
                    </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-sm" data-dismiss="modal">
                        <i class="ace-icon fa fa-times"></i>
                        Cancel
                    </button>

                    <button class="btn btn-sm btn-primary">
                        <i class="ace-icon fa fa-check"></i>
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php if (isset($project)) { ?>
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
            <button type="button" id="trigger-upload" class="btn btn-primary">
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
            #trigger-upload {
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
        </style>
        <div id="task-modal-form" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="blue bigger">Task</h4>
                    </div>

                    <?php if ($owner || $admin) { ?>
                        <div class="modal-body modal-form">
                            <?php echo form_open(site_url('project/edit_task'), ['class' => 'row form-horizontal'], ['task_id' => null, 'project_id' => isset($project) ? $project->project_id : null]); ?>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="task_name"> Task </label>

                                    <div class="col-sm-9">
                                        <input required minlength="5" type="text" placeholder="Task name" class="form-control" name="task_name" id="task-name" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="task_desc"> Description </label>

                                    <div class="col-sm-9">
                                        <textarea style="width: 100%" name="desc" id="task-desc"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Assign to </label>
                                    <div class="col-sm-9">
                                        <?php
                                        $options = [];
                                        foreach ($users as $u) {
                                            $options[$u->user_id] = $u->user_name;
                                        }

                                        $js = [
                                            'id' => 'task_assign',
                                            'class' => 'form-control select2-in',
                                            'style' => 'width:100%'
                                        ];
                                        echo form_dropdown('assigned_to', $options, isset($project) ? $project->assigned_to : null, $js);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label class="col-sm-3 control-label no-padding-right" for="task_date"> Due date </label>
                                    <div class="col-sm-9">
                                        <input required class="form-control datetimepicker" data-max="<?php echo isset($project) ? date_format(date_create($project->due_date), "d-F-Y H:i") : ''; ?>" name="due_date" type="text" id="task-date" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Weight </label>

                                    <div class="col-sm-9">
                                        <div class="knob-container inline">
                                            <input id="task-weight" name="weight" type="text" class="input-small knob" value="3" data-min="1" 
                                                   data-max="10" data-step="1" data-width="80" data-height="80" data-thickness=".2" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Status </label>

                                    <div class="col-sm-9">
                                        <input type="checkbox" id="is_done" name="is_done" value="1">Done
                                    </div>
                                </div>
                            </div>
                            </form>
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
                                    <div class="input-group">
                                        <input id="btn-input" type="text" class="form-control input-sm" placeholder="Type your message here..." />
                                        <span class="input-group-btn">
                                            <button class="btn btn-warning btn-sm" id="btn-chat" disabled>
                                                Send
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <!-- /.panel-footer -->
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-sm" data-dismiss="modal">
                                <i class="ace-icon fa fa-times"></i>
                                Cancel
                            </button>

                            <button class="btn btn-sm btn-primary">
                                <i class="ace-icon fa fa-check"></i>
                                Submit
                            </button>

                            <button class="btn btn-sm btn-danger hide">
                                <i class="ace-icon fa fa-remove"></i>
                                Remove
                            </button>
                        </div>
                    <?php } else { ?>
                        <!-- label and text only, non-editable -->    
                        <div class="modal-body">
                            <div class='row'>
                                <dl class='col-sm-6'>
                                    <dt>Task Name</dt>
                                    <dd id="task-name"></dd>

                                    <dt>PIC</dt>
                                    <dd id="task-assign"></dd>
                                    
                                </dl>
                                <dl class='col-sm-6'>
                                    <dt>Due date</dt>
                                    <dd>
                                        <span id="task-due-date"></span>
                                        (<span id="task-due-date-remain"></span>)
                                    </dd>

                                    <dt>Weight</dt>
                                    <dd id="task-weight"></dd>

                                    <dt>Status</dt>
                                    <dd id="task-status"></dd>

                                </dl>                            
                            </div>
                            <div class='row'>
                                <dl class='col-sm-12'>
                                    <dt>Description</dt>
                                    <dd id="task-desc"></dd>
                                </dl>
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
                                    <div class="input-group">
                                        <input id="btn-input" type="text" class="form-control input-sm" placeholder="Type your message here..." />
                                        <span class="input-group-btn">
                                            <button class="btn btn-warning btn-sm" id="btn-chat" disabled>
                                                Send
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <!-- /.panel-footer -->
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
    }?>