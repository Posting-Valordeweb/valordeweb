<?php
class SearchCatalog
{
	private static $dmUrl = 'http://www.dmoz.org/search?q=u:%s';
	private static $alUrl = 'http://data.alexa.com/data?cli=10&dat=snbamz&url=%s';
	private static $yahUrl = 'http://dir.search.yahoo.com/search?ei=UTF-8&h=c&p=%s';
	private static $yanUrl = 'http://yaca.yandex.ru/yca/cat/?text=site:%s';

	private static $alexaExtensionUrl = 'http://www.alexa.com/minisiteinfo/%s?offset=5&version=alxg_20100607';

	public static function alexa($domain)
	{
		return self::getAlexaInfoFromExtensionPage($domain);
	}

	private static function getAlexaInfoFromExtensionPage($domain) {
        $stats = array(
            'rank'=>0,
            'linksin'=>0,
            'country_code'=>'XX',
            'country_name'=>'Unknown',
            'country_rank'=>0,
            'version'=>'4_0_5',
            'data'=>array(),
            'speed_time'=>0, // ms Backward compatibility
            'pct'=>0, // If PCT = 46, then 54 of all websites loads faster Backward compatibility
            'review_count'=>0, // Backward compatibility
            'review_avg'=>0, // Backward compatibility
        );
        return $stats;

		$url = sprintf(self::$alexaExtensionUrl, $domain);
		if(!$html = Helper::curl($url)) {
			return $stats;
		}

        //$html = file_get_contents(Yii::getPathOfAlias("webroot.html.of_today").".html");

        $pattern_raw_global_rank = "#<a(?:[^>]*)class=[\"'](small|big) data[\"'](?:[^>]*)>(.*?)</a>#is";
        preg_match($pattern_raw_global_rank, $html, $raw_global_rank);

        if(isset($raw_global_rank[2])) {
            $pattern_extract_rank_global = "#(<span(?:[^>]*)>(.*?)</span>)#is";
            $stats['rank'] = (int) Helper::removeNonDigitCharacters(preg_replace($pattern_extract_rank_global, "",  $raw_global_rank[2]));

            $pattern_extract_delta = "#<span(?:[^>]*)class=[\"']delta rank (\w+)[\"'](?:[^>]*)>(.*)</span>#is";
            preg_match($pattern_extract_delta, $raw_global_rank[2], $delta);

            if(isset($delta[1])) {
                $stats['data']['delta_direction'] = $delta[1];
                $stats['data']['delta'] = (int) Helper::removeNonDigitCharacters($delta[2]);
            }
        }

        $pattern_raw_local_rank = "#<p(?:[^>]*)class=[\"']textsmall nomarginbottom margintop10[\"'](?:[^>]*)>(.*?)</p>#is";
        preg_match($pattern_raw_local_rank, $html, $raw_local_rank);

        if(isset($raw_local_rank[1])) {
            $end_country_name_pos = mb_stripos($raw_local_rank[1], "Rank");
            $country_code = EmojiFlag::emojiToIso(mb_substr($raw_local_rank[1], 0, 2));
            if($country_code !== null) {
                $stats['country_code'] = $country_code;
            }
            if($end_country_name_pos !== false) {
                $start_country_name_pos = 2;
                $stats['country_name'] = trim(mb_substr($raw_local_rank[1], $start_country_name_pos, $end_country_name_pos - $start_country_name_pos));
            }

            $pattern_local_rank = "#<span(?:[^>]*)class=[\"']small data textbig marginleft10[\"'](?:[^>]*)><span class=[\"']hash[\"']>\#</span>(.*?)</span>#is";
            preg_match($pattern_local_rank, $raw_local_rank[1], $local_rank);
            if(isset($local_rank[1])) {
                $stats['country_rank'] = (int) Helper::removeNonDigitCharacters($local_rank[1]);
            }
        }


        $pattern_sites_linking = "#<p(?:[^>]*)class=[\"']textbig nomargin[\"'](?:[^>]*)>(.*?)<a(?:[^>]*)>(.*?)</a></p>#is";
        preg_match($pattern_sites_linking, $html, $matches_sites_linking);
        if(isset($matches_sites_linking[2])) {
            $stats['linksin'] = (int) Helper::removeNonDigitCharacters($matches_sites_linking[2]);
        }


        $similar_sites = array();
        $similar_sites_pattern = "#<a(?:[^>]*)class=[\"']Block truncation Link[\"'](?:[^>]*)href=[\"'](.*?)[\"'](?:[^>]*)>(.*?)</a>#is";
        preg_match_all($similar_sites_pattern, $html, $matches_similar_sites);
        if(isset($matches_similar_sites[1], $matches_similar_sites[2])) {
            foreach ($matches_similar_sites[1] as $i => $url) {
                $similar_sites[$i]['url'] = $url;
                $similar_sites[$i]['name'] = Helper::_v($matches_similar_sites[2], $i, "Unknown");
            }
            $stats['data']['similar_sites'] = $similar_sites;
        }



        $pattern_related_keywords_container = "#<section(?:[^>]*)class=[\"']Full[\"'](?:[^>]*)>(.*?)</section>#is";
        preg_match($pattern_related_keywords_container, $html, $matches_related_keywords_raw);

        if(isset($matches_related_keywords_raw[1])) {
            $pattern_related_keywords = "#<a(?:[^>]*)class=[\"']Link[\"'](?:[^>]*)>(.*?)</a>#is";
            preg_match_all($pattern_related_keywords, $matches_related_keywords_raw[1], $matches_related_keywords);
            if(isset($matches_related_keywords[1])) {
                $stats['data']['related_keywords'] = $matches_related_keywords[1];
            }
        }

        return $stats;
	}

