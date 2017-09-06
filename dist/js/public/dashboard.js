$(document).ready(function () {
    function drawChart(raw) {
        var data = google.visualization.arrayToDataTable(raw);

        var options = {
            colors: ["#2f7ed8", "#0d233a", "#8bbc21", "#910000", "#1aadce", "#492970", "#f28f43", "#77a1e5", "#c42525", "#a6c96a"],
            is3D: true,
            title: 'Project by Status'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
    }
    //load statistic
    $.getJSON(base_url + 'project/get_stats', function (data) {
        var sraw = [
            ['Status', '#Projects']
        ];

        data.forEach(function (stat) {
            sraw.push([stat.name, Number(stat.total)])
        })
        //use it to draw chart
        drawChart(sraw);
    })
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