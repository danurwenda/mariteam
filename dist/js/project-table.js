/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {
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
    renderPast = function (past, t, f, m) {
        if (t === 'sort') {
            return past;
        } else {
            if (past)
            {
                var cls = '';
                if (f[2] === "3") {
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
                if (f[2] === '2') {
                    //udah beres
                    cls = 'progress-bar-info';
                    tooltip = 'finished project';
                } else if (f[2] === '4') {
                    //suspended
                    cls = 'progress-bar-warning';
                    tooltip = 'suspended project';
                } else if (f[2] === '3') {
                    //masih aktif tapi udah telat
                    cls = 'progress-bar-danger';
                    tooltip = 'behind schedule';
                } else if (f[2] === '1' && f[6]) {
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

    $('#projects-datatable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + 'project/projects_dt',
            type: 'POST',
            //data: function (d) {}
        },
        columns: [
            // name link
            {
                render: function (n, t, f, m) {
                    if (t === 'sort') {
                        return n;
                    } else {
                        //render link using id
                        return '<a href="' + base_url + 'project/edit/' + f[5] + '">' + n + '</a>'
                    }
                }
            },
            // PIC
            {},
            // project status
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
                            return '<span class="label label-danger">Failed</span>'
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
            {render: renderPast},
            // progress
            {render: renderProgress}
        ]
    });
});