define(['jquery', 'backbone', 'underscore', 'handlebars', 'ladda', 'text!templates/suggestion.html', 'handlebarsHelpers/truncate'],
    function ($, Backbone, _, Handlebars, Ladda, SuggestionTemplate) {
        var SuggestionView = Backbone.View.extend({
            events: {
                'click .actions .tv-show-seen': 'alreadySeen'
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

            alreadySeen: function (e) {
                var that = this;

                var ladda = Ladda.create(e.currentTarget);
                ladda.start();

                this.model.save(null, {
                    success: function () {
                        that.externalEvents.trigger('tv_show.added');
                    },
                    error : function () {
                        alert("An error occurred. Please try again later.");
                    }
                }).always(function () {
                    ladda.stop();
                });
            }

        });

        return SuggestionView;
    });