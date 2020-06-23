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

            if (false === this.getFormData().is_localized_read_only) {
                this.setData({read_only_locales: []}, {silent: true});
            }
        }
    });
});
