define(['backbone', '../models/suggestion'], function (Backbone, SuggestionModel) {
    var Suggestions = Backbone.Collection.extend({
        page: 0,
        limit: 21,
        model: SuggestionModel,
        popularFallback: true,
        fallbackUsed: null,

        url: function () {
            return '/user/suggestions/' + this.page * this.limit + '/' + this.limit + '/' + this.popularFallback;
        },

        parse: function (response) {
            this.fallbackUsed = response.fallbackUsed;
            return response.suggestions;
        }

    });

    return Suggestions;
});