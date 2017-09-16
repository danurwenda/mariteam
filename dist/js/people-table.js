/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {
    renderPast = function (past, t, f, m) {
        if (t === 'sort') {
            return past;
        } else {
            if (past)
            {
                if (!past instanceof Date) {
                    past = new Date(past);
                }
                return moment(past).fromNow();
            }
            return 'Never';
        }
    };
    $('#users-table').DataTable({
        responsive: true,
        columns: [
            //name
            {},
            //is a user?
            {},
            //role
            {},
            //last access
            {render: renderPast},
            //edit
            {orderable: false, searchable: false}
        ]
    });
    var groups_table = $('#groups-table').DataTable({
        serverSide: true, processing: true,
        ajax: {url: base_url + 'people/groups_dt', type: 'POST'},
        responsive: true,
        columns: [
            //name
            {},
            //is publik?
            {searchable: false,render:function(p){return p==='1'?'Yes':'No'}},
            //number of users
            {searchable: false},
            //number of projects
            {searchable: false},
            //edit
            {
                orderable: false,
                searchable: false,
                render: function (id) {
                    return '<a href data-group="' + id + '" data-toggle="modal" data-target="#group-modal-form">Edit</a>';
                }
            }
        ]
    });
    // GROUP
    $('#group-modal-form .btn-primary').click(function (e) {
        var form = $('#group-modal-form form')
                //serialize the form, except those in hidden template
                ,
                h = form.find(":input").serialize()
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
                    $('#group-modal-form').modal('hide');
                    //reload table
                    groups_table.ajax.reload()
                });
    });

    $('#group-modal-form').on('shown.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
                ,
                group_id = button.data('group'),
                modal = $(this);
        if (group_id) {
            //edit
            var row = button.closest('tr');
            var group_name = row.children(':first').html();
            var pub = row.children('td:nth-child(2)').html();
            //show "Remove" button
            modal.find('.btn-danger').data('group_name', group_name).data('group_id', group_id).removeClass('hide')
            //populate data
            modal.find('[name=group_name]').val(group_name)
            modal.find('[name=is_public]').prop('checked',pub==='Yes')
        } else {
            //create
            //hide "Remove" button
            modal.find('.btn-danger').removeData('group_name').removeData('group_id').addClass('hide')
            //clear fields
            modal.find('[name=group_name]').val('')
            modal.find('[name=is_public]').prop('checked',false)
        }
    })
    
    $('#group-modal-form .btn-danger').on("click", function (e) {
        var name = $(this).data('group_name')
        var uid = $(this).data('group_id')
        bootbox.confirm({
            message: "Are you sure you want to remove the group " + name + "?",
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
                            "group_id": uid
                        },
                        url: base_url + 'people/delete_group',
                        type: 'POST',
                        success: function (result) {
                            //refresh table
                            groups_table.ajax.reload()
                            //close modal
                            $('#group-modal-form').modal('hide');
                        }
                    });
                }
            }
        })
    });
});