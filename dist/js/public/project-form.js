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
                    return '<a href="' + base_url + 'download/' + f[4] + '">' + d + '</a>'
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
                        if (past) {
                            var dpast = moment(new Date(past))
                            return '<span data-toggle="tooltip" title="' + dpast.format('DD MMM YYYY, hh:mm') + '">' + dpast.fromNow() + '</span>';
                        }
                        return 'Never';
                    }
                }
            }
        ]
    });
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

    /////////////////////////////// GANTT
    var canWrite = true; //this is the default for test purposes
    function initGE() {
        // here starts gantt initialization
        ge = new GanttMaster();
        ge.permissions.canSeePopEdit = false;
        ge.resourceUrl = base_url + 'vendor/jquery-gantt/res/';
        ge.set100OnClose = true;

        var workSpace = $('#workSpace');
        ge.init(workSpace);
        workSpace.css({
            //    width:$(window).width()-20,
            height: $(window).height() - 100
        });
        loadI18n(); //overwrite with localized ones

        //in order to force compute the best-fitting zoom level
        delete ge.gantt.zoom;


        loadFromServer();





        ge.editor.element.oneTime(100, "cl", function () {
            $(this).find("tr.emptyRow:first").click()
        });


    }
    // lazy init GE on the first time tab shown
    $('a[href="#timeline"]').on('shown.bs.tab', function (e) {
        if (!ge)
            initGE();
    });

    function loadFromServer(callback) {
        $.getJSON(base_url + "publik/get_timeline", {
            project_id: $('.main-panel').data('project')
        }, function (response) {
            //console.debug(response);
            if (response.ok) {
                ge.loadProject(response.project);
                ge.checkpoint(); //empty the undo stack
                if (!response.project.canWrite) {
                    $(".ganttButtonBar button.requireWrite").attr("disabled", "true");
                }
                if (typeof (callback) == "function") {
                    callback(response);
                }
            } else {
                jsonErrorHandling(response);
            }
        });

    }



    //-------------------------------------------  Create some demo data ------------------------------------------------------

    $.JST.loadDecorator("RESOURCE_ROW", function (resTr, res) {
        resTr.find(".delRes").click(function () {
            $(this).closest("tr").remove()
        });
    });

    $.JST.loadDecorator("ASSIGNMENT_ROW", function (assigTr, taskAssig) {
        var resEl = assigTr.find("[name=resourceId]");
        var opt = $("<option>");
        resEl.append(opt);
        for (var i = 0; i < taskAssig.task.master.resources.length; i++) {
            var res = taskAssig.task.master.resources[i];
            opt = $("<option>");
            opt.val(res.id).html(res.name);
            if (taskAssig.assig.resourceId == res.id)
                opt.attr("selected", "true");
            resEl.append(opt);
        }
        var roleEl = assigTr.find("[name=roleId]");
        for (var i = 0; i < taskAssig.task.master.roles.length; i++) {
            var role = taskAssig.task.master.roles[i];
            var optr = $("<option>");
            optr.val(role.id).html(role.name);
            if (taskAssig.assig.roleId == role.id)
                optr.attr("selected", "true");
            roleEl.append(optr);
        }

        if (taskAssig.task.master.permissions.canWrite && taskAssig.task.canWrite) {
            assigTr.find(".delAssig").click(function () {
                var tr = $(this).closest("[assId]").fadeOut(200, function () {
                    $(this).remove()
                });
            });
        }

    });

    function loadI18n() {
        GanttMaster.messages = {
            "CANNOT_WRITE": "No permission to change the following task:",
            "CHANGE_OUT_OF_SCOPE": "Project update not possible as you lack rights for updating a parent project.",
            "START_IS_MILESTONE": "Start date is a milestone.",
            "END_IS_MILESTONE": "End date is a milestone.",
            "TASK_HAS_CONSTRAINTS": "Task has constraints.",
            "GANTT_ERROR_DEPENDS_ON_OPEN_TASK": "Error: there is a dependency on an open task.",
            "GANTT_ERROR_DESCENDANT_OF_CLOSED_TASK": "Error: due to a descendant of a closed task.",
            "TASK_HAS_EXTERNAL_DEPS": "This task has external dependencies.",
            "GANNT_ERROR_LOADING_DATA_TASK_REMOVED": "GANNT_ERROR_LOADING_DATA_TASK_REMOVED",
            "CIRCULAR_REFERENCE": "Circular reference.",
            "CANNOT_DEPENDS_ON_ANCESTORS": "Cannot depend on ancestors.",
            "INVALID_DATE_FORMAT": "The data inserted are invalid for the field format.",
            "GANTT_ERROR_LOADING_DATA_TASK_REMOVED": "An error has occurred while loading the data. A task has been trashed.",
            "CANNOT_CLOSE_TASK_IF_OPEN_ISSUE": "Cannot close a task with open issues",
            "TASK_MOVE_INCONSISTENT_LEVEL": "You cannot exchange tasks of different depth.",
            "GANTT_QUARTER_SHORT": "Quarter",
            "GANTT_SEMESTER_SHORT": "Sem",
            "CANNOT_MOVE_TASK": "CANNOT_MOVE_TASK",
            "PLEASE_SAVE_PROJECT": "PLEASE_SAVE_PROJECT"
        };
    }




})