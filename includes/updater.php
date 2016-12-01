<?php if ( ! defined( 'WPINC' ) ) exit;

/**
 * Loading and configuring the plugin updater
 */





// Load files

	if ( ! class_exists( 'Smashing_Updater' ) ){
		require_once WMTEMPLATES_PATH . 'includes/classes/class-smashing-updater.php';
	}



// Configure and run the updater

	$updater = new Smashing_Updater( WMTEMPLATES_FILE );

	$updater->set_username( 'webmandesign' );
	$updater->set_repository( 'webman-templates' );

	$updater->initialize();
