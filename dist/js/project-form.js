$(document).ready(function () {

    //=================== FORMATTING
    $('input#task-start-date').datetimepicker({
        format: "DD-MMMM-YYYY",
        //minDate: new Date($('#task-start-date').data('min'))
    });
    $('input#task-end-date').datetimepicker({
        format: "DD-MMMM-YYYY",
        //maxDate: new Date($('#task-end-date').data('max'))
    });

    $('#project_start_date').datetimepicker({
        format: "DD-MMMM-YYYY"
    });
    $('#project_end_date').datetimepicker({
        format: "DD-MMMM-YYYY", useCurrent: false
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

    $('select#task-assign').select2({
        dropdownParent: $('#task-modal-form'),
        theme: "bootstrap"
    });

    $('.knob').knob()

    $('#project-due-date-remain').html('(' + moment($('#project-due-date').text(), 'D-MMMM-YYYY HH:mm').fromNow() + ')')
    function renderStatus(d) {
        switch (d) {
            case '1':
                return '<span class="label label-success">Active</span>'
                break;
            case '2':
                return '<span class="label label-info">Done</span>'
                break;
            case '3':
                return '<span class="label label-danger">Failed</span>'
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
        order: [[2, "desc"]],
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
                            if (new Date() > new Date(f[2]) && f[3] !== '2') {
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
            {render: renderStatus
            },
            //weight
            {}
        ]
    });

    // since we use responsive datatables INSIDE a tabbed panel,
    // we need to trigger the calculating of table's width after the table is
    // displayed

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
                    return '<a href="' + base_url + 'download/' + f[4] + '"> ' + d + '</a>';
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
                render: function (id, t, f, m) {
                    if (f[6])
                        return '<span class="deldoc btn btn-danger" data-source="projects" data-doc_id=' + id + '> Remove</span>'
                    else
                        return ''
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
        docel.append('<a href="' + base_url + 'download/' + doc.dir + '"> ' + doc.filename + '</a>')
        if (doc.self) {
            var deldoc = $('<span class="deldoc deldocbtn">')
                    .attr('data-doc_id', doc.document_id)
                    .attr('data-source', 'tasks');
            deldoc.append('<i class="fa fa-times-circle-o"></i>')
            docel.append(deldoc)
        }
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

                    modal.find('[name=task_status]').val([task.status])

                    modal.find('#task-assign').val(task.assigned_to)
                    modal.find('#task-start-date').data("DateTimePicker").date(new Date(task.start_date));
                    modal.find('#task-end-date').data("DateTimePicker").date(new Date(task.end_date));
                    modal.find('#task-weight').val(task.weight).trigger('change')
                    //show "Remove" button
                    modal.find('.btn-danger')
                            .data('task_name', task.task_name)
                            .data('task_id', task.task_id)
                            .removeClass('hide')
                } else {
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
                }
                $('#fine-uploader-manual-trigger-task').fineUploader('setEndpoint', base_url + 'project/uploads/tasks/' + task_id)
                modal.find('.comment-panel #btn-chat').data('task_id', task_id)
            });

            // show chat
            $('#task-chat-panel').removeClass('hide');
            // show upload
            $('#task-uploaded').removeClass('hide');
            $('#task-upload').addClass('col-md-6');
            $('.trigger-upload').removeClass('hide')
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
            // hide chat
            $('#task-chat-panel').addClass('hide');
            // hide upload
            $('#task-uploaded').addClass('hide');
            $('#task-upload').removeClass('col-md-6');
            $('.trigger-upload').addClass('hide')
            //reset form
            $('#task-modal-form #task-form')[0].reset();
            $('#task-modal-form #task-form')
                    .find('#task-weight').val(3)
                    .trigger('change')
        }
    });


    var task_validator = $('#task-modal-form #task-form').validate();
    $('#task-modal-form .btn-subm').click(function (e) {
        var form = $('#task-modal-form #task-form')
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
                        //refresh task table
                        tasks_table.ajax.reload()
                        //refresh timeline if it's already loaded
                        if (ge)
                            loadFromServer()
                        //submit outstanding files (if any)
                        if ($('#fine-uploader-manual-trigger-task').fineUploader('getUploads').length > 0) {
                            $('#fine-uploader-manual-trigger-task').fineUploader('setEndpoint', base_url + 'project/uploads/tasks/' + data.task_id)
                            $('#fine-uploader-manual-trigger-task').fineUploader('uploadStoredFiles')
                        }
                        //close modal
                        $('#task-modal-form').modal('hide');

                    });
        }
    });

    // ======================== ACTION LISTENER =============================
    // add person
    // create person as user will toggle email column
    $('.new-person-form').on('change', '[name=is_user]', function () {
        $(this).parent().parent().next().toggle();
    })
    $('.add-person-btn').click(function (e) {
        var
                btn = $(this),
                select = btn.parent().parent().find('select'),
                person_form = $('.new-person-form').clone(true).removeClass('hide'),
                is_user = person_form.find("[name=is_user]"),
                person_validator = person_form.validate(
                        {
                            rules: {
                                email: {
                                    required: {
                                        depends: function (element) {
                                            return is_user.is(":checked");
                                        }
                                    },
                                    email: {
                                        depends: function (element) {
                                            return is_user.is(":checked");
                                        }
                                    }
                                }
                            }
                        }
                );
        var dialog = bootbox.dialog({
            title: 'Add new Person',
            message: person_form,
            buttons: {
                cancel: {
                    label: '<i class="ace-icon fa fa-times"></i>Cancel',
                    className: 'btn-sm'
                },
                submit: {
                    label: '<i class="ace-icon fa fa-check"></i>Submit',
                    className: 'btn-sm btn-primary',
                    callback: function () {
                        var h = person_form.find(":input:not(.template :input)").serialize();
                        if (person_validator.form()) {
                            // process the form
                            $.ajax({
                                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                                url: person_form.attr('action'), // the url where we want to POST
                                data: h, // our data object
                                dataType: 'json', // what type of data do we expect back from the server
                                encode: true
                            })
                                    // using the done promise callback
                                    .done(function (data) {
                                        if (data.success) {
                                            //insert the new person and make it selected
                                            var option = new Option(data.person_name, data.success);
                                            option.selected = true;

                                            select.append(option);
                                            select.trigger("change");
                                        }

                                        //close modal
                                        dialog.modal('hide');


                                    });
                        }
                        return false;
                    }
                },
            }
        });
        //since this dialog might be displayed above another modal..
        //https://stackoverflow.com/questions/31187708/show-bootbox-over-modal-dialog
        dialog.one('hidden.bs.modal', function () {
            if ($('.modal.in').css('display') == 'block')
                $('body').addClass('modal-open')
        })
    })
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
            onComplete: function (i, n, r) {
                r.filename = r.uploadName;
                r.dir = r.uuid;
                $('#task-docs').append(createDocEl(r));
            },
            onAllComplete: function () {
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
            height: $(window).height() - 280
        });
        loadI18n(); //overwrite with localized ones

        //bind save button
        $('#save-timeline').click(saveGanttOnServer);

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
        $.getJSON(base_url + "project/get_timeline", {
            project_id: $('.main-panel').data('project')
        }, function (response) {
            //console.debug(response);
            if (response.ok) {
                ge.loadProject(response.project);
                ge.checkpoint(); //empty the undo stack
                if (!response.project.canWrite) {
                    $(".ganttButtonBar .requireWrite").css("display", "none");
                    $(".ganttButtonBar .requireCanWrite").css("display", "none");
                }
                if (typeof (callback) == "function") {
                    callback(response);
                }
            }
        });

    }



    //-------------------------------------------  Create some demo data ------------------------------------------------------


    function loadI18n() {
        GanttMaster.messages = {
            "CANNOT_WRITE": "CANNOT_WRITE",
            "CHANGE_OUT_OF_SCOPE": "NO_RIGHTS_FOR_UPDATE_PARENTS_OUT_OF_EDITOR_SCOPE",
            "START_IS_MILESTONE": "START_IS_MILESTONE",
            "END_IS_MILESTONE": "END_IS_MILESTONE",
            "TASK_HAS_CONSTRAINTS": "TASK_HAS_CONSTRAINTS",
            "GANTT_ERROR_DEPENDS_ON_OPEN_TASK": "GANTT_ERROR_DEPENDS_ON_OPEN_TASK",
            "GANTT_ERROR_DESCENDANT_OF_CLOSED_TASK": "GANTT_ERROR_DESCENDANT_OF_CLOSED_TASK",
            "TASK_HAS_EXTERNAL_DEPS": "TASK_HAS_EXTERNAL_DEPS",
            "GANTT_ERROR_LOADING_DATA_TASK_REMOVED": "GANTT_ERROR_LOADING_DATA_TASK_REMOVED",
            "ERROR_SETTING_DATES": "ERROR_SETTING_DATES",
            "CIRCULAR_REFERENCE": "CIRCULAR_REFERENCE",
            "CANNOT_DEPENDS_ON_ANCESTORS": "CANNOT_DEPENDS_ON_ANCESTORS",
            "CANNOT_DEPENDS_ON_DESCENDANTS": "CANNOT_DEPENDS_ON_DESCENDANTS",
            "INVALID_DATE_FORMAT": "INVALID_DATE_FORMAT",
            "TASK_MOVE_INCONSISTENT_LEVEL": "TASK_MOVE_INCONSISTENT_LEVEL",

            "GANTT_QUARTER_SHORT": "trim.",
            "GANTT_SEMESTER_SHORT": "sem."
        };
    }



    //-------------------------------------------  Get project file as JSON (used for migrate project from gantt to Teamwork) ------------------------------------------------------
    function getFile() {
        $("#gimBaPrj").val(JSON.stringify(ge.saveProject()));
        $("#gimmeBack").submit();
        $("#gimBaPrj").val("");

        /*  var uriContent = "data:text/html;charset=utf-8," + encodeURIComponent(JSON.stringify(prj));
         neww=window.open(uriContent,"dl");*/
    }

    function saveGanttOnServer() {
        var n = new Noty({
            timeout: false,
            layout: 'center',
            text: 'Saving to server',
        }).show();
        var prj = ge.saveProject();

        delete prj.resources;
        delete prj.roles;

        if (ge.deletedTaskIds.length > 0) {
            if (!confirm("TASK_THAT_WILL_BE_REMOVED\n" + ge.deletedTaskIds.length)) {
                return;
            }
        }

        $.ajax(base_url + "/project/save_timeline", {
            dataType: "json",
            data: {
                project_id: $('.main-panel').data('project'),
                timeline: JSON.stringify(prj)
            },
            type: "POST",

            success: function (response) {
                if (response.ok) {
                    if (response.project) {
                        n.close()
                    } else {
                        ge.reset();
                    }
                } else {
                    var errMsg = "Errors saving project\n";
                    if (response.message) {
                        errMsg = errMsg + response.message + "\n";
                    }

                    if (response.errorMessages.length) {
                        errMsg += response.errorMessages.join("\n");
                    }

                    alert(errMsg);
                }
            }

        });

    }

    //-------------------------------------------  Open a black popup for managing resources. This is only an axample of implementation (usually resources come from server) ------------------------------------------------------
    function editResources() {

        //make resource editor
        var resourceEditor = $.JST.createFromTemplate({}, "RESOURCE_EDITOR");
        var resTbl = resourceEditor.find("#resourcesTable");

        for (var i = 0; i < ge.resources.length; i++) {
            var res = ge.resources[i];
            resTbl.append($.JST.createFromTemplate(res, "RESOURCE_ROW"))
        }


        //bind add resource
        resourceEditor.find("#addResource").click(function () {
            resTbl.append($.JST.createFromTemplate({
                id: "new",
                name: "resource"
            }, "RESOURCE_ROW"))
        });

        //bind save event
        resourceEditor.find("#resSaveButton").click(function () {
            var newRes = [];
            //find for deleted res
            for (var i = 0; i < ge.resources.length; i++) {
                var res = ge.resources[i];
                var row = resourceEditor.find("[resId=" + res.id + "]");
                if (row.length > 0) {
                    //if still there save it
                    var name = row.find("input[name]").val();
                    if (name && name != "")
                        res.name = name;
                    newRes.push(res);
                } else {
                    //remove assignments
                    for (var j = 0; j < ge.tasks.length; j++) {
                        var task = ge.tasks[j];
                        var newAss = [];
                        for (var k = 0; k < task.assigs.length; k++) {
                            var ass = task.assigs[k];
                            if (ass.resourceId != res.id)
                                newAss.push(ass);
                        }
                        task.assigs = newAss;
                    }
                }
            }

            //loop on new rows
            var cnt = 0
            resourceEditor.find("[resId=new]").each(function () {
                cnt++;
                var row = $(this);
                var name = row.find("input[name]").val();
                if (name && name != "")
                    newRes.push(new Resource("tmp_" + new Date().getTime() + "_" + cnt, name));
            });

            ge.resources = newRes;

            closeBlackPopup();
            ge.redraw();
        });


        var ndo = createModalPopup(400, 500).append(resourceEditor);
    }
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



    function createNewResource(el) {
        var row = el.closest("tr[taskid]");
        var name = row.find("[name=resourceId_txt]").val();
        var url = contextPath + "/applications/teamwork/resource/resourceNew.jsp?CM=ADD&name=" + encodeURI(name);

        openBlackPopup(url, 700, 320, function (response) {
            //fillare lo smart combo
            if (response && response.resId && response.resName) {
                //fillare lo smart combo e chiudere l'editor
                row.find("[name=resourceId]").val(response.resId);
                row.find("[name=resourceId_txt]").val(response.resName).focus().blur();
            }

        });
    }
})