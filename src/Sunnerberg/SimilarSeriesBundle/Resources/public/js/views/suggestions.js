define(
    ['jquery', 'backbone', 'underscore', 'collections/suggestions', 'text!templates/suggestions.html'],
    function ($, Backbone, _, SuggestionsCollection, SuggestionsTemplate) {
        var SuggestionsView = Backbone.View.extend({
            el: '#suggestions',

            initialize: function () {
                this.isLoading = false;
                this.suggestionsCollection = new SuggestionsCollection();
                var that = this;
                $(window).bind('scroll', function() {
                    that.checkScroll();
                });
            },

            remove: function () {
                $(window).off('scroll', this.checkScroll);
            },

            render: function () {
                this.loadResults();
            },

            loadResults: function () {
                var that = this;
                this.isLoading = true;
                this.suggestionsCollection.fetch({
                    success: function (response) {
                        that.isLoading = false;
                        var suggestions = response.models[0].get('suggestions');
                        var posterBaseUrl = response.models[0].get('posterBaseUrl');

                        if (suggestions.length == 0) {
                            return;
                        }

                        $('#user-suggestions-title').text('Suggestions generated for you');
                        var template = _.template(SuggestionsTemplate);
                        template = template({
                            suggestions: suggestions,
                            posterBaseUrl: posterBaseUrl,
                            _: _
                        });
                        $(that.el).append(template);

                    },
                    error: function (model, response, options) {
                        that.isLoading = false;
                        $(that.el).empty();
                        $('#user-suggestions-title').text('An error occured. Please try again later');
                    }
                })
            },

            checkScroll: function () {
                var triggerPoint = 200;
                var element = $(window);
                if (! this.isLoading && element.scrollTop() + element.height() + triggerPoint > document.body.scrollHeight) {
                    this.suggestionsCollection.page += 1;
                    this.loadResults();
                }
            }

        });

        return SuggestionsView;
    });