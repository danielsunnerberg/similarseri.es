define(['jquery', 'backbone', 'underscore', 'collections/suggestions', 'handlebars', 'text!templates/suggestions.html', 'views/suggestion','handlebarsHelpers/truncate', 'handlebarsHelpers/groupedEach'],
    function ($, Backbone, _, SuggestionsCollection, Handlebars, SuggestionsTemplate, SuggestionView) {
        var SuggestionsView = Backbone.View.extend({
            el: '#suggestions',
            suggestionViews: [],

            initialize: function (options) {
                this.setDefaultSettings();
                var that = this;
                options.events.bind('tv_show.added', function() {
                    that.refresh();
                });
                Handlebars.registerHelper('generatePosterUrl', this.generatePosterUrl);

                this.template = Handlebars.compile(SuggestionsTemplate);
                $(window).bind('scroll', function() {
                    that.checkScroll();
                });
            },

            setDefaultSettings: function () {
                this.isLoading = false;
                this.hasMoreSuggestions = true;
                this.suggestionsCollection = new SuggestionsCollection();
            },

            remove: function () {
                $(window).off('scroll', this.checkScroll);
            },

            render: function () {
                this.loadResults();
            },

            refresh: function() {
                this.setDefaultSettings();
                $(this.el).empty();
                this.render();
            },

            loadResults: function () {
                if (! this.hasMoreSuggestions) {
                    return;
                }

                var that = this;
                this.isLoading = true;
                this.suggestionsCollection.fetch({
                    success: function (suggestions) {
                        that.isLoading = false;
                        $('#user-suggestions-title').text('Suggestions generated for you');
                        that.insertSuggestions(suggestions.models);
                    },
                    error: function (model, response, options) {
                        that.isLoading = false;
                        $('.tv-show-item').remove();
                        $('#user-suggestions-title').text('An error occured. Please try again later.');
                    }
                })
            },

            insertSuggestions: function (suggestions) {
                var renderedSuggestions = [];
                suggestions.forEach(function (suggestion) {
                    renderedSuggestions.push(
                        new SuggestionView({ model: suggestion }).render().el.innerHTML
                    );
                });

                var template = this.template({
                    suggestions: renderedSuggestions
                });
                $(this.el).append(template);
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