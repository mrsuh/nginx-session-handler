nginx-session-handler
=====================

install
--
```
composer require mrsuh/nginx-session-handler
```


in app/AppKernel.php

```
new Anton\TBundle\AntonTBundle()
```


in config.yml
```
mrsuh_ngxin_session_handler:
    session_lifetime: 3600
    session_prefix: phpsession

framework:
    session:
        handler_id:  anton_t.test
```
