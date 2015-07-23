/**
 * Module which initializes javascript which are supposed to run on all pages, such as bootstrap plugins
 * initialization etc.
 */
define(['bootstrap', 'arrive', 'bootstrap-material-design', 'ripples'], function () {
    return {
        initialize: function () {
            $.material.init()
        }
    };
});