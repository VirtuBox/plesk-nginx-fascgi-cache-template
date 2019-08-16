# Nginx Fastcgi_cache custom template for Plesk Onyx

## Features

* Override default Nginx virtualhost template to work with WordPress in mode "Nginx Only"
* set fastcgi_cache bypass rules globally on the server using the directive map instead of several "if".
* add support for Nginx fastcgi_cache purge with Nginx-helper
* Bypass cache on cookies or request_uri
* add webp image rewrite support
* Deny access to hidden files and directory (.git, .htaccess, etc ..)

## Requirements

* Nginx compiled with Nginx_cache_purge module : You can use [nginx-ee](https://virtubox.github.io/nginx-ee/) to compile Nginx

**I also added a custom template compatible with stock Nginx package shipped by Plesk, available here** : [nginx-stock](https://github.com/VirtuBox/plesk-nginx-fascgi-cache-template/tree/nginx-stock)

## Limitations

* Custom rules to bypass cache set in Plesk are not applied
* fastcgi_cache template work only for wordpress

## How to use this template

1) Clone the repository

```bash
git clone https://github.com/VirtuBox/plesk-nginx-fascgi-cache-template.git /usr/local/psa/admin/conf/templates/custom
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
# let plesk detect custom template
plesk repair web -n
# rebuild vhost with custom template
plesk repair web -y -domains-only
```

5) Disable Proxy mode in Plesk > Yourdomain.tld > Apache & nginx Settings

6) Enable Nginx cache in Plesk > Yourdomain.tld > Apache & nginx Settings

7) Login into WordPress & install [Nginx-helper](https://wordpress.org/plugins/nginx-helper/) plugin

8) In Nginx-helper Settings, Enable Purge

## How to update this template

Just run :

```bash
git -C /usr/local/psa/admin/conf/templates/custom pull origin master
```

And relaunch plesk repair web to apply changes :

```bash
plesk repair web
```
