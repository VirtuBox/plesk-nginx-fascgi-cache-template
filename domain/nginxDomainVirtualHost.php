<?php
/**
 * @var Template_VariableAccessor $VAR
 * @var array $OPT
 */
?>
server {
    listen <?php echo $OPT['ipAddress']->escapedAddress . ':' . $OPT['frontendPort'] .
        ($OPT['default'] ? ' default_server' : '') . ($OPT['ssl'] ? ' ssl' : '') .
        ($OPT['ssl'] && $VAR->domain->physicalHosting->proxySettings['nginxHttp2'] ? ' http2' : '') ?>;

    server_name <?php echo $VAR->domain->asciiName ?>;
<?php if ($VAR->domain->isWildcard): ?>
    server_name ~^<?php echo $VAR->domain->pcreName ?>$;
<?php else: ?>
    server_name www.<?php echo $VAR->domain->asciiName ?>;
    <?php if ($OPT['ipAddress']->isIpV6()): ?>
    server_name ipv6.<?php echo $VAR->domain->asciiName ?>;
    <?php else: ?>
    server_name ipv4.<?php echo $VAR->domain->asciiName ?>;
    <?php endif ?>
<?php endif ?>
<?php if ($VAR->domain->webAliases): ?>
    <?php foreach ($VAR->domain->webAliases as $alias): ?>
    server_name <?php echo $alias->asciiName ?>;
    server_name www.<?php echo $alias->asciiName ?>;
    <?php endforeach ?>
<?php endif ?>
<?php if ($VAR->domain->previewDomainName): ?>
    server_name "<?php echo $VAR->domain->previewDomainName ?>";
<?php endif ?>

<?php if ($OPT['ssl']): ?>
<?php $sslCertificate = $VAR->server->sni && $VAR->domain->physicalHosting->sslCertificate ?
    $VAR->domain->physicalHosting->sslCertificate :
    $OPT['ipAddress']->sslCertificate; ?>
    <?php if ($sslCertificate->ce): ?>
    ssl_certificate             <?php echo $sslCertificate->ceFilePath ?>;
    ssl_certificate_key         <?php echo $sslCertificate->ceFilePath ?>;
        <?php if ($sslCertificate->ca): ?>
    ssl_client_certificate      <?php echo $sslCertificate->caFilePath ?>;
        <?php endif ?>
    <?php endif ?>
<?php endif ?>

<?php if (!empty($VAR->domain->physicalHosting->proxySettings['nginxClientMaxBodySize'])): ?>
    client_max_body_size <?php echo $VAR->domain->physicalHosting->proxySettings['nginxClientMaxBodySize'] ?>;
<?php endif ?>

<?php if ($VAR->domain->physicalHosting->scriptTimeout): ?>
    proxy_read_timeout <?php echo min($VAR->domain->physicalHosting->scriptTimeout, 2147483) ?>;
<?php endif ?>

<?php if (!$OPT['ssl'] && $VAR->domain->physicalHosting->ssl && $VAR->domain->physicalHosting->sslRedirect): ?>

<?php echo $VAR->includeTemplate('domain/service/nginxSeoSafeRedirects.php', array('ssl' => true)) ?>

        return 301 https://$host$request_uri;
    }
    <?php return ?>
<?php endif ?>

    root "<?php echo $OPT['ssl'] ? $VAR->domain->physicalHosting->httpsDir : $VAR->domain->physicalHosting->httpDir ?>";
    access_log "<?php echo $VAR->domain->physicalHosting->logsDir . '/' . ($OPT['ssl'] ? 'proxy_access_ssl_log' : 'proxy_access_log') ?>";
    error_log "<?php echo $VAR->domain->physicalHosting->logsDir . '/proxy_error_log' ?>";

<?php if ($OPT['default']): ?>
    <?php echo $VAR->includeTemplate('service/nginxSitePreview.php') ?>
<?php endif ?>

<?php echo $VAR->domain->physicalHosting->proxySettings['allowDeny'] ?>

<?=$VAR->includeTemplate('domain/service/nginxSeoSafeRedirects.php', $OPT)?>

<?=$VAR->includeTemplate('domain/service/nginxCache.php', $OPT)?>

<?php echo $VAR->domain->physicalHosting->nginxExtensionsConfigs ?>

<?php if (!$VAR->domain->physicalHosting->proxySettings['nginxProxyMode']): ?>
# deny access to hidden directory and files
location ~ /\.(?!well-known\/) {
    deny all;
}
<?php endif ?>

<?php if ($VAR->domain->physicalHosting->proxySettings['nginxProxyMode']): ?>
    location / {
    <?php echo $VAR->includeTemplate('domain/service/proxy.php', $OPT) ?>
    }

    <?php if (!$VAR->domain->physicalHosting->proxySettings['nginxTransparentMode'] && !$VAR->domain->physicalHosting->proxySettings['nginxServeStatic']): ?>
    location /internal-nginx-static-location/ {
        alias <?php echo $OPT['documentRoot'] ?>/;
        internal;
        <?php if ($VAR->domain->physicalHosting->expires && $VAR->domain->physicalHosting->expiresStaticOnly): ?>
        expires <?=$VAR->quote($VAR->domain->physicalHosting->expires)?>;
        <?php endif ?>
    }
    <?php endif ?>
<?php endif ?>

<?php if ($VAR->domain->active && $VAR->domain->physicalHosting->hasWebstat): ?>
    <?php echo $VAR->includeTemplate('domain/service/nginxWebstatDirectories.php', $OPT) ?>
