<?
IncludeModuleLangFile(__FILE__);
$MODULE_ID = "elcorso.warehousecsvimport";
if($APPLICATION->GetGroupRight("catalog")!="D")
{
	//CModule::IncludeModule('elcorso.warehousecsvimport');
	global $adminMenu;
	$aMenu = array(
			"parent_menu" => "global_menu_store",
			"text" => GetMessage("ELCORSO_WAREHOUSECSVIMPORT_IMPORT_OSTATKOV_NA_S"),
			"url" => "warehouse_import_admin.php?lang=".LANGUAGE_ID."&path=/",
			"more_url" => array(
			 "warehouse_import_admin.php?bucket=".$arBucket["ID"],
			),
			"title" => GetMessage("ELCORSO_WAREHOUSECSVIMPORT_IMPORT_OSTATKOV_NA_S"),
			"page_icon" => "trade_catalog_menu_icon",
			///"items_id" => "menu_catalog_store",
			"module_id" => $MODULE_ID,
			"sort" => 590,
			"items" => array()
		);
	
	return $aMenu;
}
return false;
?>
