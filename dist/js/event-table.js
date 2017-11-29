/* 
 * To change this license header, choose License Headers in Event Properties.
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
                if (new Date() > new Date(f[3]) && f[2] !== "2") {
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
                    tooltip = 'finished event';
                } else if (f[2] === '4') {
                    //suspended
                    cls = 'progress-bar-warning';
                    tooltip = 'suspended event';
                } else if (f[2] === '1' && new Date() > new Date(f[3])) {
                    //masih aktif tapi udah telat
                    cls = 'progress-bar-danger';
                    tooltip = 'behind schedule';
                } else if (f[2] === '1' && f[6]) {
                    //masih aktif, event belum telat, tapi ada task yang telat
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
                    <div class="progress-bar progress-bar-striped ` + cls + `" role="progressbar" aria-valuenow="` + percent + `" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;width: ` + percent + `%">
                        ` + percent + `%
                    </div>
                </div>`;
            } else {
                return 'No task';
            }
        }
    };
    var events_table =
            $('#events-datatable').DataTable({
        responsive: true,
        stateSave: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + 'event/events_dt',
            type: 'POST',
            //data: function (d) {}
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
            {visible: false, searchable: false}, {visible: false, searchable: false}
        ]
    });
    events_table.on('order.dt search.dt draw.dt', function () {
        //biar kolom angka ga ikut ke sort
        var start = events_table.page.info().start;
        events_table.column(0, {order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = start + i + 1;
        });

    }).draw();
});