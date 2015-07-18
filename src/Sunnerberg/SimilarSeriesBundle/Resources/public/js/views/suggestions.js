define(['jquery', 'backbone', 'underscore', 'collections/suggestions', 'handlebars', 'text!templates/suggestions.html', 'views/suggestion','handlebarsHelpers/truncate', 'handlebarsHelpers/groupedEach'],
    function ($, Backbone, _, SuggestionsCollection, Handlebars, SuggestionsTemplate, SuggestionView) {
        var SuggestionsView = Backbone.View.extend({
            el: '#suggestions',
            suggestionViews: [],

            initialize: function (options) {
                this.setDefaultSettings();
                var that = this;
                options.externalEvents.bind('tv_show.added', function() {
                    that.refresh();
                });

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
                    error: function () {
                        that.isLoading = false;
                        $('.tv-show-item').remove();
                        $('#user-suggestions-title').text('An error occured. Please try again later.');
                    }
                })
            },

            insertSuggestions: function (suggestions) {
                // @todo The code below is stupid and should be removed ASAP.
                // Inserting a sub-view's HTML by generating it through a template will forfeit all bound listeners.
                // Therefore, replacing a placeholder-id with the actual element will save listeners.
                // Run while you can.
                var that = this;
                var views = {};
                suggestions.forEach(function (suggestion) {
                    var externalEvents = _.extend({}, Backbone.Events);
                    views[suggestion.get('show').id] = new SuggestionView({model: suggestion, externalEvents: externalEvents});
                    externalEvents.bind('tv_show.added', function() {
                        that.refresh();
                    });
                });

                var template = $(this.template({
                    suggestions: _.keys(views)
                }));

                for (var id in views) {
                    if (! views.hasOwnProperty(id)) {
                        continue;
                    }

                    var view = views[id];
                    template.find('#' + id).parent().append(view.render().el);
                }

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