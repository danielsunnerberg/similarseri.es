define(['backbone'], function (Backbone) {
    var Suggestions = Backbone.Collection.extend({
        url: function () {
            return '/user/suggestions/' + this.page
        },
        page: 1
    });

    return Suggestions;
});