<?
IncludeModuleLangFile(__FILE__);
class CWarehouseCSVImport
{
	function OnBuildGlobalMenuHandler(&$aGlobalMenu, &$aModuleMenu)
	{
		if($GLOBALS['APPLICATION']->GetGroupRight("main") < "R")
			return;
		
		foreach($aModuleMenu as $key => &$aMenu)
		{
			if($aMenu["items_id"] == "menu_catalog_store")
			{
				 $aMenu["items"][] = array(
					"text" => GetMessage("ELCORSO_WAREHOUSECSVIMPORT_IMPORT_OSTATKOV_NA_S"),
					"url" => "warehouse_import_admin.php?lang=".LANGUAGE_ID."&path=/",
					"more_url" => array(
					 "warehouse_import_admin.php?bucket=".$arBucket["ID"],
					),
					"title" => "",
					"page_icon" => "clouds_page_icon",
					"items_id" => "menu_catalog_store_import",
					"module_id" => "clouds",
					"items" => array()
				);
			}	
		}	
	}
}
?>