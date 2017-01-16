# nginx-session-handler #

[![Latest Stable Version](https://poser.pugx.org/mrsuh/nginx-session-handler/v/stable)](https://packagist.org/packages/mrsuh/nginx-session-handler)
[![Total Downloads](https://poser.pugx.org/mrsuh/nginx-session-handler/downloads)](https://packagist.org/packages/mrsuh/nginx-session-handler)
[![License](https://poser.pugx.org/mrsuh/nginx-session-handler/license)](https://packagist.org/packages/mrsuh/nginx-session-handler)

This bundle integrates [predis](https://github.com/nrk/predis) and [snc/redis-bundle](https://github.com/snc/SncRedisBundle) into your Symfony3 application
So, you need to configure the bundles too.

## Installation ##

Add the nginx-session-handler package to your require section in the composer.json file.

```bash
composer require mrsuh/nginx-session-handler:2.*
```

Add the NginxSessionHandlerBundle to your application's kernel:

``` php
<?php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Mrsuh\NginxSessionHandlerBundle\MrsuhNginxSessionHandlerBundle(),
        // ...
    );
    ...
}
```

Configure the `session`  in your `config.yml`:
```yaml
framework:
    session:
        handler_id:  mrsuh.session_handler

mrsuh_nginx_session_handler:
    session_lifetime: 3600
    session_prefix: phpsession

```

Add  `session.lua` script to your nginx.conf
```apacheconf
location /security {
content_by_lua_file session.lua;
}
```

Now your php session locate in redis. Your location `/security` allow for authenticated users with role ROLE_ADMIN only.