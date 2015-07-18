define(['jquery', 'backbone', 'underscore', 'handlebars', 'text!templates/suggestion.html', 'handlebarsHelpers/truncate'],
    function ($, Backbone, _, Handlebars, SuggestionTemplate) {
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

            alreadySeen: function () {
                var that = this;
                this.model.save(null, {
                    success: function () {
                        that.externalEvents.trigger('tv_show.added');
                    },
                    error : function () {
                        alert("An error occured. Please try again later.");
                    }
                });
            }

        });

        return SuggestionView;
    });