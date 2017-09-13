$(document).ready(function () {
    function drawChart(raw) {
        var data = google.visualization.arrayToDataTable(raw);

        var options = {
            colors: ["#5cb85c", "#5bc0de", "#d9534f", "#f9c154", "#1aadce", "#492970", "#f28f43", "#77a1e5", "#c42525", "#a6c96a"],
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
        events: base_url + 'publik/calendar',
        eventClick: function (e) {
            window.location = base_url+'event/edit/'+e.id;
        }
    });
})