
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            Basic Information
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-6">
            <?php if (isset($updated)&&$updated) { ?>
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    Profile updated.
                </div>
            <?php } ?>
                    <?php echo form_open('user/update'); ?>
                    <div class="form-group">
                        <label>Email</label>
                        <input name="email" type="email" class="form-control" value="<?php echo set_value('email', $_loggeduser->email); ?>">
                        <?php echo form_error('email', '<div class="has-error"><label class="control-label">', '</label></div>'); ?>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input class="form-control" placeholder="Password" name="password" type="password" value="">
                        <?php echo form_error('password', '<div class="has-error"><label class="control-label">', '</label></div>'); ?>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input class="form-control" placeholder="Confirm Password" name="passconf" type="password" value="">
                        <?php echo form_error('passconf', '<div class="has-error"><label class="control-label">', '</label></div>'); ?>
                    </div>
                    <button type="submit" class="btn btn-default">Update Profile</button>
                    </form>
                </div>
            </div>
            <!-- /.row (nested) -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
