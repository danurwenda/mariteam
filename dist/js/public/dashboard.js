$(document).ready(function () {
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawCharts)

    $(window).resize(function () {
        drawCharts();
    });
    var sraw = [
        ['Status', '#Projects']
    ], optionss = {
        element: 'piechart',
        colors: ["#5cb85c", "#5bc0de", "#d9534f", "#f9c154", "#1aadce", "#492970", "#f28f43", "#77a1e5", "#c42525", "#a6c96a"],
        is3D: true,
        t: 'pie',
        title: 'Projects by Status'
    };
    var draw = [
        ['Deputy', '#Projects']
    ], optionsd = {
        element: 'piechartd',
        //colors: ["#5cb85c", "#5bc0de", "#d9534f", "#f9c154", "#1aadce", "#492970", "#f28f43", "#77a1e5", "#c42525", "#a6c96a"],
        t: 'bar',legend: { position: 'none' },
        title: 'Projects by Deputy'
    };

    function doDraw(arr, opts) {
        var data = google.visualization.arrayToDataTable(arr);
        let chart;
        if (opts.t == 'pie') {
            chart = new google.visualization.PieChart(document.getElementById(opts.element));
        } else if (opts.t == 'bar') {
            chart = new google.visualization.ColumnChart(document.getElementById(opts.element));
        }
        chart.draw(data, opts);
    }

    function drawCharts() {
        //load statistic only once
        if (draw.length === 1) {
            //load json
            $.getJSON(base_url + 'publik/get_project_chart_data_by_dep', function (data) {

                data.forEach(function (stat) {
                    draw.push([stat.group_name, Number(stat.total)])
                })

                doDraw(draw, optionsd);
            })
        } else {
            //else, draw
            doDraw(draw, optionsd);
        }
        //load statistic only once
        if (sraw.length === 1) {
            //load json
            $.getJSON(base_url + 'publik/get_project_chart_data', function (data) {

                data.forEach(function (stat) {
                    sraw.push([stat.name.substring(7), Number(stat.total)])
                })

                doDraw(sraw, optionss);
            })
        } else {
            //else, draw
            doDraw(sraw, optionss);
        }
    }
    //init calendar

    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listWeek'
        },
        navLinks: true, // can click day/week names to navigate views
        eventLimit: true, // allow "more" link when too many events
        events: base_url + 'publik/calendar',
        eventClick: function (e) {
            var form = $('#event-modal-form'), projects = e.projects;
            form.find('.event-name').html(e.title)
            form.find('.event-description').html(e.description)
            form.find('.event-pic').html(e.pic)
            form.find('.event-location').html(e.location)
            form.find('.event-start').html(moment(e.start).format("D-MMMM-YYYY HH:mm"))
            form.find('.event-end').html(moment(e.end).format("D-MMMM-YYYY HH:mm"))
            if (!projects) {
                form.find('.projects').hide();
            } else {
                form.find('.projects').show();
                form.find('.event-projects').empty()
                for (var prid in projects) {
                    form.find('.event-projects').append(`<li><a href="` + base_url + `logged/project/${prid}">${projects[prid]}</a></li>`);
                }
            }
            //popup event detail
            form.modal()
        }
    });
})