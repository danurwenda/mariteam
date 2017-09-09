<script>
    $$.push(
            '<?php echo js_asset_url('bootbox/bootbox.js') ?>'
            , '<?php echo base_url('dist/js/people-form.js') ?>');
</script>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            Basic Information
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-6">
                    <?php if (isset($updated) && $updated) { ?>
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            Profile updated.
                        </div>
                        <?php
                    }
                    echo form_open(isset($user) ? 'people/update' : 'people/create');
                    if (isset($user)) {
                        echo form_hidden('user_id', $user->user_id);
                    }
                    ?>
                    <div class="form-group">
                        <label>Email</label>
                        <input name="email" type="email" class="form-control" value="<?php echo set_value('email', isset($user) ? $user->email : ''); ?>">
                        <?php echo form_error('email', '<div class="has-error"><label class="control-label">', '</label></div>'); ?>
                    </div>
                    <div class="form-group">
                        <label>Full name</label>
                        <input name="name" type="text" class="form-control" value="<?php echo set_value('name', isset($user) ? $user->person_name : ''); ?>">
                        <?php echo form_error('name', '<div class="has-error"><label class="control-label">', '</label></div>'); ?>
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
                    <div class="form-group">
                        <label>Status</label>
                        <div class="radio">
                            <label>
                                <input type="radio" name="status" value="0" <?php echo set_radio('status', '0', isset($user) ? $user->status == 0 : false); ?>>Blocked
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="status" value="1" <?php echo set_radio('status', '1', isset($user) ? $user->status == 1 : true); ?>>Active
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <?php foreach ($roles as $role) { ?>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="role" value="<?php echo $role->role_id ?>" <?php echo set_radio('role', $role->role_id, isset($user) ? $user->role_id == $role->role_id : false); ?>><?php echo $role->name; ?>
                                </label>
                            </div>
                        <?php } ?>
                        <?php echo form_error('role', '<div class="has-error"><label class="control-label">', '</label></div>'); ?>
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                    <?php if (isset($user)) { ?>
                        <a class="btn btn-danger" data-person_name="<?php echo $user->person_name;?>" data-user_id="<?php echo $user->user_id;?>">Remove</a>
                    <?php } ?>
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
