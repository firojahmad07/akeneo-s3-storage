'use strict';

define([
    'pim/form/common/fields/boolean'
],
function (
    BaseField
) {
    return BaseField.extend({
        /**
         * {@inheritdoc}
         */
        updateModel: function () {
            BaseField.prototype.updateModel.apply(this, arguments);

            this.setData({read_only_locales: []}, {silent: true});
        }
    });
});
