# Akeneo S3 Storage

## Installation

#### Step 1: Add VCS repositories to the `composer.json`

```json
{
    "require": {
        "spygar/akeneo-s3-storage": "dev-master",
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/firojahmad07/akeneo-s3-storage.git"
        }
    ]
}
```

#### Step 2: Download and install the bundle

```bash
   composer require spygar/akeneo-s3-storage:dev-master
```

#### Step 3: Register the bundle in application

Add to config/bundles.php:

```
  return [
        // Add your bundles here with the associated env.
        // Ex:
        Spygar\Bundle\AkeneoS3StorageBundle\SpygarAkeneoS3StorageBundle::class => ['all' => true]
    ];
```
#### Step 4: Register the routes in application

Add to config/routes/spygar_akeneo_se_storage_bundle.yml

```
 spygar_attribute_bundle:
    resource: "@SpygarAkeneoS3StorageBundle/Resources/config/routing.yml"
```

#### Step 5: Configure the bundle in application

Configure the required properties in project side configs as in the following example:
This configuration you can add  {bundlePath}/Resource/config/ewave/AttributeProperty/parameters.yml.
 