require(['handlebars'], function (Handlebars) {
    Handlebars.registerHelper('truncate', function (string, maxLength) {
        if (string !== null && string.length > maxLength) {
            return string.substr(0, maxLength) + "…";
        }
        return string;
    });
});