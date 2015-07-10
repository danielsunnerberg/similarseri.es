var config = {
    shim : {
        'bootstrap' : { 'deps' : ['jquery'] }
    },
    paths: {
        'jquery' : ['//code.jquery.com/jquery-2.1.4.min', '/assets/vendor/jquery/dist/jquery'],
        'bootstrap' : ['/maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min', '/assets/vendor/bootstrap/dist/js/bootstrap.min'],
        'typeahead': '/assets/vendor/typeahead.js/dist/typeahead.jquery',
        'bloodhound': '/assets/vendor/typeahead.js/dist/bloodhound.min',
        'handlebars': '/assets/vendor/handlebars/handlebars.amd.min'
    }
};
requirejs.config(config);