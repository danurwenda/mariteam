<!-- Page-specific CSS -->
<?php echo css_asset('select2/select2.min.css'); ?>
<?php echo css_asset('select2/themes/select2-bootstrap.min.css'); ?>
<?php echo css_asset('datatables-plugins/dataTables.bootstrap.css'); ?>
<?php echo css_asset('datatables-responsive/responsive.dataTables.css'); ?>
<script>
    $$.push('<?php echo js_asset_url('datatables/js/jquery.dataTables.min.js') ?>'
            , '<?php echo js_asset_url('datatables/js/dataTables.bootstrap.min.js') ?>'
            , '<?php echo js_asset_url('datatables-responsive/dataTables.responsive.js') ?>'
            , '<?php echo js_asset_url('select2/select2.min.js') ?>'
            , '<?php echo base_url('dist/js/public/project-table.js') ?>');
// send data to parent frame, only when this frame is fully loaded
    function load() {
        window.parent.postMessage(document.body.offsetHeight, '*');
    }
    window.onload = load;
</script>
<div class="col-lg-12">
    <!-- table-responsive -->
    <table width="100%" class="table table-striped table-bordered table-hover" id="projects-datatable">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th class="min-tablet-p">Latest status</th>
                <th class="min-tablet-p">Target</th>
                <th class="min-tablet-p">Progress (%)</th>
            </tr>
        </thead>
    </table>
    <!-- /.table-responsive -->
</div>

<!-- /.col-lg-12 -->
