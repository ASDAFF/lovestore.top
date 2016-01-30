<?
global $DBType;
IncludeModuleLangFile(__FILE__);


CModule::AddAutoloadClasses(
	"elcorso.warehousecsvimport",
	array("CWarehouseCSVImport" => "classes/general/warehousecsvimport.php",)
);

if(!function_exists('dump'))
{
	function dump($var, $die = false, $all = false)
	{
		global $USER;
		if( in_array($USER->GetID(), array(1)) 
				|| ($all == true))
		{
			?>
			<pre><?print_r($var)?></pre><br>
			<?
		}
		if($die)
		{
			die;
		}
	}
}	


?>
