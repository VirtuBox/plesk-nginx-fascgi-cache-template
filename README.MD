# Nginx Fastcgi_cache custom template for Plesk Onyx (nginx-stock)

## Features

* Override default Nginx virtualhost template to work with WordPress in mode "Nginx Only"
* set fastcgi_cache bypass rules globally on the server using the directive map instead of several "if".
* Compatible with **stock Nginx** shipped with Plesk
* Fastcgi_cache purge can be performed by the plugin Nginx-helper
* Bypass cache on cookies or request_uri
* add webp image rewrite support
* Deny access to hidden files and directory (.git, .htaccess, etc ..)

## Limitations

* Custom rules to bypass cache set in Plesk are not applied
* fastcgi_cache template work only for wordpress

## How to use this template

1) Clone the repository

```bash
git clone -b nginx-stock https://github.com/VirtuBox/plesk-nginx-fascgi-cache-template.git /usr/local/psa/admin/conf/templates/custom
```

2) Copy Nginx configuration in Nginx conf.d directory

```bash
cp -f /usr/local/psa/admin/conf/templates/custom/nginx-conf/* /etc/nginx/conf.d/
```

3) Reload nginx

```bash
nginx -t && service nginx reload
```

4) Apply new nginx virtualhost template

```bash
plesk repair web -y
```

5) Disable Proxy mode in Plesk > Yourdomain.tld > Apache & nginx Settings

6) Enable Nginx cache in Plesk > Yourdomain.tld > Apache & nginx Settings

7) Login into WordPress & install [Nginx-helper](https://wordpress.org/plugins/nginx-helper/) plugin

8) Define Nginx RT_WP_NGINX_HELPER_CACHE_PATH path in your wp-config.php

```php
define('RT_WP_NGINX_HELPER_CACHE_PATH', '/var/cache/nginx/site.tld_fastcgi');
```

9) Add this path in your domain PHP Settings > open_basedir settings :

```conf
{WEBSPACEROOT}{/}{:}{TMP}{/}{:}/var/cache/nginx/site.tld_fastcgi{/}
```

10) In Nginx-helper Settings, Enable Purge and set method to "Delete local server cache files"

## How to update this template

Just run :

```bash
git -C /usr/local/psa/admin/conf/templates/custom pull origin nginx-stock
```

And relaunch plesk repair web to apply changes :

```bash
plesk repair web
```
