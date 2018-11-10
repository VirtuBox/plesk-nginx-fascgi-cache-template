# Nginx Fastcgi_cache custom template for Plesk Onyx

## Features

* Override default Nginx virtualhost template to work with WordPress in mode "Nginx Only"
* add support for Nginx fastcgi_cache
* add support for Nginx fastcgi_cache purge with Nginx-helper
* Bypass cache on cookies or request_uri
* add x-fastcgi-cache header


## How to use this template

1. Clone the repository

```bash
git clone x /usr/local/psa/admin/conf/templates/custom
```

2. Copy Nginx configuration in Nginx conf.d directory
