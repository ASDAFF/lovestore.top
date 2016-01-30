<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

$RIGHT = $APPLICATION->GetGroupRight("catalog");
if($RIGHT=="D")
$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
global $DB, $USER;

if (!CModule::IncludeModule("catalog")){
		CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("ELCORSO_WAREHOUSECSVIMPORT_MODULQ_TORGOVYY_KATA"), "TYPE"=>"ERROR"));
		return false;
}
if (!CModule::IncludeModule("sale")){
		CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("ELCORSO_WAREHOUSECSVIMPORT_MODULQ_INTERNET_MAGA"), "TYPE"=>"ERROR"));
		return false;
}


$message = null;        // сообщение об ошибке
$bVarsFromForm = false;

$arSitesShop = array();
$arSitesTmp = array();
$rsSites = CSite::GetList($_REQUEST["by"] = "id", $_REQUEST["order"] = "asc", array("ACTIVE" => "Y"));
while ($arSite = $rsSites->GetNext())
{
	$site = COption::GetOptionString("sale", "SHOP_SITE_".$arSite["ID"], "");
	if ($arSite["ID"] == $site)
	{
		$arSitesShop[] = array("ID" => $arSite["ID"], "NAME" => $arSite["NAME"]);
	}
	$arSitesTmp[] = array("ID" => $arSite["ID"], "NAME" => $arSite["NAME"]);
}

$rsCount = count($arSitesShop);
if($rsCount <= 0)
{
	$arSitesShop = $arSitesTmp;
	$rsCount = count($arSitesShop);
}
$rsContractors = CCatalogContractor::GetList();
$arContractors = array();
while($arContractor = $rsContractors->Fetch())
{
	$arContractors[] = $arContractor;
}
$arMeasureCode = $arResult = array();
$arStores = array();
$rsStores = CCatalogStore::GetList(array(), array("ACTIVE" => "Y"));
while($arStore = $rsStores->GetNext())
{
	$arStores[$arStore["ID"]] = $arStore;
}

foreach($arStores as $key => $val)
{
	$selectedTo = ($value['STORE_TO'] == $val['ID']) ? " selected " : " ";
	$selectedFrom = ($value['STORE_FROM'] == $val['ID']) ? " selected " : " ";
	$store = ($val["TITLE"] != '') ? $val["TITLE"]." (".$val["ADDRESS"].")" : $val["ADDRESS"];
	$storesTo .= '<option'.$selectedTo.'value="'.$val['ID'].'">'.$store.'</option>';
	$storesFrom .= '<option'.$selectedFrom.'value="'.$val['ID'].'">'.$store.'</option>';
}

$store_to_select = '<select style="max-width:300px; width:300px;" name="STORE_TO" id="CAT_DOC_STORE_TO">'.$storesTo.'</select>';

