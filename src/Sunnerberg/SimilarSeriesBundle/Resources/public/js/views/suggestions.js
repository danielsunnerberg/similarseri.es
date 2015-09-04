define(['jquery', 'backbone', 'underscore', 'collections/suggestions', 'handlebars', 'views/suggestion', 'views/loadingIndicator', 'handlebarsHelpers/truncate', 'handlebarsHelpers/groupedEach'],
    function ($, Backbone, _, SuggestionsCollection, Handlebars, SuggestionView, LoadingIndicator) {
        var SuggestionsView = Backbone.View.extend({
            el: '#suggestions',
            suggestionViews: [],
            ITEMS_PER_ROW: 3,

            initialize: function (options) {
                this.setDefaultSettings();
                var that = this;

                options.externalEvents.bind('tv_show.added', function() {
                    that.refresh();
                });

                $(window).bind('scroll', function() {
                    that.checkScroll();
                });
            },

            setDefaultSettings: function () {
                this.isLoading = false;
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
                var container = $(this.el);
                var that = this;
                container.children().fadeOut('fast').promise().done(function () {
                    container.empty();
                    that.render()
                });
            },

            loadResults: function () {
                if (! this.suggestionsCollection.hasMoreSuggestions) {
                    return;
                }

                var loadingIndicator = new LoadingIndicator({ el: this.el });
                loadingIndicator.show();

                var that = this;
                this.isLoading = true;
                this.suggestionsCollection.fetch({
                    success: function (suggestions) {
                        that.isLoading = false;
                        that.insertSuggestions(suggestions.models);
                        if (that.suggestionsCollection.fallbackUsed && $(that.el).has('.alert').length === 0) {
                            that.displayFallbackNotice();
                        }
                    },
                    error: function () {
                        that.isLoading = false;
                        $(that.el).html('An error occured. Please try again later.');
                    },
                    complete: function () {
                        loadingIndicator.remove();
                    }
                });
            },

            displayFallbackNotice: function () {
                require(['text!templates/alert.html'], function (alertTemplate) {
                    alertTemplate = Handlebars.compile(alertTemplate);
                    $(this.el).prepend(alertTemplate({type: 'info', message: '<i class="mdi-action-info align-bottom"></i> <b>You have not added any shows.</b> Until you do, you can find shows our users have enjoyed below as inspiration.'}));
                }.bind(this));
            },

            createSuggestionGroup: function () {
                var group = $('<div class="row row-eq-height"></div>');
                $(this.el).append(group);
                return group;
            },

            insertSuggestions: function (suggestions) {
                var externalEvents = _.extend({}, Backbone.Events);
                externalEvents.bind('tv_show.added', function() {
                    this.refresh();
                }.bind(this));

                var group;
                for (var i = 0; i < suggestions.length; i++) {
                    var view = new SuggestionView({model: suggestions[i], externalEvents: externalEvents}).render().el;

                    if (i % this.ITEMS_PER_ROW === 0) {
                        group = this.createSuggestionGroup();
                    }

                    group.append(view);
                }
            },

            checkScroll: function () {
                var triggerPoint = 300;
                var element = $(window);
                if (! this.isLoading && element.scrollTop() + element.height() + triggerPoint > document.body.scrollHeight) {
                    this.suggestionsCollection.page += 1;
                    this.loadResults();
                }
            }

        });

        return SuggestionsView;
    }
);
