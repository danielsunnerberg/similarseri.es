define(['backbone'], function (Backbone) {
    var Suggestion = Backbone.Model.extend({

        initialize: function (options) {
            this.id = options.show.tmdbId;
        },

        url: function () {
            return '/user/shows/' + this.id;
        }
    });

    return Suggestion;
});