if(
$REQUEST_METHOD == "POST" // проверка метода вызова страницы
&&
($_REQUEST["IMPORT"]!="") // проверка нажатия кнопок "Сохранить" и "Применить"

)
{
    CModule::IncludeModule("iblock");

    if(!empty($_FILES["FILE"]["name"]))
    {
		if(!file_exists($_SERVER['DOCUMENT_ROOT']."/upload/whs_import"))
		{
			mkdir($_SERVER['DOCUMENT_ROOT']."/upload/whs_import", 775);
		}
		$file_is_moved = false;	
		if(is_writable($_SERVER['DOCUMENT_ROOT']."/upload/whs_import"))
		{
			try{
				$file_is_moved = move_uploaded_file($_FILES["FILE"]["tmp_name"],$_SERVER['DOCUMENT_ROOT']."/upload/whs_import/".$_FILES["FILE"]["name"]);
				if(!$file_is_moved){
					throw new Exception(GetMessage("ELCORSO_WAREHOUSECSVIMPORT_NE_MOGU_PEREMESTITQ").$_SERVER['DOCUMENT_ROOT'].'/upload/whs_import/'.'. '.GetMessage("ELCORSO_WAREHOUSECSVIMPORT_VOZMOJNO_OTKAZANO_V"));
				}	
			}
			catch(Exception $e){
				$message = new CAdminMessage($e->getMessage());
			}
		}
		else
		{
			$message = new CAdminMessage(GetMessage("ELCORSO_WAREHOUSECSVIMPORT_NE_SOHRANEN_ZAGRUJEN").$_SERVER['DOCUMENT_ROOT']."/upload/whs_import");
		}
		
		
        if($file_is_moved)
		{
			// прочитаем файл для импорта
			try{
				$handle = fopen($_SERVER['DOCUMENT_ROOT']."/upload/whs_import/".$_FILES["FILE"]["name"], "r");
				if(!$file_is_moved){
					throw new Exception(GetMessage("ELCORSO_WAREHOUSECSVIMPORT_NE_MOGU_OTKRYTQ_FAYL").$_SERVER['DOCUMENT_ROOT']."/upload/whs_import/".$_FILES["FILE"]["name"]);
				}	
			}
			catch(Exception $e){
				$message = new CAdminMessage($e->getMessage());
			}
		
			if(!empty($handle))
			{
				$arGeneral = array(
					"DOC_TYPE" => "A",
					"SITE_ID" => ($_REQUEST["SITE_ID"]) ? $_REQUEST["SITE_ID"] :"s1",
					"CONTRACTOR_ID" => ($_REQUEST["CONTRACTOR_ID"]) ? intval($_REQUEST["CONTRACTOR_ID"]) :"",
					"CURRENCY" => ($_REQUEST["CURRENCY"]) ? intval($_REQUEST["CURRENCY"]) :"RUB",
					"DATE_DOCUMENT" => ($_REQUEST["DOC_DATE"]) ? $_REQUEST["DOC_DATE"] : date("d.m.Y H:i:s", time()),//
					"CREATED_BY" => $USER->GetID(),
					"MODIFIED_BY" => $USER->GetID(),
					"COMMENTARY" => "",
				);


				$total = 0;
				$row = 0;
				$notice = '';
				$z=0;
				while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) 
				{
					if(!empty($_REQUEST["FIRST_TITLES_ROW"]) && !$row)
					{
						continue;
					}
					$row++;	
					/*$num = count($data);
					echo "<p> $num полей в строке $row: <br /></p>\n";
					$row++;
					for ($c=0; $c < $num; $c++) {
						echo $data[$c] . "<br />\n";
					}*/
					if(!empty($data))
					{
						//$line=explode("\n",$data);

							//Создаем документ, если с данными все хорошо
							if(empty($docId))
							{
								$docId = CCatalogDocs::add($arGeneral);
							}

							$res = CIBlockElement::GetByID(intval($data[0]));
							if($ar_res = $res->GetNext())
							{

									$stock = intval($data[1]);
									$z++;

									$arAdditional = Array(
										"AMOUNT" => $stock,
										"ELEMENT_ID" => $ar_res["ID"],
										"PURCHASING_PRICE" => floatval($data[2]),
										"STORE_TO" => ($_REQUEST["STORE_TO"]) ? intval($_REQUEST["STORE_TO"]) :1,
										//"STORE_FROM" => 4,
										"ENTRY_ID" => $z,
										"DOC_ID" => $docId,
									);
									$total +=  $arAdditional["PURCHASING_PRICE"] * $stock;	
									$docElementId = CCatalogStoreDocsElement::add($arAdditional);
							}
							else
							{
								$notice .= GetMessage("ELCORSO_WAREHOUSECSVIMPORT_ELEMENT").$data[0].' '.GetMessage("ELCORSO_WAREHOUSECSVIMPORT_NE_NAYDEN")."\r\n";
							}
					}	
				}
				//CCatalogDocs::conductDocument($docId, $USER->GetID());
				CCatalogDocs::update($docId, array("TOTAL" => $total));
				LocalRedirect("/bitrix/admin/warehouse_import_admin.php?ID=".$ID."&mess=import&docId=".$docId."&z=".$z."&lang=".LANG);
				fclose($handle);
			}
		
		
		}	
//        else 
//		{
//			$message = new CAdminMessage(GetMessage("ELCORSO_WAREHOUSECSVIMPORT_PUSTOY_FAYL"));
//		}	
    }
    else
    {
        $message = new CAdminMessage(GetMessage("ELCORSO_WAREHOUSECSVIMPORT_NE_ZAGRUJEN_FAYL"));
    }
}



