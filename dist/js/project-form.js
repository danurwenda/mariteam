$(document).ready(function () {
    // see https://github.com/jquery-validation/jquery-validation/issues/1875#issuecomment-272667183
    jQuery.validator.setDefaults({
        // This will ignore all hidden elements alongside `contenteditable` elements
        // that have no `name` attribute
        ignore: ":hidden, [contenteditable='true']:not([name])"
    });
    //=================== FORMATTING
    var quill = new Quill('#description', {
        theme: 'snow'
    });
    function ell(text, max) {
        let ellipsis = '…';
        var t = text.substr(0, max || 20);
        if (text.length > 20) t += ellipsis;
        return t;
    }
    if ($('#edit-slug-box').length > 0)
        $('#editable-post-name').html(
            ell($('#editable-post-name-full').html())
        )
    $('#edit-slug-box').show()
    var edit_slug;
    $('#edit-slug-buttons .edit-slug').click(function (e) {
        let composePermalink = function (link) {
            // remove text input and replace with supplied link
            $('#editable-post-name').html($('#editable-post-name-full').html());
            let href = $('#sample-permalink').text();
            $('#editable-post-name').html(ell(link));
            var a = $('<a/>')
                .html($('#sample-permalink').html())
                .attr('href', href);
            $('#sample-permalink').html(a);
            // put back edit_slug
            $('#edit-slug-buttons').empty().append(edit_slug);
            edit_slug = null;
        }
        this.blur();
        // remove the <a> tag and make it plaintext
        $('#sample-permalink').html($('#sample-permalink a').html())
        // replace #editable-post-name innerHtml with a text input with value = #editable-post-name-full innerHtml
        var permalinkInput = $('<input/>').addClass('input-sm').val($('#editable-post-name-full').html());
        $('#editable-post-name').html(permalinkInput)
        // add OK button, add Cancel button
        var okButton = $(this).clone().removeClass('edit-slug').html('OK').addClass('ok').click(function (e) {
            this.blur()
            let permalinkNew = permalinkInput.val();
            // we change permalink via ajax
            $.ajax({
                data: {
                    "permalink": permalinkNew,
                    "project_id": $('.main-panel').data('project')
                },
                url: base_url + 'project/change_permalink',
                type: 'POST',
                success: function (result) {
                    // update #editable-post-name-full
                    $('#editable-post-name-full').html(permalinkNew)
                    // revert to initial configuration of link and Edit button
                    composePermalink(permalinkNew)
                }
            });
        });
        var cancelButton = $(this).clone().removeClass('edit-slug').html('Cancel').addClass('cancel').click(function (e) {
            composePermalink($('#editable-post-name-full').html())
        });
        $('#edit-slug-buttons').append(okButton).append(cancelButton);
        // detach for later use (after OK/Cancel button click)
        edit_slug = $(this).detach();
    })
    
    $('input#task-end-date').datetimepicker({
        format: "MMM-YYYY", useCurrent: false
        //maxDate: new Date($('#task-end-date').data('max'))
    });
    
    $('#project_end_date').datetimepicker({
        format: "MMM-YYYY"
    });
    $('#project_assign_to').select2({
        theme: "bootstrap"
    })
    $('#groups').select2({
        theme: "bootstrap",
        ajax: {
            url: base_url + 'project/get_groups',
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
                            text: item.group_name,
                            id: item.group_id
                        }
                    })
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });
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
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });
    $('select#task-assign').select2({
        dropdownParent: $('#task-modal-form'),
        theme: "bootstrap"
    });
    $('.knob').knob()
    $('#project-due-date-remain').html('(' + moment($('#project-due-date').text(), 'D-MMMM-YYYY HH:mm').fromNow() + ')')


    // ====================== DATA TABLE =============================
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
            // 6 columns : id, name, target, status, order, weight
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
                        if (past) {
                            var dpast = moment(past, 'YYYY-MM-DD')
                            return '<span data-toggle="tooltip" title="' + dpast.format('DD MMM YYYY') + '">' + dpast.format('MMM YYYY') + '</span>';
                        }
                        return 'Never';
                    }
                }
            },
            //status
            {
                responsivePriority: 2,
                render: renderStatus
            },
            {},
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
    // since we use responsive datatables INSIDE a tabbed panel,
    // we need to trigger the calculating of table's width after the table is
    // displayed
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
                        if (past) {
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
    var events_table = $('#events-datatable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: {
            url: base_url + 'event/events_dt',
            type: 'POST',
            data: function (d) {
                d['project_id'] = $('.main-panel').data('project')
            }
        },
        columns: [{},
        // name link
        {
            render: function (n, t, f, m) {
                if (t === 'sort') {
                    return n;
                } else {
                    //render link using id
                    return '<a href="' + base_url + 'event/edit/' + f[0] + '">' + n + '</a>'
                }
            }
        },
        // PIC
        {},
        // start time
        {
            render: function (d, t, f, m) {
                return moment(d).format("D MMMM YYYY HH:mm")
            }
        },
        // location
        {},
        { visible: false, searchable: false }, { visible: false, searchable: false }
        ]
    });
    events_table.on('order.dt search.dt draw.dt', function () {
        //biar kolom angka ga ikut ke sort
        var start = events_table.page.info().start;
        events_table.column(0, { order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = start + i + 1;
        });

    }).draw();
    // trigger the responsive data table to adjust its appearance when the tab is shown
    $('a[href="#task"]').on('shown.bs.tab', function (e) {
        tasks_table.responsive.recalc();
    });
    $('a[href="#documents"]').on('shown.bs.tab', function (e) {
        docs_table.responsive.recalc();
    });
    $('a[href="#events"]').on('shown.bs.tab', function (e) {
        events_table.responsive.recalc();
    });
    // ============== MODALS TO LOAD ENTITY ==========================
    // TOPIC
    $('#topic-modal-form .btn-primary').click(function (e) {
        var form = $('#topic-modal-form form')
            //serialize the form, except those in hidden template
            ,
            h = form.find(":input:not(.template :input)").serialize()
            // process the form
            ,
            action = form.attr('action');
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
            var deldoc = $('<span class="deldoc deldocbtn">').attr('data-doc_id', doc.document_id).attr('data-source', 'tasks');
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
        cmtel.append($('<span/>').addClass('chat-img').attr('data-letters', initial)).append($('<div/>').addClass('chat-body clearfix').append($('<div/>').addClass('chat-header').append($('<small/>').addClass('text-muted').append($('<i class="fa fa-clock-o fa-fw"></i>')).append($('<span/>').addClass('comment-time').html(moment(cmt.time).fromNow())))).append($('<p/>').html(cmt.content)))
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
            modal = $(this),
            form = modal.find('.modal-body').hasClass('modal-form');
        if (task_id) {
            //prepare form to be submitted for editing entry
            //populate form after ajax load
            $.getJSON(base_url + 'project/get_task/' + task_id, function (task) {
                if (form) {
                    modal.find('#task-name').val(task.task_name)
                    modal.find('[name=task_id]').val(task.task_id)
                    modal.find('#task-desc').val(task.description)
                    // show status
                    modal.find('[name=task_status]').closest('.form-group').removeClass('hide')
                    modal.find('[name=task_status]').val([task.status])
                    modal.find('#task-assign').val(task.assigned_to).trigger('change.select2');
                    modal.find('#task-end-date').data("DateTimePicker").date(new Date(task.end_date));
                    modal.find('#task-weight').val(task.weight).trigger('change')
                    //show "Remove" button
                    modal.find('.btn-danger').data('task_name', task.task_name).data('task_id', task.task_id).removeClass('hide')
                } else {
                    //read only
                    modal.find('#task-name').html(task.task_name)
                    var due = moment(task.end_date);
                    modal.find('#task-end-date').html(due.format('D MMM YYYY'))
                    modal.find('#task-desc').html(task.description)
                    modal.find('#task-due-date-remain').html(due.fromNow())
                    modal.find('#task-weight').html(task.weight)
                    modal.find('#task-status').html(renderStatus(task.status))
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
            modal.find('.btn-danger').removeData('task_name').removeData('task_id').addClass('hide')
            // hide status
            modal.find('[name=task_status]').closest('.form-group').addClass('hide')
            // hide chat
            $('#task-chat-panel').addClass('hide');
            // hide upload
            $('#task-uploaded').addClass('hide');
            $('#task-upload').removeClass('col-md-6');
            $('.trigger-upload').addClass('hide')
            //reset form
            $('#task-modal-form #task-form')[0].reset();
            $('#task-modal-form #task-form').find('#task-weight').val(3).trigger('change')
        }
    });
    var project_validator = $('#project_form').validate({
        rules: {
            field: {
                required: true,
                number: true
            }
        },
        submitHandler: function (form) {
            //add description as hidden
            $('<input />').attr('type', 'hidden')
                .attr('name', "description")
                .attr('value', quill.root.innerHTML)
                .appendTo('#project_form');
            form.submit()
        }
    });

    var task_validator = $('#task-modal-form #task-form').validate();
    $('#task-modal-form .btn-subm').click(function (e) {
        var form = $('#task-modal-form #task-form')
            //serialize the form, except those in hidden template
            ,
            action = form.attr('action'),
            h = form.find(":input:not(.template :input)").serialize();
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
        var btn = $(this),
            select = btn.parent().parent().find('select'),
            person_form = $('.new-person-form').clone(true).removeClass('hide'),
            is_user = person_form.find("[name=is_user]"),
            person_validator = person_form.validate({
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
            });
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
                n.close()
                if (response.ok) {
                    if (response.project) {
                        new Noty({
                            timeout: 1000,
                            layout: 'center',
                            type: 'success',
                            theme: 'relax',
                            text: 'Timeline saved',
                        }).show();
                    } else {
                        ge.reset();
                    }
                } else {
                    // TODO : error handling
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