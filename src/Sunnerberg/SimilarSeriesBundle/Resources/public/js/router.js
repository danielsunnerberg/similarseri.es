define(['backbone', 'views/suggestions', 'views/tvShowSearch'], function (Backbone, SuggestionsView, TvShowSearchView) {
    var Router = Backbone.Router.extend({
        routes: {
            '*actions': 'defaultRoute' // @todo
        }
    });

    var initialize = function () {

        var router = new Router;

        router.on('route:defaultRoute', function (actions) {
            console.log("No matching route found. Using default.", actions);
            new SuggestionsView().render();
            new TvShowSearchView().render();
        });

        Backbone.history.start({ pushState:true });
    };

    return {
        initialize: initialize
    }
});