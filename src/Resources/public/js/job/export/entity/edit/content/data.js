'use strict';

define(
    [
        'jquery',
        'underscore',
        'oro/translator',
        'ewave-attribute/template/job/export/entity/edit/content/data',
        'pim/job/product/edit/content/data',
    ],
    function (
        $,
        _,
        __,
        template,
        BaseForm,
    ) {
        return BaseForm.extend({
            filterViews: [],
            template: _.template(template),

            /**
             * {@inheritdoc}
             */
            initialize: function (config) {
                this.config = config.config;

                BaseForm.prototype.initialize.apply(this, arguments);
            },

            /**
             * Get filter configuration for the giver field
             *
             * @param {string} fieldCode
             *
             * @return {Promise}
             */
            getFilterConfig: function (fieldCode) {
                var filterConfig = _.findWhere(this.config.filters, {field: fieldCode});

                if (undefined !== filterConfig) {
                    filterConfig.isRemovable = false;

                    return $.Deferred().resolve(filterConfig).promise();
                }
            },
        });
    }
);
