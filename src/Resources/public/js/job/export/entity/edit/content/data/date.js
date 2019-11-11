'use strict';

define([
    'jquery',
    'underscore',
    'oro/translator',
    'pim/filter/product/updated',
    'ewave-attribute/template/job/export/entity/edit/content/data/date',
    'pim/date-context',
    'pim/formatter/date'
], function (
    $,
    _,
    __,
    BaseFilter,
    template,
    DateContext,
    DateFormatter
) {
    return BaseFilter.extend({
        shortname: '',

        template: _.template(template),
        events: {
            'change [name="filter-operator"], [name="filter-value"]': 'updateState'
        },

        /* Date widget options */
        datetimepickerOptions: {
            format: DateContext.get('date').format,
            defaultFormat: DateContext.get('date').defaultFormat,
            language: DateContext.get('language')
        },

        /* Model date format */
        modelDateFormat: 'yyyy-MM-dd HH:mm:ss',

        /**
         * Initializes configuration.
         *
         * @param config
         */
        initialize: function (config) {
            this.config = config.config;

            return BaseFilter.prototype.initialize.apply(this, arguments);
        },

        /**
         * {@inheritdoc}
         */
        configure: function () {
            this.listenTo(this.getRoot(), 'pim_enrich:form:entity:pre_update', function (data) {
                _.defaults(data, {field: this.getCode(), operator: _.first(_.values(this.config.operators))});
            }.bind(this));

            return BaseFilter.prototype.configure.apply(this, arguments);
        },

        /**
         * Gets the template context.
         *
         * @returns {Promise}
         */
        getTemplateContext: function () {
            return $.Deferred().resolve({
                label: __('ewave_attribute.form.job_instance.tab.content.data.' + this.getCode() + '.title'),
                removable: this.isRemovable(),
                editable: this.isEditable()
            }).promise();
        },

        /**
         * Updates operator and value on fields change.
         * Value is reset after operator has changed.
         */
        updateState: function () {
            this.$('.date-wrapper:first').datetimepicker('hide');

            var oldOperator = this.getOperator();
            var value = this.$('[name="filter-value"]').val();
            var operator = this.$('[name="filter-operator"]').val();

            if (operator !== oldOperator) {
                value = '';
            }

            if ('>' === operator) {
                value = DateFormatter.format(value, DateContext.get('date').format, this.modelDateFormat);
            } else if ('SINCE LAST JOB' === operator) {
                value = this.getParentForm().getFormData().code;
            }
            if (_.isUndefined(value)) {
                value = '';
            }

            this.setData({
                field: this.getField(),
                operator: operator,
                value: value
            });

            this.render();
        }
    });
});
