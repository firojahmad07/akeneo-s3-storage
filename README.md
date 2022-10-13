# AttributeBundle

## Installation

#### Step 0: Install dependencies
The bundles which have to be installed and configured before continuing:

1. `ewave/core-bundle:^6.0.0`

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

Add to config/bundles.php:

```
  return [
        // Add your bundles here with the associated env.
        // Ex:
        Ewave\Bundle\AttributeBundle\EwaveAttributeBundle::class => ['all' => true]
    ];
```
#### Step 4: Register the routes in application

Add to config/routes/ewave_attribute_bundle.yml

```
 ewave_attribute_bundle:
    resource: "@EwaveAttributeBundle/Resources/config/routing.yml"
```

#### Step 5: Configure the bundle in application

Configure the required properties in project side configs as in the following example:
This configuration you can add  {bundlePath}/Resource/config/ewave/AttributeProperty/parameters.yml.
 
```yaml
parameters:
    ...
    parameters:
    ewave_attribute.property_config.is_similar_product_comparison:
        propertyType: boolean
        attributeTypes:
            - 'pim_catalog_date'
            - 'pim_catalog_file'
            - 'pim_catalog_identifier'
            - 'pim_catalog_image'
        default: true
        config:
            fieldName: is_similar_product_comparison
            label: ewave_attribute.entity.attribute.property.is_similar_product_comparison.label

    ewave_attribute.property_config.is_similar_product_test:
        propertyType: text
        attributeTypes:
            - 'pim_catalog_date'
            - 'pim_catalog_image'
            - 'akeneo_reference_entity'
        default: ''
        config:
            fieldName: feature_type_value_text
            label: ewave_attribute.entity.attribute.property.feature_type_value_text.label

    ewave_attribute.property_config.is_similar_product_select:
        propertyType: select
        attributeTypes:
            - 'pim_catalog_date'
            - 'pim_catalog_image'
            - 'akeneo_reference_entity'
        default: 'number'
        config:
            isMultiple: false # if you to make this attribute as multi-select just make it true.
            fieldName: feature_type_value
            label: ewave_attribute.entity.attribute.property.feature_type_value.label
            choices:
                number: ewave_attribute.entity.attribute.property.feature_type_value.choices.number
                string: ewave_attribute.entity.attribute.property.feature_type_value.choices.string
                date: ewave_attribute.entity.attribute.property.feature_type_value.choices.date
                valuelist: ewave_attribute.entity.attribute.property.feature_type_value.choices.valuelist

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
                    choices:
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
