<?php
/**
 * Main dashboard template
 */
?><div id="jet-menu-options-page">
	<div class="jet-menu-options-page">
		<h1 class="cs-vui-title"><?php _e( 'JetMenu Options', 'jet-menu' ); ?></h1>

		<div class="jet-menu-options-page__header cx-vui-panel">
			<div class="cx-vui-component cx-vui-component--equalwidth">
				<cx-vui-button
					button-style="accent-border"
					size="mini"
					@click="openPresetManager"
				>
					<span slot="label"><?php _e( 'Preset Manager', 'jet-menu' ); ?></span>
				</cx-vui-button>

				<cx-vui-button
					button-style="accent-border"
					size="mini"
					:url="pageConfig.exportUrl"
					tag-name="a"
				>
					<span slot="label"><?php _e( 'Export Options', 'jet-menu' ); ?></span>
				</cx-vui-button>

				<cx-vui-button
					button-style="accent-border"
					size="mini"
					@click="importVisible=true"
				>
					<span slot="label"><?php _e( 'Import Options', 'jet-menu' ); ?></span>
				</cx-vui-button>

				<cx-vui-button
					button-style="default-border"
					size="mini"
					@click="resetCheckPopup=true"
				>
					<span slot="label"><?php _e( 'Reset Options', 'jet-menu' ); ?></span>
				</cx-vui-button>

			</div>
		</div>

		<div class="cx-vui-panel">
			<cx-vui-tabs
				:in-panel="false"
				:value="activeTab"
				layout="vertical"
				@input="tabSwitch"
			>
				<?php do_action( 'jet-menu/options-page-template/tabs-start' ); ?>

				<!-- Desktop Menu Styles -->
				<cx-vui-tabs-panel
					name="desktop-menu-options"
					label="<?php _e( 'Desktop Menu', 'jet-menu' ); ?>"
					key="desktop-menu-options"
				><?php

					include jet_menu()->plugin_path( 'templates/admin/options-page-desktop.php' );

				?></cx-vui-tabs-panel>

				<cx-vui-tabs-panel
					name="desktop-menu-items-options"
					label="<?php _e( 'Desktop Menu Items', 'jet-menu' ); ?>"
					key="desktop-menu-items-options"
				><?php

					include jet_menu()->plugin_path( 'templates/admin/options-page-items.php' );

				?></cx-vui-tabs-panel>

				<cx-vui-tabs-panel
					name="desktop-advanced-options"
					label="<?php _e( 'Desktop Advanced', 'jet-menu' ); ?>"
					key="desktop-advanced-options"
				><?php

					include jet_menu()->plugin_path( 'templates/admin/options-page-advanced.php' );

				?></cx-vui-tabs-panel>

				<cx-vui-tabs-panel
					name="mobile-menu-options"
					label="<?php _e( 'Mobile Menu', 'jet-menu' ); ?>"
					key="mobile-menu-options"
				><?php

					include jet_menu()->plugin_path( 'templates/admin/options-page-mobile.php' );

				?></cx-vui-tabs-panel>

				<?php do_action( 'jet-menu/options-page-template/tabs-end' ); ?>
			</cx-vui-tabs>
		</div><?php

		include jet_menu()->plugin_path( 'templates/admin/options-page-popups.php' );

	?></div>
</div>
