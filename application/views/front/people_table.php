<!-- Page-specific CSS -->
<?php echo css_asset('datatables-plugins/dataTables.bootstrap.css'); ?>
<?php echo css_asset('datatables-responsive/responsive.dataTables.css'); ?>
<script>
    $$.push('<?php echo js_asset_url('datatables/js/jquery.dataTables.min.js') ?>'
            , '<?php echo js_asset_url('bootbox/bootbox.js') ?>'
            , '<?php echo js_asset_url('datatables/js/dataTables.bootstrap.min.js') ?>'
            , '<?php echo js_asset_url('datatables-responsive/dataTables.responsive.js') ?>'
            , '<?php echo base_url('dist/js/people-table.js') ?>');
</script>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            User Management
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href ="#users" data-toggle="tab">Users</a></li>
                <li><a href ="#groups" data-toggle="tab">Groups</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="users">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="pull-right">
                                <span><a class="btn btn-primary btn-raised pull-right" href="<?php echo site_url('people/create_user'); ?>"><i class="fa fa-user-plus"></i> Create</a></span>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Users
                                </div>
                                <!-- /.panel-heading -->
                                <div class="panel-body">
                                    <table width="100%" class="table table-striped table-bordered table-hover" id="users-table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Is a user</th>
                                                <th>Role</th>                       
                                                <th>Last access</th>
                                                <th>Edit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ps as $p) { ?>
                                                <tr data-uid="<?php echo $p->person_id; ?>">
                                                    <td><?php echo $p->person_name; ?></td>
                                                    <td><?php echo ($p->user_id ? 'Yes' : 'No'); ?></td>
                                                    <td><?php echo $p->name; ?></td>
                                                    <td><?php echo $p->last_access; ?></td>
                                                    <td><?php echo anchor('people/edit/' . $p->person_id, 'Edit'); ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <!-- /.table-responsive -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="tab-pane" id="groups">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="pull-right">
                                <span><a class="btn btn-primary btn-raised pull-right" data-toggle="modal" data-target="#group-modal-form"><i class="fa fa-group"></i> Create</a></span>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Groups
                                </div>
                                <!-- /.panel-heading -->
                                <div class="panel-body">
                                    <table width="100%" class="table table-striped table-bordered table-hover" id="groups-table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Is public?</th>
                                                <th># of Users</th>                       
                                                <th># of Projects</th>
                                                <th>Edit</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <!-- /.table-responsive -->
                                </div>
                            </div>
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
<div id="group-modal-form" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="blue bigger">Add New Group</h4>
            </div>

            <div class="modal-body">
                <?php echo form_open(base_url('people/create_group'), ['class' => 'row form-horizontal']); ?>
                <div class="col-xs-12">
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Group </label>

                        <div class="col-sm-9">
                            <input type="text" placeholder="Group name" class="form-control" name="group_name" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-3">
                            <input type="checkbox" name="is_public" value="1"> Is this group visible to public?
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
