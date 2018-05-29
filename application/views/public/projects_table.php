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
</script>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            List of Project
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <p> 
                <button class="btn btn-submit" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    Filters
                </button>
            </p>
            <div class="collapse" id="collapseExample">
                <div class="well">
                    <div class="form-group">
                        <label for="multi-append" class="control-label">Project Group</label>
                        <select class="form-control" multiple="multiple" name="groups" id="groups"></select>
                    </div>
                </div>

                <!--div class="widget-toolbox padding-8 clearfix">
                    <button class="btn" type="reset">
                        <i class="ace-icon fa fa-undo bigger-110"></i>
                        Reset
                    </button>
                </div-->

            </div>
            <table width="100%" class="table table-striped table-bordered table-hover" id="projects-datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
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
