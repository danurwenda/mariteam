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
            , '<?php echo base_url('dist/js/project-table.js') ?>');
</script>
<div class="col-xs-12 widget-container-col" id="widget-container-col-1">
    <div class="widget-box" id="widget-box-1">
        <div class="widget-header">
            <h5 class="widget-title">Project Filter</h5>

            <div class="widget-toolbar">
                <a href="#" data-action="collapse">
                    <i class="ace-icon fa fa-chevron-up"></i>
                </a>
            </div>
        </div>

        <div class="widget-body">
            <div class="widget-main">
                <form>
                    <div class="form-group">
                        <label for="multi-append" class="control-label">Topics</label>
                        <div class="input-group">
                            <select multiple="multiple" name="topics" id="topics"></select>
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-sm" type="button" data-toggle="modal" data-target="#topic-modal-form">
                                    <span class="glyphicon glyphicon-plus-sign"></span>
                                </button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="col-xs-12">
    <div class="pull-right">
        <?php if ($admin) { ?>
            <span><a class="btn btn-primary btn-raised pull-right" href="<?php echo site_url('project/create'); ?>"><i class="fa fa-cubes"></i> Create</a></span>
        <?php } ?>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            List of Project
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <table width="100%" class="table table-striped table-bordered table-hover" id="projects-datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>PIC</th>
                        <th>Status</th>
                        <th>Due date</th>
                        <th>Progress (%)</th>
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
