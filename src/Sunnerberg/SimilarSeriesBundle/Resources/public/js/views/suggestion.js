define(['jquery', 'backbone', 'underscore', 'handlebars', 'ladda', 'text!templates/suggestion.html', 'handlebarsHelpers/truncate'],
    function ($, Backbone, _, Handlebars, Ladda, SuggestionTemplate) {
        var SuggestionView = Backbone.View.extend({
            events: {
                'click .actions .tv-show-seen': 'alreadySeen',
                'click .actions .tv-show-ignore': 'ignore'
            },
            template: Handlebars.compile(SuggestionTemplate),

            initialize: function (options) {
                this.externalEvents = options.externalEvents;
            },

            render: function () {
                var template = this.template(this.model.toJSON());
                this.setElement(template);
                return this;
            },

            responseHandler: function (eventAlias) {
                var that = this;
                return {
                    success: function () {
                        that.externalEvents.trigger(eventAlias);
                    },
                    error: function () {
                        alert("An error occurred. Please try again later.");
                    }
                }
            },

            alreadySeen: function (e) {
                var ladda = Ladda.create(e.currentTarget);
                ladda.start();

                this.model.save(null, this.responseHandler('tv_show.added')).always(function () {
                    ladda.stop();
                });
            },

            ignore: function (e) {
                var ladda = Ladda.create(e.currentTarget);
                ladda.start();

                this.model.ignore(null, this.responseHandler('tv_show.added')).always(function () {
                    ladda.stop();
                });
            }

        });

        return SuggestionView;
    });
