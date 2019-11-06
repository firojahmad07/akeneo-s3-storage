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



## Useful commands
https://wiki.ewave.com/display/LEGO/Akeneo+Developer+Notes

## Manual
https://wiki.ewave.com/display/LEGO/Attribute+Bundle
