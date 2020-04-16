
<?php if(xt_woo_quick_view()->access_manager()->can_use_premium_code__premium_only() && xt_wooqv_option('modal_nav_enabled', false)): ?>
    <div class="xt_wooqv-nav">
        <a class="xt_wooqv-prev"><span class="<?php echo xt_wooqv_nav_icon_class();?>"></span></a>
        <a class="xt_wooqv-next"><span class="<?php echo xt_wooqv_nav_icon_class();?>"></span></a>
    </div>
<?php endif; ?>
