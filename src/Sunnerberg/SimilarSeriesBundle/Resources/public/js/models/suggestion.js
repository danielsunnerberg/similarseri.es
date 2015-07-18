define(['backbone'], function (Backbone) {
    var Suggestion = Backbone.Model.extend({
        url: function () {
            return '/user/suggestions/' + this.id
        }
    });

    return Suggestion;
});