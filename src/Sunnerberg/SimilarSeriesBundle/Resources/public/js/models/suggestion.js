define(['backbone'], function (Backbone) {
    var Suggestion = Backbone.Model.extend({

        initialize: function (options) {
            if (options && options.show) {
                this.id = options.show.tmdbId;
            }
        },

        url: function () {
            return '/user/shows/' + this.id;
        },

        ignore: function (attributes, options) {
            options = _.defaults((options || {}), {url: "/user/ignored_shows/" + this.id});
            return Backbone.Model.prototype.save.call(this, attributes, options);
        }
    });

    return Suggestion;
});
