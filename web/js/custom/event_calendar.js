$(document).ready(function () {

    $('#calendar').clndr({
        template:
        '<div class="grid-calendar">' +
        '<div class="row calendar-week-header">' +
        '<% _.each(daysOfTheWeek, function (day) { %>' +
        '<div class="header-day col-xs-1 grid-cell">' +
        '<div><div><span><%= day %></span></div></div></div>' +
        '<% }); %>' +
        '</div>' +
        '</div>'

        // '<div class="clndr-controls">\n' +
        // '    <div class="clndr-previous-button">&lsaquo;</div>\n' +
        // '    <div class="month"><%= month %></div>\n' +
        // '    <div class="clndr-next-button">&rsaquo;</div>\n' +
        // '</div>\n' +
        // '<div class="clndr-grid">\n' +
        // '    <div class="days-of-the-week">\n' +
        // '    <% _.each(daysOfTheWeek, function (day) { %>\n' +
        // '        <div class="header-day"><%= day %></div>\n' +
        // '    <% }); %>\n' +
        // '        <div class="days">\n' +
        // '        <% _.each(days, function (day) { %>\n' +
        // '            <div class="<%= day.classes %>"><%= day.day %></div>\n' +
        // '        <% }); %>\n' +
        // '        </div>\n' +
        // '    </div>\n' +
        // '</div>'
    });

});