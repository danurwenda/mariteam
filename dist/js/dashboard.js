$(document).ready(function () {
    function drawChart(raw) {
        var data = google.visualization.arrayToDataTable(raw);

        var options = {
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
})