'use strict';

define([
    'underscore',
    'oro/translator',
    'pim/filter/attribute/select',
    'pim/fetcher-registry',
    'ewave-attribute/template/job/export/entity/edit/content/data/select',
    'jquery.select2'
], function (
    _,
    __,
    BaseFilter,
    FetcherRegistry,
    template
) {
    return BaseFilter.extend({
        shortname: 'type',
        template: _.template(template),
        choicePromise: null,
        attributeList: [],
        events: {
            'change [name="filter-value"], [name="filter-operator"]': 'updateState'
        },

        /**
         * {@inheritdoc}
         */
        initialize: function (config) {
            this.config = config.config;

            return BaseFilter.prototype.initialize.apply(this, arguments);
        },

        /**
         * {@inheritdoc}
         */
        configure: function () {
            return $.when(
                FetcherRegistry.getFetcher('attribute-list').fetchAll(),
                this.listenTo(this.getRoot(), 'pim_enrich:form:entity:pre_update', function (data) {
                    _.defaults(data, {field: this.getCode()});
                }.bind(this)),

                BaseFilter.prototype.configure.apply(this, arguments)
            ).then(function (attributeList) {
                this.attributeList = attributeList;
            }.bind(this));
        },

        /**
         * {@inheritdoc}
         */
        renderInput: function () {
            if (_.isUndefined(this.getOperator())) {
                this.setOperator(_.first(_.values(this.config.operators)));
            }

            if (_.isEmpty(this.getValue()) && _.contains(['=', '!='], this.getOperator())) {
                this.setValue(_.first(_.keys(this.getValueChoices())), {silent: false});
            }

            return BaseFilter.prototype.renderInput.apply(this, arguments);
        },

        /**
         * {@inheritdoc}
         */
        getTemplateContext: function () {
            return {
                label: __(this.config.label),
                help: __(this.config.help),
                removable: false,
                editable: this.isEditable()
            };
        },

        /**
         * Renders extension elements of the filter.
         */
        renderElements: function () {
            _.each(this.elements, function (elements, position) {
                var $container = this.$('.' + position + '-elements-container');
                $container.empty();

                _.each(elements, function (element) {
                    if ('function' === typeof element.render) {
                        $container.append(element.render().$el);
                    } else {
                        $container.append(element);
                    }
                });
            }.bind(this));
        },


        /**
         * {@inheritdoc}
         */
        postRender: function (templateContext) {
            this.$('.operator').select2({
                minimumResultsForSearch: -1
            });

            if (!_.contains(['ALL', 'EMPTY', 'NOT EMPTY'], this.getOperator())) {
                this.$('input.value').select2(this.getSelect2Options());
            }
        },

        /**
         * Return the choice options or reference data to populate the select2.
         *
         * @returns {Object}
         */
        getSelect2Options: function () {
            return {
                data: this.getValueSelect2Choices(),
                multiple: this.getOperator() === 'IN' || this.getOperator() === 'NOT IN'
            };
        },

        getValueChoices: function () {
            return this.attributeList;
        },

        getValueSelect2Choices: function () {
            return _.map(this.getValueChoices(), function (value, key) {
                return {id: key, text: __(value)};
            });
        },

        /**
         * Returns the list of the operator choices with their translations.
         *
         * @returns {object}
         */
        getLabelledOperatorChoices(shortName) {
            let result = {};
            this.config.operators.forEach((operator) => {
                const key = 'ewave_attribute.form.job_instance.tab.content.data.' + shortName + '.operators.' + operator;
                let translation = __(key);
                if (translation === key) {
                    translation = __('pim_common.operators.' + operator);
                }
                result[operator] = translation;
            });

            return result;
        },

        /**
         * {@inheritdoc}
         */
        updateState: function () {
            var cleanedValues = [];
            var operator = this.$('[name="filter-operator"]').val();

            if (!_.contains(['ALL', 'EMPTY', 'NOT EMPTY'], operator)) {
                var value = this.$('[name="filter-value"]').val();
                value = _.isString(value) ? value.split(/[\s,]+/) : value;
                cleanedValues = _.reject(value, function (val) {
                    return '' === val;
                });
            }

            this.setData({
                field: this.getField(),
                operator: operator,
                value: cleanedValues
            });

            this.render();
        },

        isEditable: function () {
            let editable = BaseFilter.prototype.isEditable.apply(this, arguments);
            return editable && (_.isArray(this.config.operators) && this.config.operators.length > 1);
        },
    });
});
