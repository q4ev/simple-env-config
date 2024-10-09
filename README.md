# simple-env-config
Singleton to parse .env file into php code for easier components configuration

It's supposed you defined global constant "ROOT_DIR" that leads to your root folder, where desired .env file presents.

PHP >=7.4

After installing add this code to your "<...>/bootstrap.php":

```php
function env ($key, $defaultValue = null)
{
    return \q4ev\simpleEnvConfig\EnvConfig::get($key, $defaultValue);
}
```
