<h1 align="center">Souin API Platform Bundle</h1>

[Souin](https://gitub.com/darkweak/souin) bundle for api-platform

Installation:
-------------
```bash
$ composer require darkweak/souin-api-platform-bundle
```

Configuration:
--------------
```yaml
# config/api_platform.yaml
api_platform:
    # ... your previous configuration
    http_cache:
        invalidation:
            enabled: true
```

### Minimal bundle configuration
This is the minimal configuration for the SouinApiPlatform bundle.
```yaml
# config/souin_api_platform.yaml
souin_api_platform: # Mandatory to enable the Souin API Platform bridge instead of the Varnish one
```

### Fully detailled configuration
```yaml
# config/souin_api_platform.yaml
souin_api_platform: # Mandatory to enable the Souin API Platform bridge instead of the Varnish one
    base: # Declare the base url parameters
        host: http://caddy # Your reverse-proxy
        api_path: /souin-api
    api: # Declare the api parameters
        authentication: # Require a login to access the Souin security API
            username: john # Your username defined in the Souin configuration
            password: passw0rd # Your password defined in the Souin configuration
        souin: # Declare the souin parameters
            path: /souin-api # The Souin API path to manage the cache
```

## Contribution

If you have idea on how to improve this bundle, feel free to contribute. If you have problems or you found some bugs, please open an issue.