<?php endif ?>

<?php if ($VAR->domain->active && !$VAR->domain->physicalHosting->proxySettings['nginxProxyMode']): ?>
    <?php echo $VAR->includeTemplate('domain/service/nginxProtectedDirectories.php', $OPT) ?>
<?php else: ?>
    <?php echo $VAR->includeTemplate('domain/service/nginxProtectedDirectoriesProxy.php', $OPT) ?>
<?php endif ?>

<?php if ($VAR->domain->active && $VAR->domain->physicalHosting->proxySettings['fileSharingPrefix']
    && $VAR->domain->physicalHosting->proxySettings['nginxProxyMode']): ?>
    location ~ "^/<?php echo $VAR->domain->physicalHosting->proxySettings['fileSharingPrefix'] ?>/" {
        <?=$VAR->includeTemplate('domain/service/proxy.php', $OPT + ['nginxCacheEnabled' => false])?>
    }
<?php endif ?>

<?php if ($VAR->domain->active && $VAR->domain->physicalHosting->proxySettings['nginxServeStatic']): ?>
    location @fallback {
        <?php if ($VAR->domain->physicalHosting->proxySettings['nginxProxyMode']): ?>
            <?php echo $VAR->includeTemplate('domain/service/proxy.php', $OPT) ?>
        <?php else: ?>
        return 404;
        <?php endif ?>
    }

    location ~ ^/(.*\.(<?php echo $VAR->domain->physicalHosting->proxySettings['nginxStaticExtensions'] ?>))$ {
        try_files $uri @fallback;
        <?php if ($VAR->domain->physicalHosting->expires && $VAR->domain->physicalHosting->expiresStaticOnly): ?>
        expires <?=$VAR->quote($VAR->domain->physicalHosting->expires)?>;
        <?php endif ?>
    }
<?php endif ?>

<?php if ($VAR->domain->active && $VAR->domain->physicalHosting->php && $VAR->domain->physicalHosting->proxySettings['nginxServePhp']): ?>
    location ~ ^/~(.+?)(/.*?\.php)(/.*)?$ {
        alias <?php echo $VAR->domain->physicalHosting->webUsersDir ?>/$1/$2;
        <?php echo $VAR->includeTemplate('domain/service/fpm.php', $OPT) ?>
    }

        <?php if ($VAR->domain->physicalHosting->proxySettings['nginxProxyMode']): ?>
    location ~ ^/~(.+?)(/.*)?$ {
            <?php echo $VAR->includeTemplate('domain/service/proxy.php', $OPT) ?>
    }
        <?php endif ?>

    location ~ \.php(/.*)?$ {
        <?php echo $VAR->includeTemplate('domain/service/fpm.php', $OPT) ?>
    }

    <?php if (!$VAR->domain->physicalHosting->proxySettings['nginxProxyMode']): ?>
    # Nginx rewrite rules for WordPress

    location  / {
    index index.php index.cgi index.pl index.html index.xhtml index.htm index.shtml;
    try_files $uri $uri/ /index.php$is_args$args;
    }
    # fastcgi_cache purge support

    location ~ /purge(/.*) {
    fastcgi_cache_purge WORDPRESS "$scheme$request_method$host$1";
    access_log off;
    }

    # Cache static files
    location ~* \.(ogg|ogv|svg|svgz|eot|otf|woff|woff2|ttf|m4a|mp4|ttf|rss|atom|gif|cur|heic|tiff|ico|zip|webm|mp3|aac|tgz|gz|rar|bz2|doc|xls|exe|ppt|tar|mid|midi|wav|bmp|rtf|swf)$ {
    add_header "Access-Control-Allow-Origin" "*";
    access_log off;
    log_not_found off;
    expires max;
    }
    # Cache css & js files
    location ~* \.(?:css(\.map)?|js(\.map)?)$ {
    add_header "Access-Control-Allow-Origin" "*";
    access_log off;
    log_not_found off;
    expires 30d;
    }

    # webp rewrite rules for jpg and png images
    # try to load alternative image.png.webp first, then try image.png
    location /wp-content/uploads {
    location ~ \.(png|jpe?g)$ {
        add_header Vary "Accept-Encoding";
        add_header "Access-Control-Allow-Origin" "*";
        add_header Cache-Control "public, no-transform";
        access_log off;
        log_not_found off;
        expires max;
        try_files $uri$webp_suffix $uri =404;
    }
}

    location = /robots.txt {
    # Some WordPress plugin gererate robots.txt file
    # Refer #340 issue
    try_files $uri $uri/ /index.php?$args;
    access_log off;
    log_not_found off;
}

    <?php endif ?>









<?php endif ?>

<?php if ($VAR->domain->physicalHosting->restrictFollowSymLinks): ?>
    disable_symlinks if_not_owner from=$document_root;
<?php endif ?>

<?php if ($VAR->domain->physicalHosting->expires && !$VAR->domain->physicalHosting->expiresStaticOnly): ?>
    expires <?=$VAR->quote($VAR->domain->physicalHosting->expires)?>;
<?php endif ?>

<?php foreach ((array)$VAR->domain->physicalHosting->headers as list($name, $value)): ?>
    add_header <?=$VAR->quote([$name, $value])?>;
<?php endforeach ?>
    add_header X-Powered-By PleskLin;

<?php if (is_file($VAR->domain->physicalHosting->customNginxConfigFile)): ?>
    include "<?php echo $VAR->domain->physicalHosting->customNginxConfigFile ?>";
<?php endif ?>
}
