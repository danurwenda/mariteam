<!-- Page-specific CSS -->
<?php echo css_asset('eonasdan-bootstrap-datetimepicker/bootstrap-datetimepicker.min.css'); ?>
<?php echo css_asset('select2/select2.min.css'); ?>
<?php echo css_asset('select2/themes/select2-bootstrap.min.css'); ?>
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
            , '<?php echo base_url('dist/js/project-form.js') ?>');
</script>
<div class="col-lg-12">
    <div class="panel panel-default">
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
                    <li><a href="#calendar" data-toggle="tab">Timeline</a>
                    </li>
                <?php } ?>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="basic">
                    <?php echo form_open(isset($project) ? 'project/update' : 'project/create', ['id' => 'project_form']); ?>
                    <div class="row">
                        <?php if (isset($updated) && $updated) { ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                Project updated.
                            </div>
                        <?php }
                        ?>
                        <div class="col-lg-6">
                            <?php
                            if (isset($project)) {
                                echo form_hidden('project_id', $project->project_id);
                            }
                            ?>                    
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
                            <?php if ($user_role == 1) { ?>
                                <div class="form-group">
                                    <label>Assign to</label>
                                    <?php
                                    $options = [];
                                    foreach ($users as $u) {
                                        $options[$u->user_id] = $u->user_name;
                                    }

                                    $js = [
                                        'id'=>'project_assign_to',
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
                    <button type="submit" class="btn btn-default">Submit</button>
                    <?php if (isset($project) && $user_role == 1) { ?>
                        <a class="btn btn-danger" data-project_name="<?php echo $project->project_name; ?>" data-project_id="<?php echo $project->project_id; ?>">Remove</a>
                    <?php } ?>
                    </form>
                </div>
                <?php if (isset($project)) { ?>
                    <div class="tab-pane fade" id="task">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="pull-right">
                                    <span><a class="btn btn-primary btn-raised pull-right" data-toggle="modal" data-target="#task-modal-form"><i class="fa fa-tasks"></i> Create</a></span>
                                </div>
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
                                                    <th>Edit</th>
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
                    <div class="tab-pane fade" id="calendar">

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
        <div id="task-modal-form" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="blue bigger">Task</h4>
                    </div>

                    <div class="modal-body">
                        <?php echo form_open(site_url('project/edit_task'), ['class' => 'row form-horizontal'], ['task_id' => null, 'project_id' => isset($project) ? $project->project_id : null]); ?>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="task_name"> Task </label>

                                <div class="col-sm-9">
                                    <input required minlength="5" type="text" placeholder="Task name" class="form-control" name="task_name" id="task_name" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="task_desc"> Description </label>

                                <div class="col-sm-9">
                                    <textarea style="width: 100%" name="desc" id="task_desc"></textarea>
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
                                    <input required class="form-control datetimepicker" data-max="<?php echo isset($project) ? date_format(date_create($project->due_date), "d-F-Y H:i") : ''; ?>" name="due_date" type="text" id="task_date" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Weight </label>

                                <div class="col-sm-9">
                                    <div class="knob-container inline">
                                        <input id="task_weight" name="weight" type="text" class="input-small knob" value="3" data-min="1" 
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
                </div>
            </div>
        </div>
        <?php
    }?>