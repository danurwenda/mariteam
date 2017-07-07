$('.btn-danger').on("click", function (e) {
    var name = $(this).data('user_name')
    var uid = $(this).data('user_id')
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
                    data: {"user_id": uid},
                    url: base_url+'/people/delete',
                    type: 'POST',
                    success: function (result) {
                        // Redirect to table view
                        window.location.href = base_url+"/people"
                    }
                });
            }
        }
    })
});