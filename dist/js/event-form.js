$(document).ready(function () {

    // ====================== FORM VALIDATION =============================
    $('#event_form').validate()
    // ====================== DATA TABLE =============================
    $('.event-docs-table').each(function (i) {

        var table = $(this)
        table.DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: base_url + 'event/docs_dt',
                type: 'POST',
                data: function (d) {
                    d['event_id'] = $('.main-panel').data('event')
                    d['type'] = table.data('type')
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
                            return '<span class="deldoc btn btn-danger" data-source="events" data-doc_id=' + id + '> Remove</span>'
                        else
                            return ''
                    }
                }
            ]
        });
    })
    $('.event-docs-table').on("click", ".deldoc", function (e) {
        var el = $(this),
                uid = $(this).data('doc_id');
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
                        url: base_url + 'event/delete_doc',
                        type: 'POST',
                        success: function (result) {
                            //reload table
                            el.closest('.event-docs-table').DataTable().ajax.reload()
                        }
                    });
                }
            }
        })
    });

    //=================== FORMATTING
    $('#linked-projects').select2({
        ajax: {
            url: base_url + "publik/projects_s2",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: function (repo) {
            if (repo.loading)
                return repo.text;

            return repo.project_name;
        },
        templateSelection: function (repo) {
            return repo.project_name || repo.text;
        }
    });
    $('#event_start_time').datetimepicker({
        format: "DD-MMMM-YYYY HH:mm"
    });
    $('#event_end_time').datetimepicker({
        format: "DD-MMMM-YYYY HH:mm",
        useCurrent: false
    });
    //link those datetimepickers
    $("#event_start_time").on("dp.change", function (e) {
        $('#event_end_time').data("DateTimePicker").minDate(e.date);
    });
    $("#event_end_time").on("dp.change", function (e) {
        $('#event_start_time').data("DateTimePicker").maxDate(e.date);
    });

    $('#event_assign_to').select2({
        theme: "bootstrap"
    })

    $('#event-due-date-remain').html('(' + moment($('#event-due-date').text(), 'D-MMMM-YYYY HH:mm').fromNow() + ')')

    // ============== MODALS TO LOAD ENTITY ==========================
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
            url: base_url + 'event/add_task_comment',
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
    // remove event
    $('#event_form .btn-danger').on("click", function (e) {
        var name = $(this).data('event_name')
        var uid = $(this).data('event_id')
        bootbox.confirm({
            message: "Are you sure you want to remove the event " + name + "?",
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
                            "event_id": uid
                        },
                        url: base_url + 'event/delete',
                        type: 'POST',
                        success: function (result) {
                            // Redirect to table view
                            window.location.href = base_url + "event"
                        }
                    });
                }
            }
        })
    });
    //=======================FILE UPLOAD
    $('.tab-pane').each(function (i) {
        var tab = $(this), uploader = tab.find('.uploaderz'),
                table = tab.find('.event-docs-table');

        uploader.fineUploader({
            template: 'qq-template-manual-trigger',
            request: {
                endpoint: base_url + 'event/uploads/events.' + table.data('type') + '/' + uploader.data('event')
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
                    table.DataTable().ajax.reload()
                    tab.find('.qq-upload-list').empty();
                }
            }
        });

        tab.find('.trigger-upload').click(function () {
            uploader.fineUploader('uploadStoredFiles');
        });
    })
})