$(document).ready(function () {
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawPie)

    $(window).resize(function () {
        drawPie();
    });
    var sraw = [
        ['Status', '#Projects']
    ], options = {
        colors: ["#2f7ed8", "#0d233a", "#8bbc21", "#910000", "#1aadce", "#492970", "#f28f43", "#77a1e5", "#c42525", "#a6c96a"],
        is3D: true,
        title: 'Project by Status'
    };

    function doDraw(arr) {
        var data = google.visualization.arrayToDataTable(arr);

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
    }

    function drawPie() {
        //load statistic only once
        if (sraw.length === 1) {
            //load json
            $.getJSON(base_url + 'publik/get_project_chart_data', function (data) {

                data.forEach(function (stat) {
                    sraw.push([stat.name, Number(stat.total)])
                })

                doDraw(sraw);
            })
        } else {
            //else, draw
            doDraw(sraw);
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
        events: [

        ]

    });
})