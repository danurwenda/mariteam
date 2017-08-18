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
                if (new Date() > new Date(f[3]) && f[2] !== 'Done') {
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
                var cls = '';
                if (f[2] === 'Done') {
                    cls = 'progress-bar-success';
                } else if (new Date() > new Date(f[3])) {
                    cls = 'progress-bar-danger';
                } else if (f[2] === 'On Hold') {
                    cls = 'progress-bar-warning';
                }
                return `<div class="progress">
                    <div class="progress-bar progress-bar-striped ` + cls + `" role="progressbar" aria-valuenow="` + percent + `" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;width: ` + percent + `%">
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
            {},
            // due date
            {render: renderPast},
            // progress
            {render: renderProgress}
        ]
    });
});