<!-- Page-specific CSS -->
<?php echo css_asset('datatables-plugins/dataTables.bootstrap.css'); ?>
<?php echo css_asset('datatables-responsive/responsive.dataTables.css'); ?>
<?php echo css_asset('jquery-gantt/platform.css'); ?>
<?php echo css_asset('jquery-gantt/libs/jquery/dateField/jquery.dateField.css'); ?>
<?php echo css_asset('jquery-gantt/gantt.css'); ?>
<?php echo css_asset('jquery-gantt/ganttPrint.css', null, ['media' => 'print']); ?>
<script>
    var ge;
    $$.push(
            '<?php echo js_asset_url('datatables/js/jquery.dataTables.min.js') ?>'
            , '<?php echo js_asset_url('datatables/js/dataTables.bootstrap.min.js') ?>'
            , '<?php echo js_asset_url('datatables-responsive/dataTables.responsive.js') ?>'
            , '<?php echo js_asset_url('moment/moment.min.js') ?>'
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
    <div class="help-block"></div>
    <div class="panel panel-default main-panel" data-project="<?php echo isset($project) ? $project->project_id : null; ?>">
        <div class="panel-heading">
            <b><?php echo $project->project_name; ?></b>
        </div>
        <div class="panel-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#basic" data-toggle="tab">Basic</a>
                </li>
                <li><a href="#task" data-toggle="tab">Tasks</a>
                </li>
                <li class=""><a href="#timeline" data-toggle="tab">Timeline</a>
                </li>
                <li><a href="#events" data-toggle="tab">Events</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="basic">
                    <dl>
                        <dt>Project Name</dt>
                        <dd><?php echo $project->project_name; ?></dd>

                        <dt>Description</dt>
                        <dd><?php echo $project->description; ?></dd>

                        <dt>Submitted on</dt>
                        <dd><?php echo date_format(date_create($project->created_at), "d-F-Y H:i"); ?></dd>

                        <dt>Start date</dt>
                        <dd>
                            <span id="project-start-date"><?php echo date_format(date_create($project->start_date), "d-F-Y"); ?></span>
                        </dd>

                        <dt>End date</dt>
                        <dd>
                            <span id="project-due-date"><?php echo date_format(date_create($project->end_date), "d-F-Y"); ?></span>
                            <span id="project-due-date-remain"></span>
                        </dd>

                        <dt>Topics</dt>
                        <dd>
                            <ul>
                                <?php
                                $topic_opts = [];
                                foreach ($topics as $u) {
                                    $topic_opts[$u->topic_id] = $u->topic_name;
                                }
                                if (count($project->topics) > 0) {
                                    foreach ($project->topics as $t) {
                                        echo "<li>" . $topic_opts[$t] . "</li>";
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
                                                <th>PIC</th>
                                                <th>Due date</th>
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
                <div class="tab-pane fade" id="timeline">
                    <div class='row'>
                        <div class='col-lg-12'>

                            <div id="workSpace" style="padding:0px; overflow-y:auto; overflow-x:hidden; border:1px solid #e5e5e5; position:relative; margin:0 5px; width:100%; height:400px;">

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
    <!-- jQuery Gantt Templates-->
    <div id="gantEditorTemplates" style="display:none;">
        <div class="__template__" type="GANTBUTTONS">
            <!--
            <div class="ganttButtonBar noprint">
                <div class="buttons">
                    <button onclick="$('#workSpace').trigger('expandAll.gantt');return false;" class="button textual icon " title="EXPAND_ALL"><span class="teamworkIcon">6</span></button>
                    <button onclick="$('#workSpace').trigger('collapseAll.gantt'); return false;" class="button textual icon " title="COLLAPSE_ALL"><span class="teamworkIcon">5</span></button>

                    <span class="ganttButtonSeparator"></span>
                    <button onclick="$('#workSpace').trigger('zoomMinus.gantt'); return false;" class="button textual icon " title="zoom out"><span class="teamworkIcon">)</span></button>
                    <button onclick="$('#workSpace').trigger('zoomPlus.gantt');return false;" class="button textual icon " title="zoom in"><span class="teamworkIcon">(</span></button>
                    <span class="ganttButtonSeparator"></span>
                    <button onclick="ge.gantt.showCriticalPath = !ge.gantt.showCriticalPath; ge.redraw();return false;" class="button textual icon requireCanSeeCriticalPath" title="CRITICAL_PATH"><span class="teamworkIcon">&pound;</span></button>
                    <span class="ganttButtonSeparator requireCanSeeCriticalPath"></span>
                    <button onclick="ge.splitter.resize(.1);return false;" class="button textual icon" ><span class="teamworkIcon">F</span></button>
                    <button onclick="ge.splitter.resize(50);return false;" class="button textual icon" ><span class="teamworkIcon">O</span></button>
                    <button onclick="ge.splitter.resize(100);return false;" class="button textual icon"><span class="teamworkIcon">R</span></button>
                </div>
            </div>-->
        </div>

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
            <th class="gdfCell edit" align="right" style="cursor:pointer"><span class="taskRowIndex">(#=obj.getRow()+1#)</span> <span class="teamworkIcon" style="font-size:12px;" >e</span></th>
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
            <td class="gdfCell taskAssigs">(#=obj.getAssigsString()#)</td>
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
                
                <td colspan="4" valign="top"><label for="name" class="required">name</label><br><input type="text" name="name" id="name"class="formElements" autocomplete='off' maxlength=255 style='width:100%' value="" required="true" oldvalue="1"></td>
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
