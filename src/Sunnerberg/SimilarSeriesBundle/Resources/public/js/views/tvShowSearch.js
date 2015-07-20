define(
    ['jquery', 'bloodhound', 'handlebars', 'backbone', 'ladda', 'text!templates/tvShowTypeahead.html', 'typeahead'],
    function($, Bloodhound, Handlebars, Backbone, Ladda, TvShowTypeaheadTemplate) {

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

                element.on('typeahead:selected', function (evt, item) {
                    var input = $(evt.currentTarget);
                    input.typeahead('val', '');
                    input.focus();

                    // @todo Use a more backbone-like way + template
                    var queueItem = $(
                        '<li class="list-group-item ladda-button" data-style="expand-left" data-size="xs" data-spinner-color="#000">' +
                            '<span class="ladda-label">'+ item.name +' ('+ item.airYear +')</span>' +
                        '</li>'
                    );
                    queueItem.hide();
                    $('.tv-show-add-queue').append(queueItem);
                    queueItem.fadeIn('fast');

                    var queueItemLoader = Ladda.create(queueItem[0]);
                    queueItemLoader.start();

                    $.ajax({
                        url: '/user/shows/' + item.tmdbId,
                        type: 'PUT',
                        success: function () {
                            that.events.trigger('tv_show.added');
                        }
                    }).always(function () {
                        queueItem.fadeOut('fast', function () {
                            queueItemLoader.stop();
                        });
                    });
                });
            }

        });
});