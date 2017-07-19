$(document).ready(function () {
    $('#task_date').datetimepicker({
        format: "DD-MMMM-YYYY HH:mm",
        maxDate: new Date($('#task_date').data('max'))
    });
    $('#project_date').datetimepicker({
        format: "DD-MMMM-YYYY HH:mm"
    });



    $('#project_assign_to').select2({
        theme: "bootstrap"})
    $('#topics').select2({
        theme: "bootstrap",
        ajax: {
            url: base_url + 'project/get_topics',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term
                };
            },
            processResults: function (data, params) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.topic_name,
                            id: item.topic_id
                        }
                    })
                };
            },
            cache: true
        }
        , escapeMarkup: function (markup) {
            return markup;
        }
    });
    $('#assign-task').select2({
        dropdownParent: $('#task-modal-form'),
        theme: "bootstrap"
    });
    $('.knob').knob()

    var placeholder = "Select a State";

    // ====================== TASK TABLE =============================
    var tasks_table = $('#tasks-datatable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: $('#tasks-datatable').data('url'),
            type: 'POST',
            data: function (d) {
                d['project_id'] = $('#project_form input[name=project_id]').val()
            }
        },
        columns: [
            //task name
            {},
            //PIC
            {},
            //due date
            {},
            //status
            {},
            //weight
            {},
            //edit
            {
                render: function (d, t, f, m) {
                    return '<a data-task="' + d + '" class="btn btn-primary btn-raised pull-right" data-toggle="modal" data-target="#task-modal-form"> Edit</a>';
                }
            }
        ]
    });

    // ============== MODALS TO LOAD ENTITY ==========================
    // TOPIC
    $('#topic-modal-form .btn-primary').click(function (e) {
        var form = $('#topic-modal-form form')
                //serialize the form, except those in hidden template
                , h = form.find(":input:not(.template :input)").serialize()
                // process the form
                , action = form.attr('action');
        $.ajax({
            type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url: action, // the url where we want to POST
            data: h, // our data object
            dataType: 'json', // what type of data do we expect back from the server
            encode: true
        })
                // using the done promise callback
                .done(function (data) {
                    //reset and close modal
                    form[0].reset();
                    //reset expandable
                    $('#topic-modal-form').modal('hide');
                });
    });
    // TASK
    $('#task-modal-form').on('shown.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
                , task_id = button.data('task')
                , modal = $(this);
        if (task_id) {
            //prepare form to be submitted for editing entry
            //populate form after ajax load
            $.getJSON(base_url + 'project/get_task/' + task_id, function (task) {
                modal.find('#task_name').val(task.task_name)
                modal.find('[name=task_id]').val(task.task_id)
                modal.find('#task_desc').val(task.description)
                modal.find('#is_done')
                        .prop('checked', '1' === task.is_done)
                        .parent()
                        .parent()
                        .removeClass('hide')
                modal.find('#task_assign').val(task.assigned_to)
                modal.find('#task_date').data("DateTimePicker").date(new Date(task.due_date));
                modal.find('#task_weight').val(task.weight).trigger('change')
                //show "Remove" button
                modal.find('.btn-danger')
                        .data('task_name', task.task_name)
                        .data('task_id', task.task_id)
                        .removeClass('hide')
            });
        } else {
            //create
            modal.find('[name=task_id]').val('')
            //hide "Remove" button
            modal.find('.btn-danger')
                    .removeData('task_name')
                    .removeData('task_id')
                    .addClass('hide')
            // hide status
            modal.find('#is_done').parent().parent().addClass('hide')
            //reset form
            $('#task-modal-form form')[0].reset();
            $('#task-modal-form form').find('#task_weight').val(3).trigger('change')
        }
    });


    var task_validator = $('#task-modal-form form').validate();
    $('#task-modal-form .btn-primary').click(function (e) {
        var form = $('#task-modal-form form')
                //serialize the form, except those in hidden template
                , action = form.attr('action')
                , h = form.find(":input:not(.template :input)").serialize();
        // validate form
        if (task_validator.form()) {
            // process the form
            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: action, // the url where we want to POST
                data: h, // our data object
                dataType: 'json', // what type of data do we expect back from the server
                encode: true
            })
                    // using the done promise callback
                    .done(function (data) {
                        //refresh table
                        tasks_table.ajax.reload()
                        //close modal
                        $('#task-modal-form').modal('hide');
                    });
        }
    });

    // ======================== REMOVE ENTITY =============================
    // remove project
    $('#project_form .btn-danger').on("click", function (e) {
        var name = $(this).data('project_name')
        var uid = $(this).data('project_id')
        bootbox.confirm({
            message: "Are you sure you want to remove the project " + name + "?",
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-danger'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-default'
                }
            },
            callback: function (result) {
                if (result) {
                    $.ajax({
                        data: {"project_id": uid},
                        url: base_url + 'project/delete',
                        type: 'POST',
                        success: function (result) {
                            // Redirect to table view
                            window.location.href = base_url + "project"
                        }
                    });
                }
            }
        })
    });
    // remove task
    $('#task-modal-form .btn-danger').on("click", function (e) {
        var name = $(this).data('task_name')
        var uid = $(this).data('task_id')
        bootbox.confirm({
            message: "Are you sure you want to remove the task " + name + "?",
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-danger'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-default'
                }
            },
            callback: function (result) {
                if (result) {
                    $.ajax({
                        data: {"task_id": uid},
                        url: base_url + 'project/delete_task',
                        type: 'POST',
                        success: function (result) {
                            //refresh table
                            tasks_table.ajax.reload()
                            //close modal
                            $('#task-modal-form').modal('hide');
                        }
                    });
                }
            }
        })
    });
})