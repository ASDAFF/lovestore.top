<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
function forumCommentsCommentMobile(
	array $comment,
	array $arParams,
	array $arResult,
	ForumCommentsComponent $component)
{
	global $APPLICATION;
	$arParams["AVATAR_SIZE"] = (intval($arParams["AVATAR_SIZE"]) ?: 58);

	static $parser = null;
	if ($parser == null)
	{
		$parser = new forumTextParser(false);
		$parser->bMobile = true;
		$parser->LAZYLOAD = ($arParams["LAZYLOAD"] == "Y");
		$parser->arFiles = $arResult["FILES"];
	}

	$parser->arUserfields = $comment["PROPS"];
	$text = $parser->convert($comment["~POST_MESSAGE_TEXT"], $comment["ALLOW"]);

	$res = array(
		"ID" => $comment["ID"],
		"NEW" => ($comment["NEW"] == "Y" ? "Y" : "N"),
		"APPROVED" => $comment["APPROVED"],
		"POST_TIMESTAMP" => $comment["POST_TIMESTAMP"],
	//	"POST_TIME" => $comment["POST_TIME"],
	//	"POST_DATE" => $comment["POST_DATE"],
		"AUTHOR" => array(
			"ID" => $comment["AUTHOR_ID"],
			"NAME" => $comment["~NAME"],
			"LAST_NAME" => $comment["~LAST_NAME"],
			"SECOND_NAME" => $comment["~SECOND_NAME"],
			"AVATAR" => ($comment["AVATAR"] && $comment["AVATAR"]["FILE"] ? $comment["AVATAR"]["FILE"]['src'] : "")
		),
		"FILES" => $comment["FILES"],
		"UF" => $comment["PROPS"],
		"POST_MESSAGE_TEXT" => $text,
		"~POST_MESSAGE_TEXT" => $comment["~POST_MESSAGE_TEXT"],
		"CLASSNAME" => "",
		"BEFORE_HEADER" => "",
		"BEFORE_ACTIONS" => "",
		"AFTER_ACTIONS" => "",
		"AFTER_HEADER" => "",
		"BEFORE" => "",
		"AFTER" => "",
		"BEFORE_RECORD" => "",
		"AFTER_RECORD" => ""
	);

	return $res;
}