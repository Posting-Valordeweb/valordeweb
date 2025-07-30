<?php
class ParseUtils {
	public static function striptags($html) {
		$html = preg_replace('/(<|>)\1{2}/is', '', (string) $html);
		$search = array(
			'#<style[^>]*?>.*?</style>#siU', // Strip style tags properly
			'#<script[^>]*?>.*?</script>#si',// Strip out javascript
			'#<!--.*?>.*?<*?-->#si', // Strip if
			'#<[\/\!]*?[^<>]*?>#si',         // Strip out HTML tags
			/*'#<style[^>]*?>.*?</style>#siU', // Strip style tags properly*/
			'#<![\s\S]*?--[ \t\n\r]*>#si',  // Strip multi-line comments including CDATA
		);
		$html = preg_replace($search, " ", (string) $html);
		$html = html_entity_decode((string) $html, ENT_QUOTES, 'UTF-8');
		$html = preg_replace('#(<\/[^>]+?>)(<[^>\/][^>]*?>)#i', '$1 $2', (string) $html);
		return $html;
	}
}