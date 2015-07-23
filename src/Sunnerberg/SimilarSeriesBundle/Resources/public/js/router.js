define(['backbone', 'underscore', 'views/suggestions', 'views/tvShowSearch', 'common'], function (Backbone, _, SuggestionsView, TvShowSearchView, Common) {
    var Router = Backbone.Router.extend({
        routes: {
            '*actions': 'defaultRoute' // @todo
        }
    });

    var initialize = function () {
        var router = new Router;

        router.on('route:defaultRoute', function (actions) {
            console.log("No matching route found. Using default.", actions);
            var events = _.extend({}, Backbone.Events);
            new SuggestionsView({externalEvents: events}).render();
            new TvShowSearchView({events: events}).render();

            Common.initialize();
        });

        Backbone.history.start({ pushState:true });
    };

    return {
        initialize: initialize
    }
});