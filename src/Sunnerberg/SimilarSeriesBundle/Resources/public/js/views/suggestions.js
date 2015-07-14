define(
    ['jquery', 'backbone', 'underscore', 'collections/suggestions', 'handlebars', 'text!templates/suggestions.html', 'handlebarsHelpers/truncate'],
    function ($, Backbone, _, SuggestionsCollection, Handlebars, SuggestionsTemplate) {
        var SuggestionsView = Backbone.View.extend({
            el: '#suggestions',

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

            generatePosterUrl: function (show, posterBaseUrl) {
                return new Handlebars.SafeString('<img class="poster responsive-image" src="'
                + posterBaseUrl + show.posterUrl
                + '" alt="Poster image for ' + show.name
                + '" />');
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
                    success: function (response) {
                        that.isLoading = false;
                        that.hasMoreSuggestions = response.models[0].get('hasMoreSuggestions');
                        var suggestions = response.models[0].get('suggestions');
                        var posterBaseUrl = response.models[0].get('posterBaseUrl');

                        if (suggestions.length == 0) {
                            return;
                        }
                        $('#user-suggestions-title').text('Suggestions generated for you');

                        var template = that.template({
                            suggestions: suggestions,
                            posterBaseUrl: posterBaseUrl
                        });
                        $(that.el).append(template);

                    },
                    error: function (model, response, options) {
                        that.isLoading = false;
                        $('.tv-show-item').remove();
                        $('#user-suggestions-title').text('An error occured. Please try again later.');
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