$(document).ready(function () {

    //=================== FORMATTING
    $('#task-date').datetimepicker({
        format: "DD-MMMM-YYYY HH:mm",
        maxDate: new Date($('#task-date').data('max'))
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

    $('#project-due-date-remain').html('(' + moment(new Date($('#project-due-date').text())).fromNow() + ')')

    // ====================== DATA TABLE =============================
    var tasks_table = $('#tasks-datatable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: $('#tasks-datatable').data('url'),
            type: 'POST',
            data: function (d) {
                d['project_id'] = $('.main-panel').data('project')
            }
        },
        columns: [
            //task name
            {
                render: function (d, t, f, m) {
                    return '<a href data-task="' + f[5] + '" data-toggle="modal" data-target="#task-modal-form"> ' + d + '</a>';
                }
            },
            //PIC
            {},
            //due date
            {
                render: function (past, t, f, m) {
                    if (t === 'sort') {
                        return past;
                    } else {
                        if (past)
                        {
                            var cls = '';
                            if (new Date() > new Date(f[2]) && f[3] !== '1') {
                                cls = 'alert-danger';
                            }
                            var dpast = moment(new Date(past))
                            return '<span class="' + cls + '" data-toggle="tooltip" title="' + dpast.format('DD MMM YYYY, hh:mm') + '">' + dpast.fromNow() + '</span>';
                        }
                        return 'Never';
                    }
                }
            },
            //status
            {render: function (d) {
                    switch (d) {
                        case '0':
                            return 'Ongoing'
                            break;
                        case '1':
                            return 'Done'
                            break;
                        default:
                            return ''
                    }
                }
            },
            //weight
            {}
        ]
    });
    $(document).on("click", ".deldoc", function (e) {
        var el = $(this), uid = $(this).data('doc_id'), source = $(this).data('source');
        bootbox.confirm({
            message: "Are you sure you want to remove this document?",
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
                        data: {"doc_id": uid},
                        url: base_url + 'project/delete_doc',
                        type: 'POST',
                        success: function (result) {
                            if (source == 'projects') {
                                //reload table
                                docs_table.ajax.reload()
                            } else if (source == 'tasks') {
                                //delete itself
                                el.parent('li').remove();
                            }
                        }
                    });
                }
            }
        })
    });
    var docs_table = $('#docs-datatable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: $('#docs-datatable').data('url'),
            type: 'POST',
            data: function (d) {
                d['project_id'] = $('.main-panel').data('project')
            }
        },
        columns: [
            //file name
            {
                render: function (d, t, f, m) {
                    return '<a href="' + base_url + 'uploads/' + f[4] + '/' + d + '"> ' + d + '</a>';
                }
            },
            //size
            {},
            //due date
            {
                render: function (past, t, f, m) {
                    if (t === 'sort') {
                        return past;
                    } else {
                        if (past)
                        {
                            var dpast = moment(new Date(past))
                            return '<span data-toggle="tooltip" title="' + dpast.format('DD MMM YYYY, hh:mm') + '">' + dpast.fromNow() + '</span>';
                        }
                        return 'Never';
                    }
                }
            },
            //doc_id, rendered as delete link
            {
                render: function (id) {
                    return '<span class="deldoc btn btn-danger" data-source="projects" data-doc_id=' + id + '> Remove</span>'
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
    function createDocEl(doc) {
        var docel = $('<li/>')
        docel.append('<a href="' + base_url + 'uploads/' + doc.dir + '/' + doc.filename + '"> ' + doc.filename + '</a>')
        var deldoc = $('<span class="deldoc deldocbtn">')
                .attr('data-doc_id', doc.document_id)
                .attr('data-source', 'tasks');
        deldoc.append('<i class="fa fa-times-circle-o"></i>')
        docel.append(deldoc)
        return docel;
    }
    function reloadTaskDocs(task_id) {
        $.getJSON(base_url + 'project/get_task_docs/' + task_id, function (docs) {
            //clear list
            $('#task-docs').empty();
            docs.forEach(function (doc, i) {
                $('#task-docs').append(createDocEl(doc));
            })
        })
    }
    function createCommentEl(cmt) {
        var cmtel = $('<li/>').addClass('clearfix');
        //craft initial
        var initial = '';
        var usernames = cmt.user.split(' ');
        if (usernames[0]) {
            initial += usernames[0].charAt(0)
        }
        if (usernames[1]) {
            initial += usernames[1].charAt(0)
        }
        //initial
        cmtel
                .append(
                        $('<span/>')
                        .addClass('chat-img')
                        .attr('data-letters', initial))
                .append(
                        $('<div/>')
                        .addClass('chat-body clearfix')
                        .append(
                                $('<div/>').addClass('header')

                                .append(
                                        $('<small/>')
                                        .addClass('text-muted')
                                        .append(
                                                $('<i class="fa fa-clock-o fa-fw"></i>')
                                                )
                                        .append(
                                                $('<span/>')
                                                .addClass('comment-time')
                                                .html(moment(cmt.time).fromNow())
                                                )

                                        )
                                )
                        .append($('<p/>').html(cmt.content))
                        )
        var user = $('<strong/>').addClass('primary-font').html(cmt.user);
        if (cmt.self) {
            cmtel.addClass('right')
            cmtel.find('div.header').append(user.addClass('pull-right'))
            cmtel.find('.chat-img').addClass('pull-right')
        } else {
            cmtel.addClass('left')
            cmtel.find('div.header').prepend(user)
            cmtel.find('.chat-img').addClass('pull-left')
            cmtel.find('.text-muted').addClass('pull-right')
        }
        return cmtel;
    }
    $('#task-modal-form').on('shown.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
                , task_id = button.data('task')
                , modal = $(this)
                , form = modal.find('.modal-body').hasClass('modal-form');
        if (task_id) {
            //prepare form to be submitted for editing entry
            //populate form after ajax load
            $.getJSON(base_url + 'project/get_task/' + task_id, function (task) {
                if (form) {
                    modal.find('#task-name').val(task.task_name)
                    modal.find('[name=task_id]').val(task.task_id)
                    modal.find('#task-desc').val(task.description)
                    modal.find('#is_done')
                            .prop('checked', '1' === task.is_done)
                            .parent()
                            .parent()
                            .removeClass('hide')
                    modal.find('#task-assign').val(task.assigned_to)
                    modal.find('#task-date').data("DateTimePicker").date(new Date(task.due_date));
                    modal.find('#task-weight').val(task.weight).trigger('change')
                    //show "Remove" button
                    modal.find('.btn-danger')
                            .data('task_name', task.task_name)
                            .data('task_id', task.task_id)
                            .removeClass('hide')
                } else {
                    //read only
                    modal.find('#task-name').html(task.task_name)
                    var due = moment(task.due_date);
                    modal.find('#task-due-date').html(due.format('D MMM YYYY'))
                    modal.find('#task-desc').html(task.description)
                    modal.find('#task-due-date-remain').html(due.fromNow())
                    modal.find('#task-weight').html(task.weight)
                    modal.find('#task-status').html(task.is_done == 1 ? 'Done' : (new Date() > new Date(task.due_date) ? 'Ongoing' : 'Overdue'))
                    modal.find('#task-assign').html(task.user_name)
                }
                $('#fine-uploader-manual-trigger-task').fineUploader('setEndpoint', base_url + 'project/uploads/tasks/' + task.task_id)
                modal.find('.comment-panel #btn-chat').data('task_id', task.task_id)
            });

            reloadTaskDocs(task_id);


            $.getJSON(base_url + 'project/get_task_comment/' + task_id, function (cmts) {
                //clear list
                $('.comment-panel ul.chat').empty();
                cmts.forEach(function (cmt, i) {
                    $('.comment-panel ul.chat').append(createCommentEl(cmt));
                })
            })
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
            $('#task-modal-form form').find('#task-weight').val(3).trigger('change')
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

    // ======================== ACTION LISTENER =============================
    // add comment
    $('.comment-panel #btn-input').keyup(function () {
        //enable button only if comment is not empty
        $('.comment-panel #btn-chat').prop('disabled', ($(this).val().length < 2))
    })
    $('.comment-panel #btn-chat').click(function (e) {
        //post comment using ajax
        var new_comment = $(this).parent().prev().val();
        var task_id = $(this).data('task_id');
        $.ajax({
            data: {"task_id": task_id, "content": new_comment},
            url: base_url + 'project/add_task_comment',
            type: 'POST',
            dataType: 'json',
            success: function (result) {
                //append new comment
                $('.comment-panel ul.chat').append(createCommentEl(result));
                //clear input
                $('.comment-panel #btn-input').val('')
            }
        });
    });
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
    //=======================FILE UPLOAD
    $('#fine-uploader-manual-trigger-project').fineUploader({
        template: 'qq-template-manual-trigger',
        request: {
            endpoint: base_url + 'project/uploads/projects/' + $('#fine-uploader-manual-trigger-project').data('project')
        },
        thumbnails: {
            placeholders: {
                waitingPath: base_url + 'vendor/fine-uploader/waiting-generic.png',
                notAvailablePath: base_url + 'vendor/fine-uploader/not_available-generic.png'
            }
        },
        autoUpload: false,
        callbacks: {
            onAllComplete: function () {
                docs_table.ajax.reload()
                $('#documents .qq-upload-list').empty();
            }
        }
    });
    $('#fine-uploader-manual-trigger-task').fineUploader({
        template: 'qq-template-manual-trigger',
        request: {
            endpoint: ''
        },
        thumbnails: {
            placeholders: {
                waitingPath: base_url + 'vendor/fine-uploader/waiting-generic.png',
                notAvailablePath: base_url + 'vendor/fine-uploader/not_available-generic.png'
            }
        },
        autoUpload: false,
        callbacks: {
            onAllComplete: function () {
                //TODO : reload list
                reloadTaskDocs($('.comment-panel #btn-chat').data('task_id'))
                $('#task-modal-form .qq-upload-list').empty();
            }
        }
    });



    $('#task-modal-form .trigger-upload').click(function () {
        $('#fine-uploader-manual-trigger-task').fineUploader('uploadStoredFiles');
    })

    $('#documents .trigger-upload').click(function () {
        $('#fine-uploader-manual-trigger-project').fineUploader('uploadStoredFiles');
    });
})