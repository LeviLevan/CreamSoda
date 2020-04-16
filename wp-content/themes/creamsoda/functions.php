<?php
/**
 * Child functions and definitions.
 */
add_filter( 'kava-theme/assets-depends/styles', 'creamsoda_styles_depends' );
add_action( 'jet-theme-core/register-config', 'creamsoda_core_config' );
add_action( 'init', 'creamsoda_plugins_wizard_config', 9 );
add_action( 'tgmpa_register', 'creamsoda_register_required_plugins' );
/**
 * Enqueue styles.
 */
function creamsoda_styles_depends( $deps ) {
	$parent_handle = 'kava-parent-theme-style';
	wp_register_style(
		$parent_handle,
		get_template_directory_uri() . '/style.css',
		array(),
		kava_theme()->version()
	);
	$deps[] = $parent_handle;
	return $deps;
}
/**
 * Register JetThemeCore config
 *
 * @param  [type] $manager [description]
 * @return [type]          [description]
 */
function creamsoda_core_config( $manager ) {
	$manager->register_config(
		array(
			'dashboard_page_name' => esc_html__( 'Cream Soda', 'creamsoda' ),
			'library_button'      => false,
			'menu_icon'           => 'dashicons-admin-generic',
			'api'                 => array( 'enabled' => false ),
			'guide'               => array(
				'title'   => __( 'Learn More About Your Theme', 'jet-theme-core' ),
				'links'   => array(
					'documentation' => array(
						'label'  => __('Check documentation', 'jet-theme-core'),
						'type'   => 'primary',
						'target' => '_blank',
						'icon'   => 'dashicons-welcome-learn-more',
						'desc'   => __( 'Get more info from documentation', 'jet-theme-core' ),
						'url'    => 'http://documentation.zemez.io/wordpress/index.php?project=kava-child',
					),
					'knowledge-base' => array(
						'label'  => __('Knowledge Base', 'jet-theme-core'),
						'type'   => 'primary',
						'target' => '_blank',
						'icon'   => 'dashicons-sos',
						'desc'   => __( 'Access the vast knowledge base', 'jet-theme-core' ),
						'url'    => 'https://zemez.io/wordpress/support/knowledge-base',
					),
				),
			)
		)
	);
}

/**
 * Register Jet Plugins Wizards config
 * @return [type] [description]
 */
function creamsoda_plugins_wizard_config() {
	if ( ! is_admin() ) {
		return;
	}
	if ( ! function_exists( 'jet_plugins_wizard_register_config' ) ) {
		return;
	}
	jet_plugins_wizard_register_config( array(
		'license' => array(
			'enabled' => false,
		),
		'plugins' => array(	
			'elementor' => array(
				'name'   => esc_html__( 'Elementor', 'creamsoda' ),
				'access' => 'skins',
				),
			'kava-extra' => array(
				'name'   => esc_html__( 'Kava Extra', 'creamsoda' ),
				'source' => 'remote',
				'path'   => 'https://github.com/ZemezLab/kava-extra/archive/master.zip',
				'access' => 'base',
				),
			'jet-elements' => array(
				'name'   => esc_html__( 'Jet Elements addon For Elementor', 'creamsoda' ),
				'source' => 'local',
				'path'   => get_stylesheet_directory() . '/plugins/jet-elements.zip',
				'access' => 'skins',
				),
			'jet-compare-wishlist' => array(
				'name'   => esc_html__( 'Jet Compare Wishlist', 'creamsoda' ),
				'source' => 'local',
				'path'   => get_stylesheet_directory() . '/plugins/jet-compare-wishlist.zip',
				'access' => 'skins',
				),
			'jet-blocks' => array(
				'name'   => esc_html__( 'Jet Blocks', 'creamsoda' ),
				'source' => 'local',
				'path'   => get_stylesheet_directory() . '/plugins/jet-blocks.zip',
				'access' => 'skins',
				),
			'jet-tabs' => array(
				'name'   => esc_html__( 'Jet Tabs', 'creamsoda' ),
				'source' => 'local',
				'path'   => get_stylesheet_directory() . '/plugins/jet-tabs.zip',
				'access' => 'skins',
				),
			'jet-tricks' => array(
				'name'   => esc_html__( 'Jet Tricks', 'creamsoda' ),
				'source' => 'local',
				'path'   => get_stylesheet_directory() . '/plugins/jet-tricks.zip',
				'access' => 'skins',
				),
			'jet-theme-core' => array(
				'name'   => esc_html__( 'Jet Theme Core', 'creamsoda' ),
				'source' => 'local',
				'path'   => get_stylesheet_directory() . '/plugins/jet-theme-core.zip',
				'access' => 'skins',
				),
			'jet-woo-builder' => array(
				'name'   => esc_html__( 'Jet Woo Builder', 'creamsoda' ),
				'source' => 'local',
				'path'   => get_stylesheet_directory() . '/plugins/jet-woo-builder.zip',
				'access' => 'skins',
				),
			'jet-smart-filters' => array(
				'name'   => esc_html__( 'Jet Smart Filters', 'creamsoda' ),
				'source' => 'local',
				'path'   => get_stylesheet_directory() . '/plugins/jet-smart-filters.zip',
				'access' => 'skins',
				),
			'jet-menu' => array(
				'name'   => esc_html__( 'Jet Menu', 'creamsoda' ),
				'source' => 'local',
				'path'   => get_stylesheet_directory() . '/plugins/jet-menu.zip',
				'access' => 'skins',
				),
			'jet-popup' => array(
				'name'   => esc_html__( 'Jet Popup', 'creamsoda' ),
				'source' => 'local',
				'path'   => get_stylesheet_directory() . '/plugins/jet-popup.zip',
				'access' => 'skins',
				),
			'jet-woo-product-gallery' => array(
				'name'   => esc_html__( 'Jet Woo Product Gallery', 'creamsoda' ),
				'source' => 'local',
				'path'   => get_stylesheet_directory() . '/plugins/jet-woo-product-gallery.zip',
				'access' => 'skins',
				),
			'woocommerce' => array(
				'name'   => esc_html__( 'Woocommerce', 'creamsoda' ),
				'access' => 'base',
				),
			'contact-form-7' => array(
				'name'   => esc_html__( 'Contact Form 7', 'creamsoda' ),
				'access' => 'skins',
				),
			),
		'skins'   => array(
			'advanced' => array(
				'default' => array(
					'full'  => array(
						'elementor',
						'jet-compare-wishlist',
						'jet-blocks',
						'jet-elements',
						'jet-tabs',
						'jet-theme-core',
						'jet-tricks',
						'contact-form-7',
						'woocommerce',
						'jet-woo-builder',
						'jet-menu',
						'jet-smart-filters',
						'jet-popup',
						'jet-woo-product-gallery',
						),
					'lite'  => false,
					'demo'  => '',
					'thumb' => get_stylesheet_directory_uri() . '/screenshot.png',
					'name'  => esc_html__( 'Cream Soda', 'creamsoda' ),
					),
				),
			),
		'texts'   => array(
			'theme-name' => esc_html__( 'Cream Soda', 'creamsoda' ),
		)
	) );
}

