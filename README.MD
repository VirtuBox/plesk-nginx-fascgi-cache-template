# Nginx Fastcgi_cache custom template for Plesk Onyx

## Features

* Override default Nginx virtualhost template to work with WordPress in mode "Nginx Only"
* add support for Nginx fastcgi_cache
* add support for Nginx fastcgi_cache purge with Nginx-helper
* Bypass cache on cookies or request_uri
* add x-fastcgi-cache header
* add webp image rewrite support

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
plesk repair web
```

5) Disable Proxy mode in Plesk > Yourdomain.tld > Apache & nginx Settings