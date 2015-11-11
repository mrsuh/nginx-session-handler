nginx-session-handler
=====================

Bundle require snc/redis-bundle. First setting it.
install
--
```
composer require mrsuh/nginx-session-handler:1.*
```


add to app/AppKernel.php

```
...
new Mrsuh\NginxSessionHandlerBundle\MrsuhNginxSessionHandlerBundle()
...
```


add to config.yml
```
...
framework:
    session:
        handler_id:  mrsuh.session_handler
...
mrsuh_nginx_session_handler:
    session_lifetime: 3600
    session_prefix: phpsession
...
```


add to nginx.conf
```
location /security {
content_by_lua_file session.lua;
}
```