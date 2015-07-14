define(
    ['jquery', 'bloodhound', 'handlebars', 'backbone', 'text!templates/tvShowTypeahead.html', 'typeahead'],
    function($, Bloodhound, Handlebars, Backbone, TvShowTypeaheadTemplate) {

        return Backbone.View.extend({
            el: '#show-search',

            initialize: function (options) {
                this.events = options.events;
                this.suggestionTemplate = Handlebars.compile(TvShowTypeaheadTemplate);
                this.source = new Bloodhound({
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    remote: {
                        url: '/search/%QUERY.json',
                        wildcard: '%QUERY'
                    }
                });
            },

            render: function () {
                var that = this;
                var element = $(this.el);
                element.typeahead(
                    {
                        hint: true,
                        highlight: true,
                        minLength: 1
                    },
                    {
                        name: 'shows-source',
                        display: 'name',
                        source: this.source,
                        templates: {
                            empty: [
                                '<div class="empty-message">',
                                'Found no TV-shows under that name.',
                                '</div>'
                            ].join('\n'),
                            suggestion: this.suggestionTemplate
                        }

                    }
                );

                element.on('typeahead:selected', function(evt, item) {
                    // @todo Use a more backbone-like way
                    $.ajax({
                        url: '/user/shows/' + item.tmdbId,
                        type: 'PUT',
                        success: function() {
                            that.events.trigger('tv_show.added');
                        }
                    });
                });
            }

        });
});