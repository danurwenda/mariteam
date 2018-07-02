<?php echo css_asset('quill/quill.snow.css'); ?>
<?php echo css_asset('eonasdan-bootstrap-datetimepicker/bootstrap-datetimepicker.min.css'); ?>
<?php echo css_asset('select2/select2.min.css'); ?>
<?php echo css_asset('select2/themes/select2-bootstrap.min.css'); ?>
<?php echo css_asset('fine-uploader/fine-uploader-new.min.css'); ?>
<?php echo css_asset('datatables-plugins/dataTables.bootstrap.css'); ?>
<?php echo css_asset('datatables-responsive/responsive.dataTables.css'); ?>
<?php echo css_asset('jquery-gantt/platform.css'); ?>
<?php echo css_asset('jquery-gantt/libs/jquery/dateField/jquery.dateField.css'); ?>
<?php echo css_asset('jquery-gantt/gantt.css'); ?>
<?php echo css_asset('jquery-gantt/ganttPrint.css', null, ['media' => 'print']); ?>
<!-- override style for <em> tag specified in platform.css -->
    <style>
        .row em{
            font-style:italic;
        }
        </style>
<script>
    var ge;
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
            , '<?php echo js_asset_url('quill/quill.min.js') ?>'
            // jquery-gantt
            , '<?php echo js_asset_url('jquery-ui/jquery-ui.min.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/libs/jquery/jquery.livequery.1.1.1.min.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/libs/jquery/jquery.timers.js') ?>'

            , '<?php echo js_asset_url('jquery-gantt/libs/utilities.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/libs/forms.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/libs/date.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/libs/dialogs.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/libs/layout.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/libs/i18nJs.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/libs/jquery/dateField/jquery.dateField.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/libs/jquery/JST/jquery.JST.js') ?>'

            , '<?php echo js_asset_url('jquery-gantt/libs/jquery/svg/jquery.svg.min.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/libs/jquery/svg/jquery.svgdom.1.8.js') ?>'

            , '<?php echo js_asset_url('jquery-gantt/ganttUtilities.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/ganttTask.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/ganttDrawerSVG.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/ganttGridEditor.js') ?>'
            , '<?php echo js_asset_url('jquery-gantt/ganttMaster.js') ?>'

            , '<?php echo base_url('dist/js/project-form.js') ?>');
</script>
<?php
if (
    $admin ||
    (isset($project) && $owner)
) {
    ?>
    <form action="<?php echo $admin ? site_url('people/create_user_simple') : site_url('user/create_person'); ?>" class="row form-horizontal hide new-person-form" method="post" accept-charset="utf-8">
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
            <?php if ($admin) {?>
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
            <?php }?>
        </div>
    </form>

<?php }?>

<div class="col-lg-12">
    <div class="panel panel-default main-panel" data-project="<?php echo isset($project) ? $project->project_id : null; ?>">
        <div class="panel-heading">
            <b><?php echo isset($project) ? $project->project_name : 'Project Information'; ?></b>
        </div>
        <div class="panel-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#basic" data-toggle="tab">Basic</a>
                </li>
                <?php if (isset($project)) {?>
                    <li class=""><a href="#task" data-toggle="tab">Tasks</a>
                    </li>
                    <li><a href="#documents" data-toggle="tab">Documents</a>
                    </li>
                    <li class=""><a href="#timeline" data-toggle="tab">Timeline</a></li>
                    <li class=""><a href="#events" data-toggle="tab">Events</a></li>
                <?php }?>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="basic">
                    <?php
