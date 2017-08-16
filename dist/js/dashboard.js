$(document).ready(function () {
    function drawChart(raw) {
        var data = google.visualization.arrayToDataTable(raw);

        var options = {
            colors:["#2f7ed8","#0d233a","#8bbc21","#910000","#1aadce","#492970","#f28f43","#77a1e5","#c42525","#a6c96a"],
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
    var calendar = $('#calendar').calendar({
            events_source: [
                {
                    "id": 1,
                    "title": "Event 1",
                    "url": "http://example.com",
                    "class": "event-important",
                    "start": moment().valueOf(), // Now in milliseconds
                    "end": moment().add(2, 'days').valueOf() // Now + 2 days in milliseconds
                },
                {
                    "id": 2,
                    "title": "Event 2",
                    "url": "http://example.com",
                    "class": "event-default",
                    "start": moment().add(1, 'days').valueOf(), // Now + 1 day in milliseconds
                    "end": moment().add(3, 'days').valueOf() // Now + 3 days in milliseconds
                },
            ],
            tmpl_path: base_url+"/vendor/bootstrap-calendar/tmpls/"
        });
})