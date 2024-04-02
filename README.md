# Akeneo S3 Storage

## Installation

#### Step 1: Add VCS repositories to the `composer.json`

```
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

```
   composer require spygar/akeneo-s3-storage:dev-master
```

#### Step 3: Register the bundle in application

Add to config/bundles.php:

```
  return [
        Spygar\Bundle\AkeneoS3StorageBundle\SpygarAkeneoS3StorageBundle::class => ['all' => true]
    ];
```
#### Step 4: Register the routes in application

Add to config/routes/spygar_akeneo_s3_storage_bundle.yml

```
 spygar_akeneo_s3_bundle:
    resource: "@SpygarAkeneoS3StorageBundle/Resources/config/routing.yml"
```

#### Step 5: Configure the bundle in application
```
    AWS_S3_BUCKET_NAME='akeneo7-s3'
    AWS_S3_REGION='us-west-1'
    AWS_S3_ACCESS_KEY='AKIA2UC3CMJSNNKMMNR7'
    AWS_S3_ACCESS_SECRET='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'
```