$(document).ready(function () {

    renderPast = function (past, t, f, m) {
        if (t === 'sort') {
            return past;
        } else {
            if (past) {
                var cls = '';
                if (f[2] === '3') {
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

                //active dan belum telat
                tooltip = 'in progress';
                cls = 'progress-bar-success'

                percent = percent * 100;
                percent = percent.toFixed(2);
                return `<div class="progress" data-toggle="tooltip" title="` + tooltip + `">
                    <div class="progress-bar ` + cls + `" role="progressbar" aria-valuenow="` + percent + `" aria-valuemin="0" aria-valuemax="100" style="font-weight:bold;color:black;padding-left:5px;min-width: 0.2em;width: ` + percent + `%">
                        ` + percent + `%
                    </div>
                </div>`;
            } else {
                return 'No task';
            }
        }
    };

    renderStatus = function (d, t, f, m) {

        switch (d) {
            case '1':
                return '<span class="label label-success">Active</span>';
                break;
            case '2':
                return '<span class="label label-info">Done</span>';
                break;
            case '3':
                return '<span class="label label-danger">Overdue</span>';
                break;
            case '4':
                return '<span class="label label-warning">Suspended</span>';
                break;
            default:
                return 'Undefined'
        }
    }

    renderSdg = function (n, t, f, m) {
        if (t === 'sort') {
            return n;
        } else {
            var projectList = '';
            f[6].forEach(e => {
                var id = e.group_id;
                projectList += '<li class="sdg-' + (id < 10 ? '0' + id : id) + '"></li>';
            });
            //render link using id
            return '<p><a href="' + parent_url + 'project/' + f[4] + '">' + n + '</a>'
                +
                //render icon2 sdg
                (projectList === '' ? '' :
                    `<ul class="sdglist">` + projectList + `</ul>`)
        }
    }



    var projects_dt = $('#projects-datatable').DataTable({
        responsive: true,
        dom: '<"top">rt<"bottom"p><"clear">',
        paging: false,
        serverSide: true,
        stateSave: true,
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
            url: base_url + 'publik/projects_dt',
            type: 'POST',
            data: function (d) {
                d['groups[]'] = $('#groups').val()
            }
        },
        columns: [
            {},
            // name link
            {
                render: renderSdg
            },
            // project status
            //{render: renderStatus},
            // due date
            { render: renderPast },
            // progress
            { render: renderProgress }
        ]
    });
    projects_dt.on('order.dt search.dt draw.dt', function () {
        //biar kolom angka ga ikut ke sort
        var start = projects_dt.page.info().start;
        projects_dt.column(0, { order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = start + i + 1;
        });

    }).draw();
    $('#groups').select2({
        theme: "bootstrap",
        ajax: {
            url: base_url + 'publik/get_groups',
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