/**
 * Register Class Tgm Plugin Activation
 */
require_once('inc/classes/class-tgm-plugin-activation.php');
/**
 * Setup Jet Plugins Wizard
 */
function creamsoda_register_required_plugins() {
	$plugins = array(
		array(
			'name'         => esc_html__( 'Jet Plugin Wizard', 'creamsoda' ),
			'slug'         => 'jet-plugins-wizard',
			'source'       => 'https://github.com/ZemezLab/jet-plugins-wizard/archive/master.zip',
			'external_url' => 'https://github.com/ZemezLab/jet-plugins-wizard',
		),
	);
	$config = array(
		'id'           => 'creamsoda',
		'default_path' => '',
		'menu'         => 'tgmpa-install-plugins',
		'has_notices'  => true,
		'dismissable'  => true,
		'dismiss_msg'  => '',
		'is_automatic' => true,
		'message'      => '',
	);
	tgmpa( $plugins, $config );
}

function woo_remove_specific_country( $country ) 
{
	unset($country["AF"]); unset($country["AX"]); unset($country["AL"]); unset($country["DZ"]); unset($country["AS"]);
	unset($country["AD"]); unset($country["AO"]); unset($country["AI"]); unset($country["AQ"]); unset($country["AG"]);
	unset($country["AR"]); unset($country["AM"]); unset($country["AW"]); unset($country["AU"]); unset($country["AT"]);
	unset($country["AZ"]); unset($country["BS"]); unset($country["BH"]); unset($country["BD"]); unset($country["BB"]);
	unset($country["BY"]); unset($country["BE"]); unset($country["PW"]); unset($country["BZ"]); unset($country["BJ"]);
	unset($country["BM"]); unset($country["BT"]); unset($country["BO"]); unset($country["BQ"]); unset($country["BA"]);
	unset($country["BW"]); unset($country["BV"]); unset($country["BR"]); unset($country["IO"]); unset($country["VG"]);
	unset($country["BN"]); unset($country["BG"]); unset($country["BF"]); unset($country["BI"]); unset($country["KH"]);
	unset($country["CM"]); unset($country["CA"]); unset($country["CV"]); unset($country["KY"]); unset($country["CF"]);
	unset($country["TD"]); unset($country["CL"]); unset($country["CN"]); unset($country["CX"]); unset($country["CC"]);
	unset($country["CO"]); unset($country["KM"]); unset($country["CG"]); unset($country["CD"]); unset($country["CK"]);
	unset($country["CR"]); unset($country["HR"]); unset($country["CU"]); unset($country["CW"]); unset($country["CY"]);
	unset($country["CZ"]); unset($country["DK"]); unset($country["DJ"]); unset($country["DM"]); unset($country["DO"]);
	unset($country["EC"]); unset($country["CZ"]); unset($country["EG"]); unset($country["SV"]); unset($country["GQ"]); 
	unset($country["ER"]); unset($country["EE"]); unset($country["ET"]); unset($country["FK"]); unset($country["FO"]);
	unset($country["FJ"]); unset($country["FI"]); unset($country["FR"]); unset($country["GF"]); unset($country["PF"]);
	unset($country["TF"]); unset($country["GA"]); unset($country["GM"]); unset($country["GE"]); unset($country["DE"]);
	unset($country["GH"]); unset($country["GI"]); unset($country["GR"]); unset($country["GL"]); unset($country["GD"]);
	unset($country["GP"]); unset($country["GU"]); unset($country["GT"]); unset($country["GG"]); unset($country["GN"]);
	unset($country["GW"]); unset($country["GY"]); unset($country["HT"]); unset($country["HM"]); unset($country["HN"]);
	unset($country["HK"]); unset($country["HU"]); unset($country["IS"]); unset($country["IN"]); unset($country["ID"]);
	unset($country["IR"]); unset($country["IQ"]); unset($country["IE"]); unset($country["IM"]); unset($country["IL"]);
	unset($country["IT"]); unset($country["CI"]); unset($country["JM"]); unset($country["JP"]); unset($country["JE"]);
	unset($country["JO"]); unset($country["KZ"]); unset($country["KE"]); unset($country["KI"]); unset($country["KW"]);
	unset($country["KG"]); unset($country["LA"]); unset($country["LV"]); unset($country["LB"]); unset($country["LS"]);
	unset($country["LR"]); unset($country["LY"]); unset($country["LI"]); unset($country["LT"]); unset($country["LU"]);
	unset($country["MO"]); unset($country["MK"]); unset($country["MG"]); unset($country["MW"]); unset($country["MY"]);
	unset($country["MV"]); unset($country["ML"]); unset($country["MT"]); unset($country["MH"]); unset($country["MQ"]);
	unset($country["MR"]); unset($country["MU"]); unset($country["YT"]); unset($country["MX"]); unset($country["FM"]);
	unset($country["MD"]); unset($country["MC"]); unset($country["MN"]); unset($country["ME"]); unset($country["MS"]);
	unset($country["MA"]); unset($country["MZ"]); unset($country["MM"]); unset($country["NA"]); unset($country["NR"]);
	unset($country["NP"]); unset($country["NL"]); unset($country["NC"]); unset($country["NZ"]); unset($country["NI"]);
	unset($country["NE"]); unset($country["NG"]); unset($country["NU"]); unset($country["NF"]); unset($country["MP"]);
	unset($country["KP"]); unset($country["NO"]); unset($country["OM"]); unset($country["PK"]); unset($country["PS"]);
	unset($country["PA"]); unset($country["PG"]); unset($country["PY"]); unset($country["PE"]); unset($country["PH"]);
	unset($country["PN"]); unset($country["PL"]); unset($country["PT"]); unset($country["PR"]); unset($country["QA"]);
	unset($country["RE"]); unset($country["RO"]); unset($country["RU"]); unset($country["RW"]); unset($country["BL"]);
	unset($country["SH"]); unset($country["IL"]); unset($country["KN"]); unset($country["LC"]); unset($country["MF"]);
	unset($country["SX"]); unset($country["PM"]); unset($country["VC"]); unset($country["SM"]); unset($country["ST"]);
	unset($country["SA"]); unset($country["SN"]); unset($country["RS"]); unset($country["SC"]); unset($country["SL"]);
	unset($country["SG"]); unset($country["SK"]); unset($country["SI"]); unset($country["SB"]); unset($country["SO"]);
	unset($country["ZA"]); unset($country["GS"]); unset($country["KR"]); unset($country["SS"]); unset($country["ES"]);
	unset($country["LK"]); unset($country["SD"]); unset($country["SR"]); unset($country["SJ"]); unset($country["SZ"]);
	unset($country["SE"]); unset($country["CH"]); unset($country["SY"]); unset($country["TW"]); unset($country["TJ"]);
	unset($country["TZ"]); unset($country["TH"]); unset($country["TL"]); unset($country["TG"]); unset($country["TK"]);
	unset($country["TO"]); unset($country["TT"]); unset($country["TN"]); unset($country["TR"]); unset($country["TM"]);
	unset($country["TC"]); unset($country["TV"]); unset($country["UG"]); unset($country["UA"]); unset($country["AE"]);
	unset($country["GB"]); unset($country["US"]); unset($country["UM"]); unset($country["VI"]); unset($country["UY"]);
	unset($country["UZ"]); unset($country["VU"]); unset($country["VA"]); unset($country["VE"]); unset($country["VN"]);
	unset($country["WF"]); unset($country["EH"]); unset($country["WS"]); unset($country["YE"]); unset($country["ZM"]);
	unset($country["ZW"]);

return $country; 
}
add_filter( 'woocommerce_countries', 'woo_remove_specific_country', 10, 1 );
	