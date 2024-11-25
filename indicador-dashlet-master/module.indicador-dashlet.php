<?php
//
// iTop module definition file
//

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'indicador-dashlet/1.0.0',
	array(
		// Identification
		//
		'label' => 'Dashlet de Indicador',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			
		),
		'mandatory' => false,
		'visible' => true,

		// Components
		//
		'datamodel' => array(
			'model.indicador-dashlet.php',
			'dashletindicador.class.inc.php'
		),
		'webservice' => array(
			
		),
		'data.struct' => array(
			// add your 'structure' definition XML files here,
		),
		'data.sample' => array(
			// add your sample data XML files here,
		),
		
		// Documentation
		//
		'doc.manual_setup' => '', // hyperlink to manual setup documentation, if any
		'doc.more_information' => '', // hyperlink to more information, if any 

		// Default settings
		//
		'settings' => array(
			// Module specific settings go here, if any
		),
	)
);

if (file_exists(APPROOT . 'extensions/indicador-dashlet-master/asset/css/dashlet-indicador.css')) {
    $sCSSFile = utils::GetAbsoluteUrlModulesRoot() . 'indicador-dashlet-master/asset/css/dashlet-indicador.css';
    $oPage->add_linked_stylesheet($sCSSFile);
}

?>
