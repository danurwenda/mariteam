<?php echo css_asset('select2/select2.min.css'); ?>
<?php echo css_asset('select2/themes/select2-bootstrap.min.css'); ?>
<script>
    $$.push(
            '<?php echo js_asset_url('bootbox/bootbox.js') ?>'
            , '<?php echo js_asset_url('select2/select2.min.js') ?>'
            , '<?php echo js_asset_url('jquery-form/jquery.form.min.js') ?>'
            , '<?php echo js_asset_url('jquery-validation/jquery.validate.min.js') ?>'
            , '<?php echo base_url('dist/js/people-form.js') ?>');
</script>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            Basic Information
        </div>
        <div class="panel-body">
            <?php if (isset($updated) && $updated) { ?>
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    Profile updated.
                </div>
                <?php
            }
            echo form_open(isset($person) ? 'people/update' : 'people/create');
            if (isset($person)) {
                echo form_hidden('person_id', $person->person_id);
            }
            if (isset($person->user_id)) {
                echo form_hidden('user_id', $person->user_id);
            }
            ?>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Full name</label>
                        <input required minlength="5" name="person_name" type="text" class="form-control" value="<?php echo isset($person) ? $person->person_name : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Institution</label>
                        <input required minlength="3" name="instansi" type="text" class="form-control" value="<?php echo isset($person) ? $person->instansi : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Position</label>
                        <input required minlength="3" name="jabatan" type="text" class="form-control" value="<?php echo isset($person) ? $person->jabatan : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input name="phone" type="text" class="form-control" value="<?php echo isset($person) ? $person->phone : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="multi-append" class="control-label">Groups</label>
                        <?php
                        $group_opts = [];
                        foreach ($groups as $u) {
                            $group_opts[$u->group_id] = $u->group_name;
                        }

                        $js = [
                            'id' => 'groups',
                            'class' => 'form-control select2'
                        ];
                        echo form_multiselect('groups[]', $group_opts, set_value('groups[]', isset($person) ? $person->groups : null ), $js);
                        ?>
                    </div>
                </div>
                <div class="col-lg-6 user-field">
                    <div class="form-group">
                        <label>Is a User? </label>
                        <input 
                        <?php
                        echo isset($person->user_id) ? 'disabled' : '';
                        echo set_checkbox('status', '0', isset($person->user_id));
                        ?> name="user" type="checkbox" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input name="email" type="email" class="form-control" value="<?php echo set_value('email', isset($person) ? $person->email : ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input class="form-control" placeholder="Password" name="password" id="password" type="password" value="">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input class="form-control" placeholder="Confirm Password" name="passconf" type="password" value="">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <div class="radio">
                            <label>
                                <input type="radio" name="status" value="0" <?php echo set_radio('status', '0', isset($person->status) ? $person->status == 0 : false); ?>>Blocked
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="status" value="1" <?php echo set_radio('status', '1', isset($person->status) ? $person->status == 1 : true); ?>>Active
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <?php foreach ($roles as $role) { ?>
                            <div class="radio">
                                <label class="apaini">
                                    <input type="radio" name="role" value="<?php echo $role->role_id ?>" <?php echo set_radio('role', $role->role_id, isset($person->role_id) ? $person->role_id == $role->role_id : false); ?>><?php echo $role->name; ?>
                                </label>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <!-- /.row (nested) -->
            <button type="submit" class="btn btn-default">Submit</button>
            <?php if (isset($person)) { ?>
                <a class="btn btn-danger" data-person_name="<?php echo $person->person_name; ?>" data-person_id="<?php echo $person->person_id; ?>">Remove</a>
            <?php } ?>
            </form>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
