$(document).ready(function () {
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
    var userCB = $('[name=user]');
    //toggle user field
    enableUserFields = function (enable) {
        $('.user-field input:not([name=user])').prop("disabled", !enable);
    }
    userCB.change(function () {
        enableUserFields($(this).is(':checked'))
    });
    enableUserFields(userCB.is(':checked'))
    //form validation
    var validator = $('form').validate({
        errorPlacement: function (error, element) {
            if (element.is(":radio"))
                error.appendTo(element.parent().parent().parent());
            else
                error.appendTo(element.parent())
        },
        submitHandler: function (form) {
            $(form).ajaxSubmit(function (result) {
                result = JSON.parse(result);
                if (result.success) {
                    // Redirect to table view
                    window.location.href = base_url + "people"
                    new Noty({
                        timeout: 1000,
                        layout: 'center',
                        type: 'success',
                        theme: 'relax',
                        text: 'Saved',
                    }).show();
                }
            })
        },
        rules: {
            email: {
                required: {
                    depends: function (e) {
                        return userCB.is(':checked')
                    }
                },
                email: {
                    depends: function (element) {
                        return userCB.is(":checked");
                    }
                },
                remote: {
                    param: {
                        url: base_url + 'people/check_email',
                        type: "post",
                        data: {
                            person_id: function () {
                                return $("[name=person_id]").val();
                            }
                        }
                    },
                    depends: function (element) {
                        return userCB.is(":checked");
                    }
                }
            },
            passconf: {
                equalTo: {
                    param: '#password',
                    depends: function (element) {
                        return userCB.is(":checked");
                    }
                }
            },
            role: {
                required: {
                    depends: function (e) {
                        return userCB.is(':checked')
                    }
                }
            }
        }
    });

    //delete people button
    $('.btn-danger').on("click", function (e) {
        var name = $(this).data('person_name')
        var uid = $(this).data('person_id')
        bootbox.confirm({
            message: "Are you sure you want to remove the account " + name + "?",
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
                        data: {"person_id": uid},
                        dataType: 'json',
                        url: base_url + 'people/delete',
                        type: 'POST',
                        success: function (result) {
                            if (result.success) {
                                // Redirect to table view
                                window.location.href = base_url + "/people"
                            } else {
                                alert('Remove failed : has active reference')
                            }
                        }
                    });
                }
            }
        })
    });
})