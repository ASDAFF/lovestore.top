<?
IncludeModuleLangFile(__FILE__);
Class elcorso_warehousecsvimport extends CModule
{
	const MODULE_ID = 'elcorso.warehousecsvimport';
	var $MODULE_ID = 'elcorso.warehousecsvimport'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("elcorso.warehousecsvimport_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("elcorso.warehousecsvimport_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("elcorso.warehousecsvimport_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("elcorso.warehousecsvimport_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		
		return true;
	}

	function InstallEvents()
	{
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CWarehouseCSVImport', 'OnBuildGlobalMenuHandler');
		return true;
	}

	function UnInstallEvents()
	{
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CWarehouseCSVImport', 'OnBuildGlobalMenuHandler');
		return true;
	}

	function InstallFiles($arParams = array())
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
				}
				closedir($dir);
			}
		}
		
		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$item);
				}
				closedir($dir);
			}
		}
		
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		$this->InstallEvents();
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallEvents();
		$this->UnInstallFiles();
	}
}
?>
