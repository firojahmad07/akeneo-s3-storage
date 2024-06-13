# Akeneo S3 Storage

Akeneo S3 Storage used to store akeneo images and assets like file inside s3 bucket to reduce extra storage space in akeneo.

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

#### Step 5: Configure aws s3 details inside .env file.
```
    AWS_S3_BUCKET_NAME='akeneo7-s3'
    AWS_S3_REGION='us-west-1'
    AWS_S3_ACCESS_KEY='AKIA2UC3CMJSNNKMMNR7'
    AWS_S3_ACCESS_SECRET='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'
    AWS_S3_DEFAULT_FOLDER='akeneo-assets'
```


#### Step 6: installation setup command
```
    rm -rf var/cache/** && bin/console pim:installer:assets && bin/console ca:cl && yarn run less && yarn run webpack && yarn run update-extensions
```