	private static function strToInt($str) {
		return intval(trim(str_replace(",","",$str)));
	}

	public static function yahoo($domain)
	{
		$url = sprintf(self::$yahUrl, $domain);
		if(!$response = Helper::curl($url))
			return 0;
		preg_match_all('#<([^>]*) (?:[^>]*)class="url"(?:[^>]*)>(.*?)<\/\\1>#is', $response, $snippets);
		if(empty($snippets)) {
			return 0;
		}
		foreach($snippets[2] as $id=>$url) {
			if(mb_strpos(strip_tags($url), $domain) !== false) {
				preg_match('#<span id="resultCount" class="count">(.*?)</span>#ui', $response, $matches);
				$total = isset($matches[1]) ? (float)preg_replace("#\D#", "", $matches[1]) : 0;
				return $total > 0 ? $total : 1;
			}
		}
		return 0;
	}

	public static function inAlexa($domain)
	{
		$url = sprintf(self::$alUrl, $domain);
		if(!$response = Helper::curl($url))
			return 0;
		return preg_match('/\<popularity url\="(.*?)" text\="([\d]+)" source="(.*?)"\/\>/si', $response) ? 1 : 0;
	}

	public static function yandex($domain)
	{
		$url = sprintf(self::$yanUrl, $domain);
		if(!$response = Helper::curl($url)) {
			return 0;
		}
		preg_match_all('#<div class="z-counter">(.*)</div>#ui', $response, $matches);
		return isset($matches[1][0]) ? (float)preg_replace("/[^0-9]/", "", $matches[1][0]) : 0;
	}

	public static function dmoz($domain)
	{
		$url = sprintf(self::$dmUrl, $domain);
		if(!$response = Helper::curl($url)) {
			return 0;
		}
		$pattern = '<h3(?:[^>]*)id="site-list-header"(?:[^>]*)>(.*) \- (.*) of (.*)</h3>';
		preg_match("#{$pattern}#is", $response, $matches);
		return isset($matches[3]) ? (float)preg_replace("#\D#", "", $matches[3]) : 0;
	}
}