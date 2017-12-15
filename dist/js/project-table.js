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
                var cls = '';
                if (f['project_status'] === "3") {
                    cls = 'alert-danger';
                }
                var dpast = moment(new Date(past))
                return '<span class="' + cls + '" data-toggle="tooltip" title="' + dpast.format('DD MMM YYYY, hh:mm') + '">' + dpast.fromNow() + '</span>';
            }
            return 'Never';
        }
    };

    renderProgress = function (percent, t, f, m) {
        if (t === 'sort') {
            return percent;
        } else {
            if (percent > -1) {
                var cls = '', tooltip = '';
                var f2 = f['project_status'];
                if (f2 === '2') {
                    //udah beres
                    cls = 'progress-bar-info';
                    tooltip = 'finished project';
                } else if (f2 === '4') {
                    //suspended
                    cls = 'progress-bar-warning';
                    tooltip = 'suspended project';
                } else if (f2 === '3') {
                    //masih aktif tapi udah telat
                    cls = 'progress-bar-danger';
                    tooltip = 'behind schedule';
                } else if (f2 === '1' && f['delay']) {
                    //masih aktif, project belum telat, tapi ada task yang telat
                    tooltip = 'overdue task';
                    cls = 'progress-bar-late';
                } else {
                    //active dan belum telat
                    tooltip = 'in progress';
                    cls = 'progress-bar-success'
                }
                percent = percent * 100;
                percent = percent.toFixed(2);
                return `<div class="progress" data-toggle="tooltip" title="` + tooltip + `">
                    <div class="progress-bar progress-bar-striped ` + cls + `" role="progressbar" aria-valuenow="` + percent + `" aria-valuemin="0" aria-valuemax="100" style="font-weight:bold;color:black;padding-left:5px;min-width: 0.2em;width: ` + percent + `%">
                        ` + percent + `%
                    </div>
                </div>`;
            } else {
                return 'No task';
            }
        }
    };

    var projects_dt = $('#projects-datatable').DataTable({
        stateSave:true,
        responsive: true,
        processing: true,
        serverSide: true,
        stateSaveParams: function (settings, data) {
            data['groups'] = $('#groups').val();
        },
        stateLoaded: function (settings, loadeddata) {
            // Fetch the preselected item, and add to the control
            var filterSelect = $('#groups');
            $.ajax({
                type: 'GET',
                url: base_url + 'publik/get_groups_elmt',
                data: {
                    'groups[]': loadeddata.groups
                }
            }).then(function (data) {
                data = JSON.parse(data);
                for (var i = 0; i < data.length; i++) {
                    let d = data[i];
                    // create the option and append to Select2
                    filterSelect.append(new Option(d.group_name, d.group_id, true, true));
                }
                filterSelect.trigger('change');

                // manually trigger the `select2:select` event
                filterSelect.trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
                });
            });
        },
        ajax: {
            url: base_url + 'project/projects_dt',
            type: 'POST',
            data: function (d) {
                d['groups[]'] = $('#groups').val()
            }
        },
        columns: [
            // name link
            {
                data: 'project_name',
                render: function (n, t, f, m) {
                    if (t === 'sort') {
                        return n;
                    } else {
                        //render link using id
                        return '<a href="' + base_url + 'project/edit/' + f['project_id'] + '">' + n + '</a>'
                    }
                }
            },
            // PIC
            {
                data: 'person_name'
            },
            // project status
            {
                data: 'project_status',
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
            // due date
            {
                data: 'end_date',
                render: renderPast
            },
            // progress
            {
                data: 'progress',
                render: renderProgress
            }
        ]
    });
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

    $('#groups').change(function () {
        //update table
        projects_dt.ajax.reload()
    })
});