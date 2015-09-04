define(['jquery', 'handlebars', 'ladda', 'text!templates/loadingIndicator.html'],
    function ($, Handlebars, Ladda, LoadingIndicatorTemplate) {
        var LoadingIndicatorView = Backbone.View.extend({

            template: Handlebars.compile(LoadingIndicatorTemplate)(),

            initialize: function (options) {
                this.externalEvents = options.externalEvents;
            },

            render: function () {
                this.setElement(this.template);
                return this;
            },

            show: function () {
                this.element = $(this.template);
                $(this.el).append(this.element);

                var laddaController = Ladda.create(this.element[0]);
                laddaController.start();

            },

            remove: function () {
                this.element.remove();
            }

        });

        return LoadingIndicatorView;
    }
);
