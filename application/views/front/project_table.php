<!-- Page-specific CSS -->
<?php echo css_asset('datatables-plugins/dataTables.bootstrap.css'); ?>
<?php echo css_asset('datatables-responsive/responsive.dataTables.css'); ?>
<script>
    $$.push('<?php echo js_asset_url('datatables/js/jquery.dataTables.min.js') ?>'
            , '<?php echo js_asset_url('datatables/js/dataTables.bootstrap.min.js') ?>'
            , '<?php echo js_asset_url('datatables-responsive/dataTables.responsive.js') ?>'
            , '<?php echo base_url('dist/js/project-table.js') ?>');
</script>
<div class="col-lg-12">
    <div class="pull-right">
        <?php if($user_role==1){?>
        <span><a class="btn btn-primary btn-raised pull-right" href="<?php echo site_url('project/create'); ?>"><i class="fa fa-cubes"></i> Create</a></span>
        <?php }?>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            List of Project
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>PIC</th>
                        <th>Status</th>
                        <th>Due date</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $now = date_create();
                    foreach ($ps as $p) {
                        ?>
                        <tr data-uid="<?php echo $p->project_id; ?>">
                            <td><?php echo anchor('project/edit/' . $p->project_id, $p->project_name); ?></td>
                            <td><?php echo $p->user_name; ?></td>
                            <td><?php echo $p->status; ?></td>
                            <td><?php echo $p->due_date; ?></td>
                            <td><?php echo $p->progress ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->