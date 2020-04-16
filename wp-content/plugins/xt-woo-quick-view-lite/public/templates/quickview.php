<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the quick view modal.
 *
 * This template can be overridden by copying it to yourtheme/woo-quick-view/quickview.php.
 *
 * HOWEVER, on occasion we will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @link       http://xplodedthemes.com
 * @since      1.4.3
 *
 * @package    XT_Woo_Quick_View
 * @subpackage XT_Woo_Quick_View/public/templates
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$fullscreen = xt_wooqv_modal_type_is('fullscreen');
?>

<?php if(!$fullscreen):?>
<div class="xt_wooqv-overlay"></div>
<?php endif; ?>

<?php xt_woo_quick_view()->get_template('parts/navigation'); ?>

<div id="xt_wooqv" class="<?php xt_wooqv_class(); ?>" <?php xt_wooqv_attributes();?>>
	
	<div class="xt_wooqv-product"></div>

</div>
