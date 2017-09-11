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
                    form.find('.event-projects').append(`<li><a href="`+base_url+`publik/project/${prid}">${projects[prid]}</a></li>`);
                }
            }
            //popup event detail
            form.modal()
        }
    });
})