<?php
/**
 * @var Template_VariableAccessor $VAR
 * @var array $OPT
 */
?>
<?php if ($VAR->domain->physicalHosting->proxySettings['nginxCacheEnabled']): ?>
    add_header X-Cache-Status $upstream_cache_status;
    set $no_cache "";

    <?php if (!empty($VAR->domain->physicalHosting->proxySettings['nginxCacheBypassLocations'])): ?>
        if ($request_uri ~* <?=$VAR->quote(join('|', $VAR->domain->physicalHosting->proxySettings['nginxCacheBypassLocations']))?>) {
            set $no_cache 1;
        }
    <?php endif ?>

    set $cache_cookie $http_cookie;
    <?php foreach ($VAR->domain->physicalHosting->proxySettings['nginxCacheCookies'] as $cookie): ?>
        if ($cache_cookie ~ "(.*)(?:^|;)\s*<?=preg_quote($cookie)?>=[^;]+(?:$|;)(.*)") {
            set $no_cache 1;
        }
    <?php endforeach ?>
<?php endif ?>
