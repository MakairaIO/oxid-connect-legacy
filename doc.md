# Developer dpc

This oxid module is made for Oxid 6.2+. For older oxid versions you must install
`makaira/connect-oxid-compat` with PHP composer.

## Commands

`Touch all` and `Cleanup` are console commands now. They can be called like all oxid
console commands with `./vendor/bin/oe-console`:

-  `./vendor/bin/oe-console makaira:touch-all`
-  `./vendor/bin/oe-console makaira:cleanup`

## How to register a modifier

- Extent [Makaira\Connect\Modifier](src/Makaira/Connect/Modifier/Common/ShopModifier.php)
- Add modifier as service with [oxid dependency injection](https://docs.oxid-esales.com/developer/en/6.2/development/tell_me_about/service_container.html)
- Modifiers are injected by events. You need to tag the service in your `services.yaml` like this:

```yaml
  your_custom_modifier:
    class: Your\Custom\Mofifier\FoobarModifier
    public: true
    arguments:
      $database: '@Makaira\Connect\Database\DoctrineDatabase' # example constructor argument
    tags:
      - { name: 'kernel.event_listener', event: 'makaira.importer.modifier.product', priority: 1000, method: addModifier }      # register as product modifier
      - { name: 'kernel.event_listener', event: 'makaira.importer.modifier.variant', priority: 1000, method: addModifier }      # register as variant modifier
      - { name: 'kernel.event_listener', event: 'makaira.importer.modifier.category', priority: 1000, method: addModifier }     # register as category modifier
      - { name: 'kernel.event_listener', event: 'makaira.importer.modifier.manufacturer', priority: 1000, method: addModifier } # register as manufacturer modifier
```

- if have oxid < 6.2
  - use `makaira/connect-oxid-compat` to get a compatibility layer.
- you can force rebuilding the cached service container by deleting `MarmaladeConnectCompatServiceContainer.php` in `tmp`

      `rm -rf source/tmp/MarmaladeConnectCompatServiceContainer.php