$APPLICATION->SetTitle(GetMessage("ELCORSO_WAREHOUSE_IMPORT_edit"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aTabs = array(
array("DIV" => "edit1", "TAB" => GetMessage("ELCORSO_WAREHOUSECSVIMPORT_REDAKTIROVANIE_IMPOR"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("ELCORSO_WAREHOUSECSVIMPORT_REDAKTIROVANIE_IMPOR")),

);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($_REQUEST["mess"] == "ok" && $ID>0)
CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("ELCORSO_WAREHOUSECSVIMPORT_SOHRANENO"), "TYPE"=>"OK"));

if($_REQUEST["mess"] == "import")
{
	CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("ELCORSO_WAREHOUSECSVIMPORT_IMPORT_USPESNO_ZAVER").$z." ".GetMessage("ELCORSO_WAREHOUSECSVIMPORT_ELEMENTOV"), "TYPE"=>"OK"));
	
	if(!empty($_REQUEST["docId"]))
	{
		echo BeginNote().GetMessage("ELCORSO_WAREHOUSECSVIMPORT_IMPORTIROVANNYE_DANN").'<a href="/bitrix/admin/cat_store_document_edit.php?ID='.intval($_REQUEST["docId"]).'">'.$docId."</a>".EndNote();
		//
	}

	if(!empty($notice))
	{
		CAdminMessage::ShowMessage(array("MESSAGE"=>$notice, "TYPE"=>"ERROR"));
	}	
}



echo BeginNote().
	GetMessage("ELCORSO_WAREHOUSECSVIMPORT_DLA_ZAGRUZKI_OSTATKO").'<strong>;</strong>\'.<br>'
		. GetMessage("ELCORSO_WAREHOUSECSVIMPORT_SOZDATQ_TAKOY_FAYL_V")
		
		. GetMessage("ELCORSO_WAREHOUSECSVIMPORT_POLA_DOLJNY_BYTQ_RAS").'</strong>:<br>'
		. '<strong>ID '.GetMessage("ELCORSO_WAREHOUSECSVIMPORT_TOVARA_IZ_INFOBLOKA").'</strong>;.<br>'
		. GetMessage("ELCORSO_WAREHOUSECSVIMPORT_CENA_MOJET_NE_UKAZYV").
	EndNote();

// SHOW ERRORS	
if($message)
	echo $message->Show();

?>
<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form" id="subscrf">
<?// проверка идентификатора сессии ?>
<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?=LANG?>">
<?if($ID>0 && !$bCopy):?>
  <input type="hidden" name="ID" value="<?=$ID?>">
<?endif;?>
<?
// отобразим заголовки закладок
$tabControl->Begin();
?>
<?
//********************
// первая закладка - форма редактирования параметров рассылки
//********************
$tabControl->BeginNextTab();


