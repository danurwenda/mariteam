$(document).ready(function () {

    //=================== FORMATTING
    $('#project-due-date-remain').html('(' + moment(new Date($('#project-due-date').text())).fromNow() + ')')
    function renderStatus(d) {
        switch (d) {
            case '1':
                return '<span class="label label-success">Active</span>'
                break;
            case '2':
                return '<span class="label label-info">Done</span>'
                break;
            case '3':
                return '<span class="label label-danger">Overdue</span>'
                break;
            case '4':
                return '<span class="label label-warning">Suspended</span>'
                break;
            default:
                return 'Undefined'
        }
    }
    // ====================== DATA TABLE =============================
    var tasks_table = $('#tasks-datatable').DataTable({
        order: [
            [0, "asc"]
        ],
        responsive: true,
        dom: '<"top">rt<"bottom"p><"clear">',
        paging: false,
        processing: true,
        ordering: false,
        serverSide: true,
        ajax: {
            url: $('#tasks-datatable').data('url'),
            type: 'POST',
            data: function (d) {
                d['project_id'] = $('.main-panel').data('project')
            }
        },
        columns: [
            {},
            //task name
            {
                render: function (d, t, f, m) {
                    return '<a href data-task="' + f[0] + '" data-toggle="modal" data-target="#task-modal-form"> ' + d + '</a>';
                },
                responsivePriority: 1
            },

            //due date
            {
                render: function (past, t, f, m) {
                    if (t === 'sort') {
                        return past;
                    } else {
                        var dpast = moment(past, 'YYYY-MM-DD')
                        return '<span data-toggle="tooltip" title="' + dpast.format('DD MMM YYYY') + '">' + dpast.format('MMM YYYY') + '</span>';
                    }
                }
            },
            //status
            {
                responsivePriority: 2,
                render: function (d) {
                    switch (d) {
                        case '1':
                            return '<span class="label label-success">Active</span>'
                            break;
                        case '2':
                            return '<span class="label label-info">Done</span>'
                            break;
                        case '3':
                            return '<span class="label label-danger">Overdue</span>'
                            break;
                        case '4':
                            return '<span class="label label-warning">Suspended</span>'
                            break;
                        default:
                            return 'Undefined'
                    }
                }
            },

            //order
            { visible: false, searchable: false }
        ]
    });
    tasks_table.on('order.dt search.dt draw.dt', function () {
        //biar kolom angka ga ikut ke sort
        var start = tasks_table.page.info().start;
        tasks_table.column(0, { order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = start + i + 1;
        });

    }).draw();
    // trigger the responsive data table to adjust its appearance when the tab is shown
    $('a[href="#task"]').on('shown.bs.tab', function (e) {
        tasks_table.responsive.recalc();
    });
    $(document).on("click", ".deldoc", function (e) {
        var el = $(this),
            uid = $(this).data('doc_id'),
            source = $(this).data('source');
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
                        data: {
                            "doc_id": uid
                        },
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
        dom: '<"top">rt<"bottom"p><"clear">',
        paging: false,
        processing: true,
        ordering: false,
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
                    return '<a href="' + base_url + 'download/' + f[4] + '">' + d + '</a>'
                }
            },
            //size
            {
                render: function (size) {
                    var i = size == 0 ? 0 : Math.floor(Math.log(size) / Math.log(1024));
                    return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
                }
            },
            //due date
            {
                render: function (past, t, f, m) {
                    if (t === 'sort') {
                        return past;
                    } else {
                        if (past) {
                            var dpast = moment(new Date(past))
                            return '<span data-toggle="tooltip" title="' + dpast.format('DD MMM YYYY, hh:mm') + '">' + dpast.format('DD MMMM YYYY') + '</span>';
                        }
                        return 'Never';
                    }
                }
            }
        ]
    });
    docs_table.on('draw.dt', function () {
        if (0 === docs_table.rows()[0].length) {
            // hide tab
            $('#docs-tab').hide()
        }
    })
    $('a[href="#documents"]').on('shown.bs.tab', function (e) {
        docs_table.responsive.recalc();
    });
    // ============== MODALS TO LOAD ENTITY ==========================
    // TASK
    function createDocEl(doc) {
        var docel = $('<li/>')
        docel.append('<a href="' + base_url + 'download/' + doc.dir + '"> ' + doc.filename + '</a>')

        return docel;
    }

    function reloadTaskDocs(task_id) {
        $.getJSON(base_url + 'publik/get_task_docs/' + task_id, function (docs) {
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
                        $('<div/>')
                            .addClass('chat-header')

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
            cmtel.find('div.chat-header').append(user.addClass('pull-right'))
            cmtel.find('.chat-img').addClass('pull-right')
        } else {
            cmtel.addClass('left')
            cmtel.find('div.chat-header').prepend(user)
            cmtel.find('.chat-img').addClass('pull-left')
            cmtel.find('.text-muted').addClass('pull-right')
        }
        return cmtel;
    }

    $('#task-modal-form').on('shown.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
            ,
            task_id = button.data('task'),
            modal = $(this);
        if (task_id) {
            //prepare form to be submitted for editing entry
            //populate form after ajax load
            $.getJSON(base_url + 'publik/get_task/' + task_id, function (task) {

                //read only
                modal.find('#task-name').html(task.task_name)
                modal.find('#task-start-date').html(moment(task.start_date).format('D MMM YYYY'))
                var due = moment(task.end_date);
                modal.find('#task-end-date').html(due.format('D MMM YYYY'))
                modal.find('#task-desc').html(task.description)
                modal.find('#task-due-date-remain').html(due.fromNow())
                modal.find('#task-weight').html(task.weight)

                modal.find('#task-status').html(
                    renderStatus(task.status)
                )
                modal.find('#task-assign').html(task.person_name)


            });

            // hide upload
            $('task-upload-row').removeClass('hide');
            reloadTaskDocs(task_id);

            // hide chat
            $('task-chat-panel').removeClass('hide');
            $.getJSON(base_url + 'publik/get_task_comment/' + task_id, function (cmts) {
                //clear list
                $('.comment-panel ul.chat').empty();
                cmts.forEach(function (cmt, i) {
                    $('.comment-panel ul.chat').append(createCommentEl(cmt));
                })
            })
        }
    });
    var events_table = $('#events-datatable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + 'publik/events_dt',
            type: 'POST',
            data: function (d) {
                d['project_id'] = $('.main-panel').data('project')
            }
        },
        columns: [
            {},
            {
                render: function (d, t, f, m) {
                    return '<a href data-toggle="modal" data-target="#event-modal-form"> ' + d + '</a>';
                }
            },
            {},
            {
                render: function (d, t, f, m) {
                    return moment(d).format("D MMMM YYYY HH:mm")
                }
            },
            {},
            { visible: false, searchable: false }, { visible: false, searchable: false }

        ]
    });
    events_table.on('draw.dt', function () {
        if (0 === events_table.rows()[0].length) {
            // hide tab
            $('#events-tab').hide()
        }
    })
    events_table.on('order.dt search.dt draw.dt', function () {
        //biar kolom angka ga ikut ke sort
        var start = events_table.page.info().start;
        events_table.column(0, { order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = start + i + 1;
        });

    }).draw();
    $('#event-modal-form').on('shown.bs.modal', function (event) {
        var button = $(event.relatedTarget), // Button that triggered the modal

            tr = button.parents('tr'), form = $('#event-modal-form'),
            rowdata = events_table.row(tr).data();
        form.find('.event-name').html(rowdata[1])
        form.find('.event-description').html(rowdata[5])
        form.find('.event-pic').html(rowdata[2])
        form.find('.event-location').html(rowdata[4])
        form.find('.event-start').html(moment(rowdata[3]).format("D-MMMM-YYYY HH:mm"))
        form.find('.event-end').html(moment(rowdata[6]).format("D-MMMM-YYYY HH:mm"))
    })
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
            data: {
                "task_id": task_id,
                "content": new_comment
            },
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
                        data: {
                            "project_id": uid
                        },
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
                        data: {
                            "task_id": uid
                        },
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