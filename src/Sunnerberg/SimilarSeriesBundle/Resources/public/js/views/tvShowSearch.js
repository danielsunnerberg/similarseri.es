define(
    ['jquery', 'bloodhound', 'handlebars', 'backbone', 'ladda', 'models/suggestion', 'text!templates/tvShowTypeahead.html', 'text!templates/queueItem.html', 'typeahead'],
    function($, Bloodhound, Handlebars, Backbone, Ladda, SuggestionModel, TvShowTypeaheadTemplate, QueueItemTemplate) {

        return Backbone.View.extend({
            el: '#show-search',

            initialize: function (options) {
                this.events = options.events;
                this.suggestionTemplate = Handlebars.compile(TvShowTypeaheadTemplate);
                this.queueItemTemplate = Handlebars.compile(QueueItemTemplate);
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

                element.on('typeahead:selected', this.addSelectedShow.bind(this));
            },

            addSelectedShow: function (evt, item) {
                var input = $(evt.currentTarget);
                input.typeahead('val', '');
                input.focus();

                var queueItem = $(this.queueItemTemplate(item));
                queueItem.hide();
                $('.tv-show-add-queue').append(queueItem);
                queueItem.fadeIn('fast');

                var queueItemLoader = Ladda.create(queueItem[0]);
                queueItemLoader.start();

                var that = this;
                var show = new SuggestionModel();
                show.save({id: item.tmdbId}, {
                    success: function () {
                        that.events.trigger('tv_show.added');
                    }
                }).always(function () {
                    queueItem.fadeOut('fast', function () {
                        queueItemLoader.stop();
                    });
                });
            }

        });
});