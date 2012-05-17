<?php
	/***************************************************************
	 * Copyright notice
	 *
	 * (c) 2012
	 * All rights reserved
	 *
	 * This script is part of the TYPO3 project. The TYPO3 project is
	 * free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * The GNU General Public License can be found at
	 * http://www.gnu.org/copyleft/gpl.html.
	 *
	 * This script is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * This copyright notice MUST APPEAR in all copies of the script!
	 ***************************************************************/

	/**
	 * Testcase for the Tx_Extensionmanager_Utility_List class in the TYPO3 Core.
	 *
	 * @package TYPO3
	 * @subpackage extensionmanager
	 */
class Tx_Extensionmanager_Repository_ConfigurationItemRepositoryTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_Extensionmanager_Domain_Repository_ConfigurationItemRepository
	 */
	public $configurationItemRepository;

	public function setUp() {
		$className = $this->getConfigurationItemRepositoryMock();
		$this->configurationItemRepository = new $className;

	}

	public function getConfigurationItemRepositoryMock() {
		$className = 'Tx_Extensionmanager_Repository_ConfigurationItemRepositoryMock';
		if (!class_exists($className, FALSE)) {
			eval(
				'class ' . $className . ' extends Tx_Extensionmanager_Domain_Repository_ConfigurationItemRepository {' .
				'  public function addMetaInformation(&$configuration) {' .
				'    return parent::addMetaInformation($configuration);' .
				'  }' .
				'  public function extractInformationForConfigFieldsOfTypeUser($configurationOption) {' .
				'    return parent::extractInformationForConfigFieldsOfTypeUser($configurationOption);' .
				'  }' .
				'  public function extractInformationForConfigFieldsOfTypeOptions($configurationOption) {' .
				'    return parent::extractInformationForConfigFieldsOfTypeOptions($configurationOption);' .
				'  }' .
				'  public function mergeWithExistingConfiguration(array $configuration, array $extension) {' .
				'    return parent::mergeWithExistingConfiguration($configuration, $extension);' .
				'  }' .
				'}'
			);
		}
		return $className;
	}

	/**
	 * @test
	 */
	public function addMetaInformationUnsetsOriginalConfigurationMetaKey() {
		$configuration = array(
			'__meta__' => 'metaInformation',
			'test123' => 'test123'
		);
		$this->configurationItemRepository->addMetaInformation($configuration);
		$this->assertEquals(array('test123' => 'test123'), $configuration);
	}

	/**
	 * @test
	 */
	public function addMetaInformationReturnsMetaInformation() {
		$configuration = array(
			'__meta__' => 'metaInformation',
			'test123' => 'test123'
		);
		$meta = $this->configurationItemRepository->addMetaInformation($configuration);
		$this->assertEquals('metaInformation', $meta);
	}

	public function extractInformationForConfigFieldsOfTypeUserAddsGenericAndTypeInformationDataProvider() {
		return array(
			array(
				array(
					'cat' => 'basic',
					'subcat_name' => 'enable',
					'subcat' => 'a/enable/z',
					'type' => 'user[EXT:saltedpasswords/classes/class.tx_saltedpasswords_emconfhelper.php:tx_saltedpasswords_emconfhelper->checkConfigurationFrontend]',
					'label' => 'Frontend configuration check',
					'name' => 'checkConfigurationFE',
					'value' => 0,
					'default_value' => 0,
					'comparisonGeneric' => 'EXT:saltedpasswords/classes/class.tx_saltedpasswords_emconfhelper.php:tx_saltedpasswords_emconfhelper->checkConfigurationFrontend'
				),
			),
			array(
				array(
					'cat' => 'basic',
					'subcat_name' => 'enable',
					'subcat' => 'a/enable/z',
					'type' => 'user[EXT:saltedpasswords/classes/class.tx_saltedpasswords_emconfhelper.php:tx_saltedpasswords_emconfhelper->checkConfigurationBackend]',
					'label' => 'Backend configuration check',
					'name' => 'checkConfigurationBE',
					'value' => 0,
					'default_value' => 0,
					'comparisonGeneric' => 'EXT:saltedpasswords/classes/class.tx_saltedpasswords_emconfhelper.php:tx_saltedpasswords_emconfhelper->checkConfigurationBackend'
				),
			),
			array(
				array(
					'cat' => 'basic',
					'subcat_name' => 'enable',
					'subcat' => 'a/enable/z',
					'type' => 'user[EXT:saltedpasswords/classes/class.tx_saltedpasswords_emconfhelper.php:tx_saltedpasswords_emconfhelper->buildHashMethodSelectorFE]',
					'label' => 'Hashing method for the frontend: Defines salted hashing method to use. Choose "Portable PHP password hashing" to stay compatible with other CMS (e.g. Drupal, Wordpress). Choose "MD5 salted hashing" to reuse TYPO3 passwords for OS level authentication (other servers could use TYPO3 passwords). Choose "Blowfish salted hashing" for advanced security to reuse passwords on OS level (Blowfish might not be supported on your system TODO).',
					'name' => 'FE.saltedPWHashingMethod',
					'value' => 'tx_saltedpasswords_salts_phpass',
					'default_value' => 'tx_saltedpasswords_salts_phpass',
					'comparisonGeneric' => 'EXT:saltedpasswords/classes/class.tx_saltedpasswords_emconfhelper.php:tx_saltedpasswords_emconfhelper->buildHashMethodSelectorFE'
				)
			)
		);
	}

	/**
	 * @test
	 * @dataProvider extractInformationForConfigFieldsOfTypeUserAddsGenericAndTypeInformationDataProvider
	 * @param $configurationOption
	 */
	public function extractInformationForConfigFieldsOfTypeUserAddsGenericAndTypeInformation($configurationOption) {
		$configurationOptionModified = $this->configurationItemRepository->extractInformationForConfigFieldsOfTypeUser($configurationOption);
		$this->assertEquals('user', $configurationOptionModified['type']);
		$this->assertEquals($configurationOption['comparisonGeneric'], $configurationOptionModified['generic']);
	}

	/**
	 * @test
	 */
	public function extractInformationForConfigFieldsOfTypeOptionsAddsGenericTypeAndLabelInformation() {
		$option = array (
			'cat' => 'basic',
			'subcat_name' => 'enable',
			'subcat' => 'a/enable/100z',
			'type' => 'options[Minimal (Most features disabled. Administrator needs to enable them using TypoScript. For advanced administrators only.),Typical (Most commonly used features are enabled. Select this option if you are unsure which one to use.),Demo (Show-off configuration. Includes pre-configured styles. Not for production environments.)]',
			'label' => 'Default configuration settings',
			'name' => 'defaultConfiguration',
			'value' => 'Typical (Most commonly used features are enabled. Select this option if you are unsure which one to use.)',
			'default_value' => 'Typical (Most commonly used features are enabled. Select this option if you are unsure which one to use.)',
			'genericComparisonValue' =>
				array (
					'Minimal (Most features disabled. Administrator needs to enable them using TypoScript. For advanced administrators only.)' => 'Minimal (Most features disabled. Administrator needs to enable them using TypoScript. For advanced administrators only.)',
					'Typical (Most commonly used features are enabled. Select this option if you are unsure which one to use.)' => 'Typical (Most commonly used features are enabled. Select this option if you are unsure which one to use.)',
					'Demo (Show-off configuration. Includes pre-configured styles. Not for production environments.)' => 'Demo (Show-off configuration. Includes pre-configured styles. Not for production environments.)',
				),
			'typeComparisonValue' => 'options'
		);

		$optionModified = $this->configurationItemRepository->extractInformationForConfigFieldsOfTypeOptions($option);
		$this->assertArrayHasKey('generic', $optionModified);
		$this->assertArrayHasKey('type', $optionModified);
		$this->assertArrayHasKey('label', $optionModified);
		$this->assertEquals($option['genericComparisonValue'], $optionModified['generic']);
		$this->assertEquals($option['typeComparisonValue'], $optionModified['type']);
	}

	/**
	 * @test
	 */
	public function mergeWithExistingConfigurationOverwritesDefaultKeysWithCurrent() {
		$backupExtConf = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'];
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['testextensionkey'] = serialize(array(
			'FE.' => array(
				'enabled' => '1',
				'saltedPWHashingMethod' => 'tx_saltedpasswords_salts_sha1',
			),
			'CLI.' => array(
				'enabled' => '0',
			)
		));
		$defaultConfiguration = array(
			'FE.' => array(
				'enabled' => '0',
				'saltedPWHashingMethod' => 'tx_saltedpasswords_salts_md5',
			),
			'BE.' => array(
				'enabled' => '1',
				'saltedPWHashingMethod' => 'tx_saltedpasswords_salts_md5',
			),
		);
		$expectedResult = array(
			'FE.' => array(
				'enabled' => '1',
				'saltedPWHashingMethod' => 'tx_saltedpasswords_salts_sha1',
			),
			'BE.' => array(
				'enabled' => '1',
				'saltedPWHashingMethod' => 'tx_saltedpasswords_salts_md5',
			),
			'CLI.' => array(
				'enabled' => '0',
			)
		);
		$result = $this->configurationItemRepository->mergeWithExistingConfiguration($defaultConfiguration, array('key' => 'testextensionkey'));
		$this->assertEquals($expectedResult, $result);
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'] = $backupExtConf;
	}

	public function createArrayFromConstantsCreatesAnArrayWithMetaInformationDataProvider() {
		return array(
			'demo data from salted passwords' => array(
				'
# cat=basic/enable; type=user[EXT:saltedpasswords/classes/class.tx_saltedpasswords_emconfhelper.php:tx_saltedpasswords_emconfhelper->checkConfigurationFrontend]; label=Frontend configuration check
checkConfigurationFE=0

# cat=advancedBackend; type=boolean; label=Force salted passwords: Enforce usage of SaltedPasswords. Old MD5 hashed passwords will stop working.
BE.forceSalted = 0

TSConstantEditor.advancedbackend {
  description = <span style="background:red; padding:1px 2px; color:#fff; font-weight:bold;">1</span> Install tool has hardcoded md5 hashing, enabling this setting will prevent use of a install-tool-created BE user.<br />Currently same is for changin password with user setup module unless you use pending patch!
			1=BE.forceSalted
}'
				,
				array (
					'checkConfigurationFE' =>
					array (
						'cat' => 'basic',
						'subcat_name' => 'enable',
						'subcat' => 'a/enable/z',
						'type' => 'user[EXT:saltedpasswords/classes/class.tx_saltedpasswords_emconfhelper.php:tx_saltedpasswords_emconfhelper->checkConfigurationFrontend]',
						'label' => 'Frontend configuration check',
						'name' => 'checkConfigurationFE',
						'value' => '0',
						'default_value' => '0',
					),
					'BE.forceSalted' =>
					array (
						'cat' => 'advancedbackend',
						'subcat' => 'x/z',
						'type' => 'boolean',
						'label' => 'Force salted passwords: Enforce usage of SaltedPasswords. Old MD5 hashed passwords will stop working.',
						'name' => 'BE.forceSalted',
						'value' => '0',
						'default_value' => '0',
					),
				),
				array (
					'advancedbackend.' =>
					array (
						'description' => '<span style="background:red; padding:1px 2px; color:#fff; font-weight:bold;">1</span> Install tool has hardcoded md5 hashing, enabling this setting will prevent use of a install-tool-created BE user.<br />Currently same is for changin password with user setup module unless you use pending patch!',
						1 => 'BE.forceSalted',
					),
				),
				array (
					'checkConfigurationFE' =>
					array (
						'cat' => 'basic',
						'subcat_name' => 'enable',
						'subcat' => 'a/enable/z',
						'type' => 'user[EXT:saltedpasswords/classes/class.tx_saltedpasswords_emconfhelper.php:tx_saltedpasswords_emconfhelper->checkConfigurationFrontend]',
						'label' => 'Frontend configuration check',
						'name' => 'checkConfigurationFE',
						'value' => '0',
						'default_value' => '0',
					),
					'BE.forceSalted' =>
					array (
						'cat' => 'advancedbackend',
						'subcat' => 'x/z',
						'type' => 'boolean',
						'label' => 'Force salted passwords: Enforce usage of SaltedPasswords. Old MD5 hashed passwords will stop working.',
						'name' => 'BE.forceSalted',
						'value' => '0',
						'default_value' => '0',
						'highlight' => 1,
					),
					'__meta__' =>
					array (
						'advancedbackend' =>
						array (
							'highlightText' => '<span style="background:red; padding:1px 2px; color:#fff; font-weight:bold;">1</span> Install tool has hardcoded md5 hashing, enabling this setting will prevent use of a install-tool-created BE user.<br />Currently same is for changin password with user setup module unless you use pending patch!',
						),
					),
				)
			)
		);
	}

	/**
	 * @test
	 * @dataProvider createArrayFromConstantsCreatesAnArrayWithMetaInformationDataProvider
	 */
	public function createArrayFromConstantsCreatesAnArrayWithMetaInformation($raw, $constants, $setupTsConstantEditor, $expected) {
		$tsStyleConfig = $this->getMock('t3lib_tsStyleConfig');

		$configurationItemRepositoryMock = $this->getMock(
			'Tx_Extensionmanager_Domain_Repository_ConfigurationItemRepository',
			array('getT3libTsStyleConfig')
		);
		$configurationItemRepositoryMock->expects($this->once())->method('getT3libTsStyleConfig')
			->will($this->returnValue($tsStyleConfig));

		$tsStyleConfig->expects($this->once())
			->method('ext_initTSstyleConfig')
			->with($raw, $this->anything(), $this->anything(), $this->anything())
			->will($this->returnValue($constants));

		$tsStyleConfig->setup['constants']['TSConstantEditor.'] = $setupTsConstantEditor;
		$constantsResult = $configurationItemRepositoryMock->createArrayFromConstants($raw, array());
		$this->assertEquals($expected, $constantsResult);
	}


}