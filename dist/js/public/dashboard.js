$(document).ready(function () {
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