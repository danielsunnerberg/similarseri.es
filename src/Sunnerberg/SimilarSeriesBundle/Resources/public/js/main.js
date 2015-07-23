var config = {
    urlArgs: "bust=" + (new Date()).getTime(),
    shim : {
        'bootstrap' : { 'deps' : ['jquery'] },
        'ladda' : { 'deps' : ['spin'] },
        'arrive' : { 'deps' : ['jquery'] },
        'bootstrap-material-design': { 'deps': ['jquery'] },
        'ripples': { 'deps': ['jquery'] }
    },
    paths: {
        'jquery' : '/assets/vendor/jquery/dist/jquery',
        'bootstrap' : '/assets/vendor/bootstrap/dist/js/bootstrap.min',
        'typeahead': '/assets/vendor/typeahead.js/dist/typeahead.jquery',
        'bloodhound': '/assets/vendor/typeahead.js/dist/bloodhound.min',
        'handlebars': '/assets/vendor/handlebars/handlebars.amd.min',
        'underscore': '/assets/vendor/underscore/underscore-min',
        'backbone': '/assets/vendor/backbone/backbone-min',
        'text': '/assets/vendor/requirejs-text/text',
        'spin': '/assets/vendor/ladda-bootstrap/dist/spin.min',
        'ladda': '/assets/vendor/ladda-bootstrap/dist/ladda.min',
        'arrive': '/assets/vendor/arrive/minified/arrive.min',
        'bootstrap-material-design': '/assets/vendor/bootstrap-material-design/dist/js/material.min',
        'ripples': '/assets/vendor/bootstrap-material-design/dist/js/ripples.min'
    }
};
requirejs.config(config);

require(['router'], function (Router) {
    Router.initialize();
});
