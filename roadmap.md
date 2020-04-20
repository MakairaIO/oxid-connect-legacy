- compatibility with older oxid versions
  - include [symfony DI](https://github.com/symfony/dependency-injection) for older oxid versions (through composer/vendor directory)
    - load `services.yaml` from oxid modules like [marm/yamm](https://github.com/marmaladeDE/yamm) did with `dic.php`
      (like done in [OxidMocks/ContainerFactory.php](tests/Makaira/Connect/OxidMocks/ContainerFactory.php))
    - make all calls to `ContainerFactory` through a new compatibility class
    - use [Dumping the Configuration for Performance](https://symfony.com/doc/current/components/dependency_injection/compilation.html#dumping-the-configuration-for-performance)
      - trigger recompile on module activation/deactivation
    - add [EventDispatcher](https://github.com/symfony/event-dispatcher) as service
    - register [RegisterListenersPass](https://github.com/symfony/event-dispatcher/blob/master/DependencyInjection/RegisterListenersPass.php)
      to make adding modifiers through events possible
    - add [Doctrine\DBAL\Connection](https://github.com/doctrine/dbal) as service
      - get connection configuration from oxid config
  - add helper script to call touch-all and cleanup command without oxid console
  - &#128591;
