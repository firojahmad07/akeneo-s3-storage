# AttributeBundle

## Installation

#### Step 0: Install dependencies
The bundles which have to be installed and configured before continuing:

1. `ewave/core-bundle:^3.4.0`

#### Step 1: Add VCS repositories to the `composer.json`

```json
{
  "repositories": [
      {
          "type": "vcs",
          "url": "https://stash.ewave.com/scm/legoakeneo/attributebundle.git"
      }
  ]
}
```

#### Step 2: Download and install the bundle

```bash
composer require ewave/attribute-bundle:^3
```

#### Step 3: Register the bundle in application

Update list of bundles in your `app/AppKernel.php` as in the following example:

```php
<?php
class AppKernel extends Kernel
{
    public function registerProjectBundles()
    {
        return [
            //...
            new Ewave\Bundle\AttributeBundle\EwaveAttributeBundle()
            //...
        ];
    }
}
```

#### Step 4: Configure the bundle in application

Configure the required properties in project side configs as in the following example:
This adds the boolean property to all attributes. This one will be displayed on all attribute pages before attribute specific section.
 
```yaml
parameters:
    ...
    ewave_attribute.property_config.is_similar_product_comparison:
        propertyCode: is_similar_product_comparison
        propertyType: boolean
        attributeTypes:
            - 'pim_catalog_date'
            - 'pim_catalog_file'
            - 'pim_catalog_identifier'
            - 'pim_catalog_image'
            - 'pim_catalog_metric'
            - 'pim_catalog_multiselect'
            - 'pim_catalog_number'
            - 'pim_catalog_price_collection'
            - 'pim_catalog_simpleselect'
            - 'pim_catalog_text'
            - 'pim_catalog_textarea'
            - 'pim_catalog_boolean'
            - 'pim_assets_collection'
            - 'pim_reference_data_multiselect'
            - 'pim_reference_data_simpleselect'
            - 'akeneo_reference_entity_collection'
            - 'akeneo_reference_entity'
        default: false
        formExtensions:
            -
                module: pim/form/common/fields/boolean
                parents:
                    - 'pim-attribute-form-date'
                    - 'pim-attribute-form-file'
                    - 'pim-attribute-form-identifier'
                    - 'pim-attribute-form-image'
                    - 'pim-attribute-form-metric-edit'
                    - 'pim-attribute-form-select'
                    - 'pim-attribute-form-number'
                    - 'pim-attribute-form-price'
                    - 'pim-attribute-form-text'
                    - 'pim-attribute-form-textarea'
                    - 'pim-attribute-form-boolean'
                    - 'pim-attribute-form-assets-collection'
                    - 'pim-attribute-form-ref-data-multi'
                    - 'pim-attribute-form-ref-data-simple'
                    - 'pim-attribute-form-reference-entity'
                targetZone: content
                position:   92
                config:
                    fieldName: is_similar_product_comparison
                    label:     ewave_attribute.entity.attribute.property.is_similar_product_comparison.label
                    required:  false

    ewave_attribute.property_config.feature_type_value:
        propertyCode: feature_type_value
        propertyType: select
        attributeTypes:
            - 'pim_catalog_date'
            - 'pim_catalog_file'
            - 'pim_catalog_identifier'
            - 'pim_catalog_image'
            - 'pim_catalog_metric'
            - 'pim_catalog_multiselect'
            - 'pim_catalog_number'
            - 'pim_catalog_price_collection'
            - 'pim_catalog_simpleselect'
            - 'pim_catalog_text'
            - 'pim_catalog_textarea'
            - 'pim_catalog_boolean'
            - 'pim_assets_collection'
            - 'pim_reference_data_multiselect'
            - 'pim_reference_data_simpleselect'
            - 'akeneo_reference_entity_collection'
            - 'akeneo_reference_entity'
        default: number #This is the default value. You need to specify one option from choices: number,string,date, valuelist or or leave blank 
        formExtensions:
            -
                module: pim/form/common/fields/select
                parents:
                    - 'pim-attribute-form-date'
                    - 'pim-attribute-form-file'
                    - 'pim-attribute-form-identifier'
                    - 'pim-attribute-form-image'
                    - 'pim-attribute-form-metric-edit'
                    - 'pim-attribute-form-select'
                    - 'pim-attribute-form-number'
                    - 'pim-attribute-form-price'
                    - 'pim-attribute-form-text'
                    - 'pim-attribute-form-textarea'
                    - 'pim-attribute-form-boolean'
                    - 'pim-attribute-form-assets-collection'
                    - 'pim-attribute-form-ref-data-multi'
                    - 'pim-attribute-form-ref-data-simple'
                    - 'pim-attribute-form-reference-entity'
                targetZone: content
                position:  94
                config:
                    fieldName: feature_type_value
                    label: ewave_attribute.entity.attribute.property.feature_type_value.label
                    required: false
                    choices:
                        number: ewave_attribute.entity.attribute.property.feature_type_value.number
                        string: ewave_attribute.entity.attribute.property.feature_type_value.string
                        date: ewave_attribute.entity.attribute.property.feature_type_value.date
                        valuelist: ewave_attribute.entity.attribute.property.feature_type_value.valuelist

    ewave_attribute.property_config.feature_type_value_text:
        propertyCode: feature_type_value_text
        propertyType: text
        attributeTypes:
            - 'pim_catalog_date'
            - 'pim_catalog_file'
            - 'pim_catalog_identifier'
            - 'pim_catalog_image'
            - 'pim_catalog_metric'
            - 'pim_catalog_multiselect'
            - 'pim_catalog_number'
            - 'pim_catalog_price_collection'
            - 'pim_catalog_simpleselect'
            - 'pim_catalog_text'
            - 'pim_catalog_textarea'
            - 'pim_catalog_boolean'
            - 'pim_assets_collection'
            - 'pim_reference_data_multiselect'
            - 'pim_reference_data_simpleselect'
            - 'akeneo_reference_entity_collection'
            - 'akeneo_reference_entity'
        default: value example #This is the default value. Can be left blank
        formExtensions:
            -
                module: pim/form/common/fields/text
                parents:
                    - 'pim-attribute-form-date'
                    - 'pim-attribute-form-file'
                    - 'pim-attribute-form-identifier'
                    - 'pim-attribute-form-image'
                    - 'pim-attribute-form-metric-edit'
                    - 'pim-attribute-form-select'
                    - 'pim-attribute-form-number'
                    - 'pim-attribute-form-price'
                    - 'pim-attribute-form-text'
                    - 'pim-attribute-form-textarea'
                    - 'pim-attribute-form-boolean'
                    - 'pim-attribute-form-assets-collection'
                    - 'pim-attribute-form-ref-data-multi'
                    - 'pim-attribute-form-ref-data-simple'
                    - 'pim-attribute-form-reference-entity'
                targetZone: content
                position:  96
                config:
                    fieldName: feature_type_value_text
                    label: ewave_attribute.entity.attribute.property.feature_type_value_text.label
                    required: false

services:
    ewave_attribute.property.property_config.is_similar_product_comparison:
        class: '%ewave_attribute.property.property_config.class%'
        arguments:
            - '%ewave_attribute.property_config.is_similar_product_comparison%'
        tags:
            - { name: ewave_attribute.provider.attribute_property }

    ewave_attribute.property.property_config.feature_type_value:
        class: '%ewave_attribute.property.property_config.class%'
        arguments:
            - '%ewave_attribute.property_config.feature_type_value%'
        tags:
            - { name: ewave_attribute.provider.attribute_property }

    ewave_attribute.property.property_config.feature_type_value_text:
        class: '%ewave_attribute.property.property_config.class%'
        arguments:
            - '%ewave_attribute.property_config.feature_type_value_text%'
        tags:
            - { name: ewave_attribute.provider.attribute_property }

```

Add missing translations for newly created properties in jsmessages.*.yml
```yaml
ewave_attribute:
    entity:
        attribute:
            property:
                is_similar_product_comparison:
                    label: Is similar product comparison
                feature_type_value:
                    label: Feature type value
                    number: Number
                    string: String
                    date: Date
                    valuelist: Valuelist
                feature_type_value_text:
                    label: Feature type value text

```

## Useful commands
https://wiki.ewave.com/display/LEGO/Akeneo+Developer+Notes

## Manual
https://wiki.ewave.com/display/LEGO/Attribute+Bundle
