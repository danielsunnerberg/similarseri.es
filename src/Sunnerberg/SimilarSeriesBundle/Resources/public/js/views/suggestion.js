define(['jquery', 'backbone', 'underscore', 'handlebars', 'text!templates/suggestion.html', 'handlebarsHelpers/truncate'],
    function ($, Backbone, _, Handlebars, SuggestionTemplate) {
        var SuggestionView = Backbone.View.extend({
            template: Handlebars.compile(SuggestionTemplate),

            render: function () {
                var template = this.template(this.model.toJSON());
                $(this.el).html(template);
                return this;
            }

        })

        return SuggestionView;
    });