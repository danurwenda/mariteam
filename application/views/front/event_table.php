<!-- Page-specific CSS -->
<?php echo css_asset('datatables-plugins/dataTables.bootstrap.css'); ?>
<?php echo css_asset('datatables-responsive/responsive.dataTables.css'); ?>
<script>
    $$.push('<?php echo js_asset_url('datatables/js/jquery.dataTables.min.js') ?>'
            , '<?php echo js_asset_url('datatables/js/dataTables.bootstrap.min.js') ?>'
            , '<?php echo js_asset_url('datatables-responsive/dataTables.responsive.js') ?>'
            , '<?php echo base_url('dist/js/event-table.js') ?>');
</script>
<div class="col-lg-12">
    <div class="pull-right">
        <?php if ($admin) { ?>
            <span><a class="btn btn-primary btn-raised pull-right" href="<?php echo site_url('event/create'); ?>"><i class="fa fa-cubes"></i> Create</a></span>
        <?php } ?>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            List of Event
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
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
