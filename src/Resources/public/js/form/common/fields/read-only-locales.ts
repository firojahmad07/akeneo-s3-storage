const BaseAvailableLocales = require('pimui/js/attribute/form/properties/available-locales');

class ReadOnlyLocales extends BaseAvailableLocales {
  /**
   * {@inheritdoc}
   */
  isVisible() {
    return undefined !== this.getFormData().is_localized_read_only && this.getFormData().is_localized_read_only;
  }
}

export = ReadOnlyLocales;
