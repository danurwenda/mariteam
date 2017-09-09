<!-- Page-specific CSS -->
<?php echo css_asset('datatables-plugins/dataTables.bootstrap.css'); ?>
<?php echo css_asset('datatables-responsive/responsive.dataTables.css'); ?>
<script>
    $$.push('<?php echo js_asset_url('datatables/js/jquery.dataTables.min.js') ?>'
            , '<?php echo js_asset_url('datatables/js/dataTables.bootstrap.min.js') ?>'
            , '<?php echo js_asset_url('datatables-responsive/dataTables.responsive.js') ?>'
            , '<?php echo base_url('dist/js/people-table.js') ?>');
</script>
<div class="col-lg-12">
    <div class="pull-right">
        <span><a class="btn btn-primary btn-raised pull-right" href="<?php echo site_url('people/create');?>"><i class="fa fa-user-plus"></i> Create</a></span>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            List of User
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Member for</th>
                        <th>Last access</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ps as $p) { ?>
                        <tr data-uid="<?php echo $p->user_id; ?>">
                            <td><?php echo $p->person_name; ?></td>
                            <td><?php echo $p->status == 0 ? 'Blocked' : 'Active'; ?></td>
                            <td><?php echo $p->rname; ?></td>
                            <td><?php echo $p->created_at; ?></td>
                            <td><?php echo $p->last_access; ?></td>
                            <td><?php echo anchor('people/edit/' . $p->user_id, 'Edit'); ?></td>
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
