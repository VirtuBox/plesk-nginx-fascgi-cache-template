<?php
/**
 * @var Template_VariableAccessor $VAR
 * @var array $OPT
 */
?>
<?php if ($VAR->domain->physicalHosting->proxySettings['nginxCacheEnabled']): ?>
    fastcgi_cache_key <?=$VAR->quote($VAR->domain->physicalHosting->proxySettings['nginxCacheKey'])?>;

    fastcgi_no_cache $skip_cache;
    fastcgi_cache_bypass $skip_cache;

    fastcgi_cache <?="{$VAR->domain->asciiName}_fastcgi"?>;
    fastcgi_cache_valid <?=$VAR->quote($VAR->domain->physicalHosting->proxySettings['nginxCacheTimeout'])?>;
    fastcgi_cache_use_stale <?=join(' ', array_merge(
        $VAR->domain->physicalHosting->proxySettings['nginxCacheUseStale5xx'] ? ['http_500', 'http_503'] : [],
        $VAR->domain->physicalHosting->proxySettings['nginxCacheUseStale4xx'] ? ['http_403', 'http_404'] : [],
        $VAR->domain->physicalHosting->proxySettings['nginxCacheUseStaleUpdating'] ? ['updating'] : []
    ) ?: ['off'])?>;
    <?php if ($VAR->domain->physicalHosting->proxySettings['nginxCacheUseStaleUpdating']): ?>
        fastcgi_cache_background_update on;
    <?php endif ?>
<?php endif ?>
