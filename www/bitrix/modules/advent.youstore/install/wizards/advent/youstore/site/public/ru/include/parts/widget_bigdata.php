<div class="widget">
    <h3>Возможно, это вас<br/>заинтересует:</h3>
	<?$APPLICATION->IncludeComponent(
		"bitrix:catalog.bigdata.products", 
		"advice_product", 
		array(
			"RCM_TYPE" => "bestsell",
			"ID" => $_REQUEST["PRODUCT_ID"],
			"IBLOCK_TYPE" => "catalog",
			"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
			"SHOW_FROM_SECTION" => "N",
			"HIDE_NOT_AVAILABLE" => "N",
			"SHOW_DISCOUNT_PERCENT" => "N",
			"PRODUCT_SUBSCRIPTION" => "N",
			"SHOW_NAME" => "Y",
			"SHOW_IMAGE" => "Y",
			"MESS_BTN_BUY" => "Купить",
			"MESS_BTN_DETAIL" => "Подробнее",
			"MESS_BTN_SUBSCRIBE" => "Подписаться",
			"PAGE_ELEMENT_COUNT" => "6",
			"LINE_ELEMENT_COUNT" => "3",
			"TEMPLATE_THEME" => "blue",
			"DETAIL_URL" => "",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "3600",
			"CACHE_GROUPS" => "N",
			"SHOW_OLD_PRICE" => "N",
			"PRICE_CODE" => array(
				0 => "BASE",
			),
			"SHOW_PRICE_COUNT" => "1",
			"PRICE_VAT_INCLUDE" => "Y",
			"CONVERT_CURRENCY" => "Y",
			"BASKET_URL" => SITE_DIR."personal/basket.php",
			"ACTION_VARIABLE" => "action",
			"PRODUCT_ID_VARIABLE" => "id",
			"PRODUCT_QUANTITY_VARIABLE" => "quantity",
			"ADD_PROPERTIES_TO_BASKET" => "Y",
			"PRODUCT_PROPS_VARIABLE" => "prop",
			"PARTIAL_PRODUCT_PROPERTIES" => "Y",
			"USE_PRODUCT_QUANTITY" => "N",
			"SHOW_PRODUCTS_16" => "Y",
			"PROPERTY_CODE_16" => array(
				0 => "",
				1 => "",
			),
			"CART_PROPERTIES_16" => array(
				0 => "",
				1 => "",
			),
			"ADDITIONAL_PICT_PROP_16" => "MORE_PHOTO",
			"LABEL_PROP_16" => "LABEL",
			"PROPERTY_CODE_17" => array(
				0 => "SIZE",
				1 => "COLOR",
				2 => "",
			),
			"CART_PROPERTIES_17" => array(
				0 => "SIZE",
				1 => "COLOR",
				2 => "",
			),
			"ADDITIONAL_PICT_PROP_17" => "MORE_PHOTO",
			"OFFER_TREE_PROPS_17" => array(
				0 => "SIZE",
				1 => "COLOR",
			),
			"SECTION_ID" => "",
			"SECTION_CODE" => "",
			"SECTION_ELEMENT_ID" => "",
			"SECTION_ELEMENT_CODE" => "",
			"DEPTH" => "2",
			"CURRENCY_ID" => "RUB"
		),
		false
	);?>
</div>