?>
<input type="hidden" name="ID" value="<?=intval($_REQUEST["ID"]);?>"/>
		<tr>
		  <td width="40%"><?=GetMessage("ELCORSO_WAREHOUSECSVIMPORT_FAYL_VYGRUZKI")?></td>
		  <td width="60%"><input type="file" name="FILE"/></td>
		</tr>
		<tr>
		  <td width="40%"><?=GetMessage("ELCORSO_WAREHOUSECSVIMPORT_PERVAA_STROKA_SODERJ")?></td>
		  <td width="60%"><input type="checkbox" name="FIRST_TITLES_ROW" /></td>
		</tr>
		<tr>
			  <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage('CAT_DOC_DATE')?>:</td>
			  <td width="60%" class="adm-detail-content-cell-r">
				  <?if($bReadOnly):?>
					  <?=$str_DATE_DOCUMENT?>
				  <?else:?>
					  <?= CalendarDate("DOC_DATE", (isset($str_DATE_DOCUMENT)) ? $str_DATE_DOCUMENT : date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), time()), "form_catalog_document_form", "15", "class=\"typeinput\""); ?>
				  <?endif;?>
			  </td>
		</tr>
		
		
		
		<tr class="adm-detail-required-field">
			<td width="40%" class="adm-detail-content-cell-l"><?= GetMessage("CAT_DOC_SITE_ID") ?>:</td>
			<td width="60%" class="adm-detail-content-cell-r">
				<select id="SITE_ID" name="SITE_ID" <?=$isDisable?>/>
				<?foreach($arSitesShop as $key => $val)
				{
					$selected = ($val['ID'] == $str_SITE_ID) ? 'selected' : '';
					echo"<option ".$selected." value=".$val['ID'].">".$val["NAME"]." (".$val["ID"].")"."</option>";
				}
				?>
				</select>
			</td>
		</tr>
					
		<tr class="adm-detail-required-field">
			<td width="40%" class="adm-detail-content-cell-l"><?= GetMessage("CAT_DOC_CONTRACTOR") ?>:</td>
			<td width="60%" class="adm-detail-content-cell-r">
				<?if(count($arContractors) > 0 && is_array($arContractors)):?>
					<select style="max-width:300px"  name="CONTRACTOR_ID" <?=$isDisable?>/>
					<?foreach($arContractors as $key => $val)
					{
						$selected = ($val['ID'] == $str_CONTRACTOR_ID) ? 'selected' : '';
						$companyName = ($val["PERSON_TYPE"] == CONTRACTOR_INDIVIDUAL) ? htmlspecialcharsbx($val["PERSON_NAME"]) : htmlspecialcharsbx($val["COMPANY"]." (".$val["PERSON_NAME"].")");
						echo"<option ".$selected." value=".$val['ID'].">".$companyName."</option>";
					}
					?>
					</select>
				<?else:?>
					<a href="/bitrix/admin/cat_contractor_edit.php?lang=<? echo urlencode(LANGUAGE_ID); ?>"><?echo GetMessage("CAT_DOC_CONTRACTOR_ADD")?></a>
				<?endif;?>
			</td>
			
		</tr>
					
		<tr class="adm-detail-required-field">
			<td width="40%" class="adm-detail-content-cell-l"><?= GetMessage("CAT_DOC_CURRENCY") ?>:</td>
			<td width="60%" class="adm-detail-content-cell-r"><? echo CCurrency::SelectBox("CAT_CURRENCY_STORE", $str_CURRENCY, "", true, "", " id='CAT_CURRENCY_STORE'".$isDisable);?></td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%" class="adm-detail-content-cell-l"><?= GetMessage("CAT_DOC_STORE_TO") ?>:</td>
			<td width="60%" class="adm-detail-content-cell-r"><?=$store_to_select?></td>
		</tr>

  <!--  HTML-код строк таблицы -->
  
 
<?
//********************
// вторая закладка - параметры автоматической генерации рассылки
//********************


// завершение формы - вывод кнопок сохранения изменений
$tabControl->Buttons(

);
?>

&nbsp;
<input type="submit" value="<?=GetMessage("ELCORSO_WAREHOUSECSVIMPORT_IMPORTIROVATQ")?>" name="IMPORT"/>

<?

// завершаем интерфейс закладки
$tabControl->End();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>