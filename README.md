nginx-session-handler
=====================

install
--
```
composer require mrsuh/nginx-session-handler:1.*
```


add to app/AppKernel.php

```
new Mrsuh\NginxSessionHandlerBundle\MrsuhNginxSessionHandlerBundle()
```


add to config.yml
```
mrsuh_nginx_session_handler:
    session_lifetime: 3600
    session_prefix: phpsession

framework:
    session:
        handler_id:  mrsuh.session_handler
```
