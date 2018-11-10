<?php
/**
 * @var Template_VariableAccessor $VAR
 * @var array $OPT
 */
?>
        fastcgi_split_path_info ^((?U).+\.php)(/?.+)$;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_pass "<?php echo $VAR->domain->physicalHosting->fpmSocket ?>";
        include /etc/nginx/fastcgi.conf;
        
        fastcgi_cache_bypass $skip_cache;
        fastcgi_no_cache $skip_cache;

        fastcgi_cache WORDPRESS;
        fastcgi_cache_valid 200 60m;

<?php if ($OPT['nginxCacheEnabled'] ?? true): ?>
    <?=$VAR->includeTemplate('domain/service/nginxCacheFastCgi.php', $OPT)?>
<?php endif ?>
