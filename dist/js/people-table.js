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
                if (!past instanceof Date) {
                    past = new Date(past);
                }
                return moment(past).fromNow();
            }
            return 'Never';
        }
    };
    $('#dataTables-example').DataTable({
        responsive: true,
        columns: [
            //name
            {},
            //status
            {},
            //role
            {},
            //member for
            {
                render: renderPast
            },
            //last access
            {render: renderPast},
            //edit
            {orderable: false, searchable: false}
        ]
    });
});