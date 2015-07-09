var config = {
    shim : {
        'bootstrap' : { 'deps' : ['jquery'] }
    },
    paths: {
        'jquery' : ['//code.jquery.com/jquery-2.1.4.min', '/assets/vendor/jquery/dist/jquery'],
        'bootstrap' : ['/maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min', '/assets/vendor/bootstrap/dist/js/bootstrap.min'],
        'bootstrap3-typeahead': '/assets/vendor/bootstrap3-typeahead/bootstrap3-typeahead.min'
    }
};
requirejs.config(config);
