define(['backbone'], function (Backbone) {
    var Suggestions = Backbone.Collection.extend({
        url: function () {
            return '/user/suggestions/' + this.page * this.limit + '/' + this.limit
        },
        page: 0,
        limit: 20
    });

    return Suggestions;
});