ApiBundle
==========

Api features for EkynaResourceBundle.

## Installation

Install through composer:

    composer require ekyna/api-bundle:^0.8

Register the bundles:

```php
// config/bundles.php
<?php

return [
    // ...

    Ekyna\Bundle\ResourceBundle\EkynaResourceBundle::class => ['all' => true],
    Ekyna\Bundle\ApiBundle\EkynaApiBundle::class => ['all' => true],
];

```

## Configuration

See [this page](https://github.com/ekyna/ResourceBundle) to configure ekyna/resource-bundle.

```yaml
ekyna_api:
    routing_prefix: /api
```
