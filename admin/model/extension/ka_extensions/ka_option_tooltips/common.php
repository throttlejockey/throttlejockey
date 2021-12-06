<?php
/*
	$Project: Product Option Tooltips $
	$Author: karapuz  team<support@ka-station.com> $

	$Version: 2.0.0.5 $ ($Revision: 71 $)
*/

namespace extension\ka_extensions\ka_option_tooltips;

class ModelCommon extends \Kamodel {

	public function isEmpty($text) {
		$text = strip_tags(html_entity_decode($text, ENT_QUOTES, 'UTF-8'), '<img>');
        // $text = strip_tags(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
		$text = str_replace('&nbsp;', '', $text);
		$text = trim($text);

		if (empty($text)) {
			return true;
		}

		return false;
	}
}

class_alias(__NAMESPACE__ . '\ModelCommon', '\ModelExtensionKaExtensionsKaOptionTooltipsCommon');