if ($admin) {
    echo form_open(isset($project) ? 'project/update' : 'project/create', ['id' => 'project_form'], isset($project) ? ['project_id' => $project->project_id] : []);
    ?>
                        <div class="row">
                            <?php if (isset($updated) && $updated) {?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    Project updated.
                                </div>
                            <?php }
    ?>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input required="" minlength="5" name="name" type="text" class="form-control" value="<?php echo set_value('name', isset($project) ? $project->project_name : ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Owner</label>
                                    <input required="" minlength="5" name="owner" type="text" class="form-control" value="<?php echo set_value('owner', isset($project) ? $project->owner : ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Offtaker</label>
                                    <input required="" minlength="5" name="offtaker" type="text" class="form-control" value="<?php echo set_value('offtaker', isset($project) ? $project->offtaker : ''); ?>">
                                </div>
                                <div class="form-group ">
                                    <label for="description">Description</label>
                                    <div id="description"><?php echo isset($project) ? $project->description : ''; ?></div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                               <div class="form-group">
                                    <label for="multi-append" class="control-label">Groups</label>
                                    <?php
$group_opts = [];
    foreach ($groups as $u) {
        $group_opts[$u->group_id] = $u->group_name;
    }

    $js = [
        'id' => 'groups',
        'class' => 'form-control select2',
    ];
    echo form_multiselect('groups[]', $group_opts, set_value('groups[]', isset($project) ? $project->groups : null), $js);
    ?>
                                </div>

                                <div class="form-group ">
                                    <label for="project_date">Target</label>
                                    <input required class="form-control datetimepicker" name="end_date" type="text" id="project_end_date" value="<?php echo set_value('end_date', isset($project) ? date_format(date_create($project->end_date), "d-F-Y") : ''); ?>">
                                </div>
                                    <div class="form-group">
                                    <label>Cost (USD)</label>
                                    <input required="" minlength="5" name="cost" type="text" class="form-control" value="<?php echo set_value('cost', isset($project) ? $project->cost : ''); ?>">
                                            </div>
                                <div class="form-group">
                                    <label>IRR</label>
                                    <input required="" minlength="5" name="irr" type="text" class="form-control" value="<?php echo set_value('irr', isset($project) ? $project->IRR : ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Latest Status</label>
                                    <input required="" minlength="5" name="latest_status" type="text" class="form-control" value="<?php echo set_value('latest_status', isset($project) ? $project->latest_status : ''); ?>">
                                    </div>

                            </div>
                        </div>
                        <?php if ($admin) {?>
                            <button type="submit" class="btn btn-default">Submit</button>
                            <?php if (isset($project)) {?>
                                <a class="btn btn-danger" data-project_name="<?php echo $project->project_name; ?>" data-project_id="<?php echo $project->project_id; ?>">Remove</a>
                                <?php
}
    }
    ?>
                        </form>
                    <?php } else {?>
                        <!-- label and text only, non-editable -->
                        <dl>
                            <dt>Project Name</dt>
                            <dd><?php echo $project->project_name; ?></dd>

                            <dt>Description</dt>
                            <dd><?php echo $project->description; ?></dd>

                            <dt>Target</dt>
                            <dd>
                                <span id="project-due-date"><?php echo date_format(date_create($project->end_date), "d-F-Y"); ?></span>
                                <span id="project-due-date-remain"></span>
                            </dd>
                        </dl>
                    <?php }?>
                </div>
                <?php if (isset($project)) {?>
                    <div class="tab-pane fade" id="task">
                        <div class="row">
                            <div class="col-xs-12">
                                <?php if ($admin || $owner) {?>
                                    <div class="pull-right">
                                        <span><a class="btn btn-primary btn-raised pull-right" data-toggle="modal" data-target="#task-modal-form"><i class="fa fa-tasks"></i> Create</a></span>
                                    </div>
                                <?php }?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Task List
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <table width="100%" class="table table-striped table-bordered table-hover" id="tasks-datatable" data-url="<?php echo site_url('project/tasks_dt'); ?>">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Task</th>
                                                    <th>Target</th>
                                                    <th>Status</th>
                                                    <th>Weight</th>
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
                            <div class='col-xs-12'>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Upload Document
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <div id="fine-uploader-manual-trigger-project" data-project=<?php echo $project->project_id; ?>></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="timeline">
                        <div class='row'>
                            <div class='col-xs-12'>

                                <div id="workSpace" style="padding:0px; overflow-y:auto; overflow-x:hidden; border:1px solid #e5e5e5; position:relative; margin:0 5px; width:100%;">

                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="events">
                        <div class='row'>
                            <div class='col-xs-12'>
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
                <?php }?>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
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
<?php if (isset($project)) {?>
    <!-- jQuery Gantt Templates-->
    <div id="gantEditorTemplates" style="display:none;">
        <div class="__template__" type="GANTBUTTONS"><!--
          <div class="ganttButtonBar noprint">
            <div class="buttons">
              <span class="ganttButtonSeparator requireCanWrite"></span>
              <button onclick="$('#workSpace').trigger('undo.gantt');return false;" class="button textual icon requireCanWrite" title="undo"><span class="teamworkIcon">&#39;</span></button>
              <button onclick="$('#workSpace').trigger('redo.gantt');return false;" class="button textual icon requireCanWrite" title="redo"><span class="teamworkIcon">&middot;</span></button>
              <span class="ganttButtonSeparator requireCanWrite requireCanInOutdent"></span>
              <button onclick="$('#workSpace').trigger('outdentCurrentTask.gantt');return false;" class="button textual icon requireCanWrite requireCanInOutdent" title="un-indent task"><span class="teamworkIcon">.</span></button>
              <button onclick="$('#workSpace').trigger('indentCurrentTask.gantt');return false;" class="button textual icon requireCanWrite requireCanInOutdent" title="indent task"><span class="teamworkIcon">:</span></button>
              <span class="ganttButtonSeparator requireCanWrite requireCanMoveUpDown"></span>
              <button onclick="$('#workSpace').trigger('moveUpCurrentTask.gantt');return false;" class="button textual icon requireCanWrite requireCanMoveUpDown" title="move up"><span class="teamworkIcon">k</span></button>
              <button onclick="$('#workSpace').trigger('moveDownCurrentTask.gantt');return false;" class="button textual icon requireCanWrite requireCanMoveUpDown" title="move down"><span class="teamworkIcon">j</span></button>
              <span class="ganttButtonSeparator"></span>
              <button onclick="$('#workSpace').trigger('expandAll.gantt');return false;" class="button textual icon " title="EXPAND_ALL"><span class="teamworkIcon">6</span></button>
              <button onclick="$('#workSpace').trigger('collapseAll.gantt'); return false;" class="button textual icon " title="COLLAPSE_ALL"><span class="teamworkIcon">5</span></button>

            <span class="ganttButtonSeparator"></span>
              <button onclick="$('#workSpace').trigger('zoomMinus.gantt'); return false;" class="button textual icon " title="zoom out"><span class="teamworkIcon">)</span></button>
              <button onclick="$('#workSpace').trigger('zoomPlus.gantt');return false;" class="button textual icon " title="zoom in"><span class="teamworkIcon">(</span></button>
           <span class="ganttButtonSeparator"></span>
              <button onclick="ge.gantt.showCriticalPath=!ge.gantt.showCriticalPath; ge.redraw();return false;" class="button textual icon requireCanSeeCriticalPath" title="CRITICAL_PATH"><span class="teamworkIcon">&pound;</span></button>
            <span class="ganttButtonSeparator requireCanSeeCriticalPath"></span>
              <button onclick="ge.splitter.resize(.1);return false;" class="button textual icon" ><span class="teamworkIcon">F</span></button>
              <button onclick="ge.splitter.resize(50);return false;" class="button textual icon" ><span class="teamworkIcon">O</span></button>
              <button onclick="ge.splitter.resize(100);return false;" class="button textual icon"><span class="teamworkIcon">R</span></button>
              &nbsp; &nbsp; &nbsp; &nbsp;
            <button id="save-timeline" class="button first requireWrite" title="Save">Save</button>
            <button class="button login" title="login/enroll" onclick="loginEnroll($(this));" style="display:none;">login/enroll</button>
            <button class="button opt collab" title="Start with Twproject" onclick="collaborate($(this));" style="display:none;"><em>collaborate</em></button>
            </div></div>
            --></div>

        <div class="__template__" type="TASKSEDITHEAD"><!--
          <table class="gdfTable" cellspacing="0" cellpadding="0">
            <thead>
            <tr style="height:40px">
              <th class="gdfColHeader" style="width:35px; border-right: none"></th>
              <th class="gdfColHeader" style="width:25px;"></th>
              <th class="gdfColHeader gdfResizable" style="width:300px;">name</th>
              <th class="gdfColHeader"  align="center" style="width:17px;" title="Start date is a milestone."><span class="teamworkIcon" style="font-size: 8px;">^</span></th>
              <th class="gdfColHeader gdfResizable" style="width:80px;">start</th>
              <th class="gdfColHeader"  align="center" style="width:17px;" title="End date is a milestone."><span class="teamworkIcon" style="font-size: 8px;">^</span></th>
              <th class="gdfColHeader gdfResizable" style="width:80px;">End</th>
              <th class="gdfColHeader gdfResizable" style="width:50px;">dur.</th>
              <th class="gdfColHeader gdfResizable" style="width:20px;">%</th>
              <th class="gdfColHeader gdfResizable requireCanSeeDep" style="width:50px;">depe.</th>
            </tr>
            </thead>
          </table>
            --></div>

        <div class="__template__" type="TASKROW"><!--
          <tr taskId="(#=obj.id#)" class="taskEditRow (#=obj.isParent()?'isParent':''#) (#=obj.collapsed?'collapsed':''#)" level="(#=level#)">
            <th class="gdfCell edit" align="right" style="cursor:pointer;"><span class="taskRowIndex">(#=obj.getRow()+1#)</span> <span class="teamworkIcon" style="font-size:12px;" >e</span></th>
            <td class="gdfCell noClip" align="center"><div class="taskStatus cvcColorSquare" status="(#=obj.status#)"></div></td>
            <td class="gdfCell indentCell" style="padding-left:(#=obj.level*10+18#)px;">
              <div class="exp-controller" align="center"></div>
              <input type="text" name="name" value="(#=obj.name#)" placeholder="name">
            </td>
            <td class="gdfCell" align="center"><input type="checkbox" name="startIsMilestone"></td>
            <td class="gdfCell"><input type="text" name="start"  value="" class="date"></td>
            <td class="gdfCell" align="center"><input type="checkbox" name="endIsMilestone"></td>
            <td class="gdfCell"><input type="text" name="end" value="" class="date"></td>
            <td class="gdfCell"><input type="text" name="duration" autocomplete="off" value="(#=obj.duration#)"></td>
            <td class="gdfCell"><input type="text" name="progress" class="validated" entrytype="PERCENTILE" autocomplete="off" value="(#=obj.progress?obj.progress:''#)" (#=obj.progressByWorklog?"readOnly":""#)></td>
            <td class="gdfCell requireCanSeeDep"><input type="text" name="depends" autocomplete="off" value="(#=obj.depends#)" (#=obj.hasExternalDep?"readonly":""#)></td>
          </tr>
            --></div>

        <div class="__template__" type="TASKEMPTYROW"><!--
          <tr class="taskEditRow emptyRow" >
            <th class="gdfCell" align="right"></th>
            <td class="gdfCell noClip" align="center"></td>
            <td class="gdfCell"></td>
            <td class="gdfCell"></td>
            <td class="gdfCell"></td>
            <td class="gdfCell"></td>
            <td class="gdfCell"></td>
            <td class="gdfCell"></td>
            <td class="gdfCell"></td>
            <td class="gdfCell"></td>
            <td class="gdfCell requireCanSeeDep"></td>
            <td class="gdfCell"></td>
          </tr>
            --></div>

        <div class="__template__" type="TASKBAR"><!--
          <div class="taskBox taskBoxDiv" taskId="(#=obj.id#)" >
            <div class="layout (#=obj.hasExternalDep?'extDep':''#)">
              <div class="taskStatus" status="(#=obj.status#)"></div>
              <div class="taskProgress" style="width:(#=obj.progress>100?100:obj.progress#)%; background-color:(#=obj.progress>100?'red':'rgb(153,255,51);'#);"></div>
              <div class="milestone (#=obj.startIsMilestone?'active':''#)" ></div>

              <div class="taskLabel"></div>
              <div class="milestone end (#=obj.endIsMilestone?'active':''#)" ></div>
            </div>
          </div>
            --></div>


        <div class="__template__" type="CHANGE_STATUS"><!--
            <div class="taskStatusBox">
              <div class="taskStatus cvcColorSquare" status="STATUS_ACTIVE" title="active"></div>
              <div class="taskStatus cvcColorSquare" status="STATUS_DONE" title="completed"></div>
              <div class="taskStatus cvcColorSquare" status="STATUS_FAILED" title="failed"></div>
              <div class="taskStatus cvcColorSquare" status="STATUS_SUSPENDED" title="suspended"></div>
              <div class="taskStatus cvcColorSquare" status="STATUS_UNDEFINED" title="undefined"></div>
            </div>
            --></div>





        <div class="__template__" type="TASK_EDITOR"><!--
          <div class="ganttTaskEditor">
            <h2 class="taskData">Task editor</h2>
            <table  cellspacing="1" cellpadding="5" width="100%" class="taskData table" border="0">
                  <tr>
                <td width="200" style="height: 80px"  valign="top">
                  <label for="code">code/short name</label><br>
                  <input type="text" name="code" id="code" value="" size=15 class="formElements" autocomplete='off' maxlength=255 style='width:100%' oldvalue="1">
                </td>
                <td colspan="3" valign="top"><label for="name" class="required">name</label><br><input type="text" name="name" id="name"class="formElements" autocomplete='off' maxlength=255 style='width:100%' value="" required="true" oldvalue="1"></td>
                  </tr>


              <tr class="dateRow">
                <td nowrap="">
                  <div style="position:relative">
                    <label for="start">start</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" id="startIsMilestone" name="startIsMilestone" value="yes"> &nbsp;<label for="startIsMilestone">is milestone</label>&nbsp;
                    <br><input type="text" name="start" id="start" size="8" class="formElements dateField validated date" autocomplete="off" maxlength="255" value="" oldvalue="1" entrytype="DATE">
                    <span title="calendar" id="starts_inputDate" class="teamworkIcon openCalendar" onclick="$(this).dateField({inputField:$(this).prevAll(':input:first'),isSearchField:false});">m</span>          </div>
                </td>
                <td nowrap="">
                  <label for="end">End</label>&nbsp;&nbsp;&nbsp;&nbsp;
                  <input type="checkbox" id="endIsMilestone" name="endIsMilestone" value="yes"> &nbsp;<label for="endIsMilestone">is milestone</label>&nbsp;
                  <br><input type="text" name="end" id="end" size="8" class="formElements dateField validated date" autocomplete="off" maxlength="255" value="" oldvalue="1" entrytype="DATE">
                  <span title="calendar" id="ends_inputDate" class="teamworkIcon openCalendar" onclick="$(this).dateField({inputField:$(this).prevAll(':input:first'),isSearchField:false});">m</span>
                </td>
                <td nowrap="" >
                  <label for="duration" class=" ">Days</label><br>
                  <input type="text" name="duration" id="duration" size="4" class="formElements validated durationdays" title="Duration is in working days." autocomplete="off" maxlength="255" value="" oldvalue="1" entrytype="DURATIONDAYS">&nbsp;
                </td>
              </tr>

              <tr>
                <td  colspan="2">
                  <label for="status" class=" ">status</label><br>
                  <select id="status" name="status" class="taskStatus" status="(#=obj.status#)"  onchange="$(this).attr('STATUS',$(this).val());">
                    <option value="STATUS_ACTIVE" class="taskStatus" status="STATUS_ACTIVE" >active</option>
                    <option value="STATUS_SUSPENDED" class="taskStatus" status="STATUS_SUSPENDED" >suspended</option>
                    <option value="STATUS_DONE" class="taskStatus" status="STATUS_DONE" >completed</option>
                    <option value="STATUS_FAILED" class="taskStatus" status="STATUS_FAILED" >failed</option>
                    <option value="STATUS_UNDEFINED" class="taskStatus" status="STATUS_UNDEFINED" >undefined</option>
                  </select>
                </td>

                <td valign="top" nowrap>
                  <label>progress</label><br>
                  <input type="text" name="progress" id="progress" size="7" class="formElements validated percentile" autocomplete="off" maxlength="255" value="" oldvalue="1" entrytype="PERCENTILE">
                </td>
              </tr>

                  </tr>
                  <tr>
                    <td colspan="4">
                      <label for="description">Description</label><br>
                      <textarea rows="3" cols="30" id="description" name="description" class="formElements" style="width:100%"></textarea>
                    </td>
                  </tr>
                </table>

            <h2>Assignments</h2>
          <table  cellspacing="1" cellpadding="0" width="100%" id="assigsTable">
            <tr>
              <th style="width:100px;">name</th>
              <th style="width:70px;">Role</th>
              <th style="width:30px;">est.wklg.</th>
              <th style="width:30px;" id="addAssig"><span class="teamworkIcon" style="cursor: pointer">+</span></th>
            </tr>
          </table>

          <div style="text-align: right; padding-top: 20px">
            <span id="saveButton" class="button first" onClick="$(this).trigger('saveFullEditor.gantt');">Save</span>
          </div>

          </div>
            --></div>



        <div class="__template__" type="ASSIGNMENT_ROW"><!--
          <tr taskId="(#=obj.task.id#)" assId="(#=obj.assig.id#)" class="assigEditRow" >
            <td ><select name="resourceId"  class="formElements" (#=obj.assig.id.indexOf("tmp_")==0?"":"disabled"#) ></select></td>
            <td ><select type="select" name="roleId"  class="formElements"></select></td>
            <td ><input type="text" name="effort" value="(#=getMillisInHoursMinutes(obj.assig.effort)#)" size="5" class="formElements"></td>
            <td align="center"><span class="teamworkIcon delAssig del" style="cursor: pointer">d</span></td>
          </tr>
            --></div>



        <div class="__template__" type="RESOURCE_EDITOR"><!--
          <div class="resourceEditor" style="padding: 5px;">

            <h2>Project team</h2>
            <table  cellspacing="1" cellpadding="0" width="100%" id="resourcesTable">
              <tr>
                <th style="width:100px;">name</th>
                <th style="width:30px;" id="addResource"><span class="teamworkIcon" style="cursor: pointer">+</span></th>
              </tr>
            </table>

            <div style="text-align: right; padding-top: 20px"><button id="resSaveButton" class="button big">Save</button></div>
          </div>
            --></div>



        <div class="__template__" type="RESOURCE_ROW"><!--
          <tr resId="(#=obj.id#)" class="resRow" >
            <td ><input type="text" name="name" value="(#=obj.name#)" style="width:100%;" class="formElements"></td>
            <td align="center"><span class="teamworkIcon delRes del" style="cursor: pointer">d</span></td>
          </tr>
            --></div>


    </div>

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
    <div id="task-modal-form" class="modal " tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="blue bigger">Task</h4>
                </div>

                <?php if ($owner || $admin) {?>
                    <div class="modal-body modal-form">
                        <?php echo form_open(site_url('project/edit_task'), ['id' => 'task-form', 'class' => 'row form-horizontal'], ['task_id' => null, 'project_id' => isset($project) ? $project->project_id : null]); ?>
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
                                    <div class="input-group">
                                        <?php
$options = [];
    foreach ($users as $u) {
        $options[$u->person_id] = $u->person_name;
    }

    $js = [
        'id' => 'task-assign',
        'class' => 'form-control select2-in',
        'style' => 'width:100%',
    ];
    echo form_dropdown('assigned_to', $options, null, $js);
    ?>
                                        <span class="input-group-btn">
                                            <button class="btn btn-default add-person-btn btn-sm" type="button">
                                                <span class="glyphicon glyphicon-plus-sign"></span>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="col-sm-3 control-label no-padding-right" for="task_date"> Start date </label>
                                <div class="col-sm-9">
                                    <input required class="form-control datetimepicker" data-min="<?php echo isset($project) ? date_format(date_create($project->start_date), "d-F-Y 00:00:00") : ''; ?>" name="start_date" type="text" id="task-start-date" >
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="col-sm-3 control-label no-padding-right" for="task_date"> Due date </label>
                                <div class="col-sm-9">
                                    <input required class="form-control datetimepicker" data-max="<?php echo isset($project) ? date_format(date_create($project->end_date), "d-F-Y 23:59:59") : ''; ?>" name="end_date" type="text" id="task-end-date" >
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
                                    <?php foreach ($statuses as $role) {?>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="task_status" value="<?php echo $role->status_id ?>" ><?php echo $role->name; ?>
                                            </label>
                                        </div>
                                    <?php }?>

                                </div>
                            </div>
                        </div>
                        </form>
                        <div class='row' id="task-upload-row">
                            <div class='col-sm-12 col-md-6' id="task-uploaded">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Uploaded Document
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <ul id="task-docs"></ul>
                                    </div>
                                </div>
                            </div>
                            <div class='col-sm-12 col-md-6' id="task-upload">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Upload Document
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <div id="fine-uploader-manual-trigger-task"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="comment-panel panel panel-default" id="task-chat-panel">
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

                        <button class="btn btn-sm btn-primary btn-subm">
                            <i class="ace-icon fa fa-check"></i>
                            Submit
                        </button>

                        <button class="btn btn-sm btn-danger hide">
                            <i class="ace-icon fa fa-remove"></i>
                            Remove
                        </button>
                    </div>
                <?php } else {
    ?>
                    <!-- label and text only, non-editable -->
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
                            <div class='col-sm-12 col-md-6'>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Uploaded Document
                                    </div>
                                    <div class="panel-body">
                                        <ul id="task-docs">

                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class='col-sm-12 col-md-6'>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Upload Document
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <div id="fine-uploader-manual-trigger-task"></div>
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
                <?php }?>
            </div>
        </div>
    </div>
<?php }
?>