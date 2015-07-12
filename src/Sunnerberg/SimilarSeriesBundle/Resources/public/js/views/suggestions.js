define(
    ['backbone', 'underscore', 'collections/suggestions', 'text!templates/suggestions.html'],
    function (Backbone, _, SuggestionsCollection, SuggestionsTemplate) {
        var SuggestionsView = Backbone.View.extend({
            el: '#suggestions',
            initialize: function () {
                this.isLoading = false;
                this.suggestionsCollection = new SuggestionsCollection();
            },
            render: function() {
                this.loadResults();
            },
            loadResults: function () {
                var that = this;
                this.isLoading = true;
                this.suggestionsCollection.fetch({
                    success: function (response) {
                        var template = _.template(SuggestionsTemplate);
                        template = template({
                            suggestions: response.models[0].get('suggestions'),
                            posterBaseUrl: response.models[0].get('posterBaseUrl'),
                            _: _
                        });
                        $(that.el).append(template);
                        that.isLoading = false;
                        console.log(response);
                    },
                    error: function (model, response, options) {
                        // @todo
                    }
                })
            }

        });

        return SuggestionsView;
    });