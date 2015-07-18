define(['backbone', '../models/suggestion'], function (Backbone, SuggestionModel) {
    var Suggestions = Backbone.Collection.extend({
        url: function () {
            return '/user/suggestions/' + this.page * this.limit + '/' + this.limit
        },
        parse: function (response) {
            return response.suggestions;
        },
        page: 0,
        limit: 20,
        model: SuggestionModel
    });

    return Suggestions;
});