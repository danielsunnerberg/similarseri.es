requirejs(['/bundles/sunnerbergsimilarseries/js/main.js'], function () {
    require(['jquery', 'bloodhound', 'handlebars', 'typeahead'], function($, Bloodhound, Handlebars) {
        $(function() {

            var suggestionFormat = [
                '<div>',
                    '<img width="40" height="54" src="{{ posterUrl }}" alt="Poster for show: {{ name }}" />',
                    '{{ name }} <small>({{ airYear }})</small>',
                '</div>'
            ].join('\n');

            var showsSource = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: '/search/%QUERY.json',
                    wildcard: '%QUERY'
                }
            });

            var showSearchInput = $('#show-search');
            showSearchInput.typeahead(
                {
                    hint: true,
                    highlight: true,
                    minLength: 1
                },
                {
                    name: 'shows-source',
                    display: 'name',
                    source: showsSource,
                    templates: {
                        empty: [
                            '<div class="empty-message">',
                            'Found no TV-shows under that name.',
                            '</div>'
                        ].join('\n'),
                        suggestion: Handlebars.compile(suggestionFormat)
                    }

                }
            );

            showSearchInput.on('typeahead:selected', function(evt, item) {
                $.get('/user/shows/add/' + item.tmdbId, function() {
                   console.log("Done");
                });
            });

        });
    });
});