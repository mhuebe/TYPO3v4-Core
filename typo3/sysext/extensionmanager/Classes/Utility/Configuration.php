<?php
	/***************************************************************
	 *  Copyright notice
	 *
	 *  (c) 2012 Susanne Moog <susanne.moog@typo3.org>
	 *  All rights reserved
	 *
	 *  This script is part of the TYPO3 project. The TYPO3 project is
	 *  free software; you can redistribute it and/or modify
	 *  it under the terms of the GNU General Public License as published by
	 *  the Free Software Foundation; either version 2 of the License, or
	 *  (at your option) any later version.
	 *
	 *  The GNU General Public License can be found at
	 *  http://www.gnu.org/copyleft/gpl.html.
	 *  A copy is found in the textfile GPL.txt and important notices to the license
	 *  from the author is found in LICENSE.txt distributed with these scripts.
	 *
	 *
	 *  This script is distributed in the hope that it will be useful,
	 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
	 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 *  GNU General Public License for more details.
	 *
	 *  This copyright notice MUST APPEAR in all copies of the script!
	 ***************************************************************/

	/**
	 * Utility for dealing with ext_emconf
	 *
	 * @author Susanne Moog <susanne.moog@typo3.org>
	 * @package Extension Manager
	 * @subpackage Utility
	 */
class Tx_Extensionmanager_Utility_Configuration implements t3lib_Singleton {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var Tx_Extensionmanager_Domain_Repository_ConfigurationItemRepository
	 */
	protected $configurationItemRepository;

	/**
	 * @param Tx_Extbase_Object_ObjectManager $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager){
		$this->objectManager = $objectManager;
	}

	/**
	 * @param Tx_Extensionmanager_Domain_Repository_ConfigurationItemRepository $configurationItemRepository
	 * @return void
	 */
	public function injectConfigurationItemRepository(Tx_Extensionmanager_Domain_Repository_ConfigurationItemRepository $configurationItemRepository){
		$this->configurationItemRepository = $configurationItemRepository;
	}

	public function saveDefaultConfiguration($extensionKey) {
		$currentConfiguration = $this->getCurrentConfiguration($extensionKey);
		$this->writeConfiguration($currentConfiguration, $extensionKey);
	}

	public function writeConfiguration(array $configuration, $extensionKey) {
		/** @var $installUtility Tx_Extensionmanager_Utility_Install */
		$installUtility = $this->objectManager->get('Tx_Extensionmanager_Utility_Install');
		$installUtility->writeExtensionTypoScriptStyleConfigurationToLocalconf($extensionKey, $configuration);
	}

	/**
	 * @param string $extensionKey
	 * @return array
	 */
	public function getCurrentConfiguration($extensionKey) {
		$extension = $GLOBALS['TYPO3_LOADED_EXT'][$extensionKey];
		$defaultConfig = $this->configurationItemRepository->createArrayFromConstants($configRaw = t3lib_div::getUrl(PATH_site . $extension['siteRelPath'] . '/ext_conf_template.txt'), $extension);
		$currentExtensionConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extension['key']]);
		$currentExtensionConfig = is_array($currentExtensionConfig) ? $currentExtensionConfig : array();
		$currentFullConfiguration = t3lib_div::array_merge_recursive_overrule($defaultConfig, $currentExtensionConfig);

		return $currentFullConfiguration;
	}
}
