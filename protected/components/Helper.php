<?php
class Helper {
	public static function formatLanguage($lang_id, $encode = true) {
		$languages = Yii::app()->params["languages"];
		$lang = isset($languages[$lang_id]) ? $languages[$lang_id] . " (". $lang_id .")" : $lang_id;
		return $encode ? CHtml::encode($lang) : $lang;
	}

	public static function getLastElement(array $array, $diff = 1) {
		if (count($array) < $diff)
			return null;
		$keys = array_keys($array);
		return $array[$keys[count($keys) - $diff]];
	}

	public static function slug($text, $default = null) {
		$text = preg_replace('~[^\\pL\d]+~ui', '-', $text);
		$text = trim($text, '-');
		$text = mb_strtolower($text);
		if (empty($text)) {
			return $default;
		}
		return $text;
	}

	public static function proportion ($total, $num) {
		return $total > 0 ? round($num * 100 / $total, 2) : 0;
	}

	public static function generateRandomString($length = 12) {
		return substr(str_shuffle(sha1(uniqid().microtime(true))), 0, $length);
	}

	public static function getInstalledUrl() {
		return preg_replace("(https?://)", "", Yii::app() -> getBaseUrl(true));
	}

	public static function mb_ucfirst($string) {
		$first = mb_strtoupper(mb_substr($string, 0, 1));
		$second = mb_substr($string, 1);
		return $first . $second;
	}

	public static function cropText($text, $length, $separator = '...') {
		return mb_strlen($text) > $length ? mb_substr($text, 0, $length). $separator : $text;
	}

    /*
     * thelonglongdomain.com -> thelong...ain.com
     */
    public static function cropDomain($domain, $length=24, $separator='...') {
        if(mb_strlen($domain)<$length) {
            return $domain;
        }
        $sepLength=mb_strlen($separator);
        $backLen=6;
        $availableLen=$length-$sepLength-$backLen; // 20-3-6=11
        $firstPart=mb_substr($domain, 0, $availableLen);
        $lastPart=mb_substr($domain, -$backLen);
        return $firstPart.$separator.$lastPart;
    }

	public static function _v($a, $k, $d = null) {
		return isset($a[$k]) ? $a[$k] : $d;
	}

	public static function curl($url, array $headers = array(), $cookie = false) {
		$ch = curl_init($url);
		if($cookie) {
		    $path = Yii::getPathOfAlias(Yii::app()->params['site_cost.curl_cookie_cache_path']);
		    $cookie = $path."/cookie_{$cookie}.txt";
        }
		$html = self::curl_exec($ch, $headers, $cookie);
		curl_close($ch);
		return $html;
	}

	public static function curl_exec($ch, $headers=array(), $cookie = false, & $maxredirect = null)
    {
        return curl_exec(self::ch($ch, $headers, $cookie, $maxredirect));
	}

	public static function ch($ch, $headers=array(), $cookie = false, &$maxredirect = null) {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);

        if($cookie) {
            curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie);
            curl_setopt( $ch, CURLOPT_COOKIEFILE, $cookie);
        }

        if(!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if(isset($headers['user_agent'])) {
            $user_agent = $headers['user_agent'];
            unset($headers['user_agent']);
        } else {
            $user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36";
        }


        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent );

        $mr = $maxredirect === null ? 5 : intval($maxredirect);
        if (ini_get('open_basedir') == '' && (ini_get('safe_mode') == 'Off' || ini_get('safe_mode')=='')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
            curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
        } else {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            $original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            $parsed = parse_url($original_url);
            if(!$parsed) {
                return false;
            }
            $scheme = isset($parsed['scheme']) ? $parsed['scheme'] : '';
            $host = isset($parsed['host']) ? $parsed['host'] : '';

            if ($mr > 0)
            {
                $newurl = $original_url;
                $rch = curl_copy_handle($ch);

                curl_setopt($rch, CURLOPT_HEADER, true);
                curl_setopt($rch, CURLOPT_NOBODY, true);
                curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
                curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
                do
                {
                    curl_setopt($rch, CURLOPT_URL, $newurl);
                    $header = curl_exec($rch);
                    if (curl_errno($rch)) {
                        $code = 0;
                    } else {
                        $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                        if (in_array($code, array(301, 302, 307, 308))) {
                            preg_match('/Location:(.*?)\n/i', $header, $matches);
                            $newurl = trim(array_pop($matches));

                            if(!$parsed = parse_url($newurl)) {
                                return false;
                            }

                            if(!isset($parsed['scheme'])) {
                                $parsed['scheme'] = $scheme;
                            } else {
                                $scheme = $parsed['scheme'];
                            }

                            if(!isset($parsed['host'])) {
                                $parsed['host'] = $host;
                            } else {
                                $host = $parsed['host'];
                            }
                            $newurl = self::unparse_http_url($parsed);
                        } else {
                            $code = 0;
                        }
                    }
                } while ($code && --$mr);
                curl_close($rch);

                if (!$mr)
                {
                    if ($maxredirect === null)
                        return false;
                    else
                        $maxredirect = 0;

                    return false;
                }
                curl_setopt($ch, CURLOPT_URL, $newurl);
            }
        }
        return $ch;
    }

    public static function unparse_http_url(array $parsed) {
        if(!isset($parsed['host'])) {
            return false;
        }
        $url = isset($parsed['scheme']) ? $parsed['scheme']."://" : "http://";
        if(isset($parsed['user'])) {
            $url .= $parsed['user'];
            if(isset($parsed['pass'])) {
                $url .= ":".$parsed['pass'];
            }
            $url .= "@".$parsed['host'];
        } else {
            $url .= $parsed['host'];
        }

        if(isset($parsed['port'])) {
            $url .= ":".$parsed['port'];
        }

        if(isset($parsed['path'])) {
            $url .= $parsed['path'];
        }
        if(isset($parsed['query'])) {
            $url .= "?".$parsed['query'];
        }
        if(isset($parsed['fragment'])) {
            $url .= "#".$parsed['fragment'];
        }
        return $url;
    }

    public static function curl_get_final_url($curl_info, $default)
    {
        if(false === $curl_info) {
            return $default;
        }
        if(!empty($curl_info['redirect_url'])) {
            return $curl_info['redirect_url'];
        }
        return self::_v($curl_info, "url", $default);
    }

    public static function url_get_scheme_host($url, $default)
    {
        $parsed = parse_url($url);
        if(false === $parsed) {
            return $default;
        }
        if(!isset($parsed['scheme'], $parsed['host'])) {
            return $default;
        }
        return $parsed['scheme']."://".$parsed['host'].'/';
    }

	public static function time_elapsed_string($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);
		if (isset($diff)) {
			$string = array(
			'y' => array(
				"one"=>"{One} year ago",
				"many"=>"{Many} years ago",
			),
			'm' => array(
				"one"=>"{One} month ago",
				"many"=>"{Many} months ago",
			),
			'd' => array(
				"one"=>"{One} day ago",
				"many"=>"{Many} days ago",
			),
			'h' => array(
				"one"=>"{One} hour ago",
				"many"=>"{Many} hours ago",
			),
			'i' => array(
				"one"=>"{One} minute ago",
				"many"=>"{Many} minutes ago",
			),
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				//$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
				if($diff->$k > 1) {
					$v=Yii::t("post", $v['many'], array(
						"{Many}"=>$diff->$k
					));
				} else {
					$v=Yii::t("post", $v['one'], array(
						"{One}"=>$diff->$k
					));
				}
			} else {
				unset($string[$k]);
			}
		}
		if (!$full)
			$string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) : Yii::t("post", 'Just now');
		} else {
			return 0;
		}
	}

    public static function f($number, $decimal=0) {
        return number_format($number, $decimal, Yii::app()->params['site_cost.dec_point'], Yii::app()->params['site_cost.thousands_sep']);
    }
	
    public static function p($number, $d=false) {
		$decimals = $d === false ? Yii::app()->params['site_cost.decimals'] : (int) $d;
		return strtr(Yii::app()->params['site_cost.price_pattern'], array(
			'{currency}'=>Yii::app()->params['site_cost.currency'],
			'{price}'=>self::f($number, $decimals),
		));
    }
		
	public static function isAllowedCaptcha() {
        return Yii::app()->params['site_cost.captcha'];
	}

    public static function getFlagUrl($country_code) {
        $country=ECountryList::getInstance(Yii::app()->language);
        $country_code=strtolower($country_code);
        if($country_code=='uk') {
            $country_code='gb';
        }
        $flag_path=Yii::app()->basePath.'/../images/flags/'.$country_code.'.png';
        if(file_exists($flag_path)) {
            $src=Yii::app() -> getBaseUrl(true).'/images/flags/'.$country_code.'.png';
            return CHtml::image($src, $country->getCountryName($country_code), array(
                "alt"=>Helper::mb_ucfirst($country->getCountryName($country_code)),
            ));
        } else {
            return null;
        }
    }

    public static function alexaSpeed($num) {
        if($num==0) { return null; }
        if($num < 50) {
            return Yii::t("website", "{Percent} of sites are faster", array(
                "{Percent}"=>100-$num."%",
            ));
        } else {
            return Yii::t("website", "{Percent} of sites are slower", array(
                "{Percent}"=>$num."%",
            ));
        }
    }

		public static $brandUrl=null;
    public static function getBrandUrl() {
				if(empty(self::$brandUrl)) {
					self::$brandUrl=ucfirst(self::getInstalledUrl());
				}
        return self::$brandUrl;
    }

    public static function trimDomain($domain) {
			$domain=trim($domain);
			$domain=trim($domain, "/");
			$domain=mb_strtolower($domain);
			$domain = preg_replace("#^(https?://)#i", "", $domain);
			$domain = preg_replace("#^www\.#i", "", $domain);
			return $domain;
    }

    public static function checkDoFollowLink($domain) {
        $banner_free = (array) Yii::app()->params['sales.banner_free'];
        if (!empty($banner_free)) {
            foreach($banner_free as $pattern) {
                if(preg_match("#{$pattern}#i", $domain)) {
                    return true;
                }
            }
        }

        Yii::import("application.library.LinkFinder");
        $finder = new LinkFinder();
        $finder->setUrl('http://'.$domain);
        $finder->setLike(true);
        $finder->setHref(self::getInstalledUrl());
        return $finder->exists();
    }

    public static function getSummaryText($pgNr, $total, $perPage) {
        $start=($pgNr-1)*$perPage+1;
        $end=$start+$perPage-1;
        if($end>$total) {
            $end=$total;
            $start=$end-$total+1;
        }
        return $total == "0" ? Yii::t("misc", "Nothing found") : Yii::t('misc','Displaying {start}-{end} of {count} results.', array(
            '{start}'=>$start,
            '{end}'=>$end,
            '{count}'=>$total,
        ));
    }

    public static function highlightWordPart($str, $keyword) {
        $pattern="#\p{L}*?".preg_quote($keyword, "#")."\p{L}*#ui";
        return preg_replace($pattern, '<span class="highlight">$0</span>', $str);
    }

    public static function formatCurrencyPrice($price, $d=false) {
        return self::p(self::currencyPrice($price), $d);
    }

    public static function currencyPrice($price) {
        return $price * Yii::app()->params['site_cost.dollar_rate'];
    }

    public static function removeNonDigitCharacters($string)
    {
        return preg_replace("/[^\d+]/is", "", $string);
    }

    public static function getLocalConfigIfExists($config_name)
    {
        $dir = Yii::getPathOfAlias('application.config');
        $conf_local = $dir.'/'.$config_name."_local.php";
        $conf_prod = $dir.'/'.$config_name.".php";
        return file_exists($conf_local) ? require $conf_local : require $conf_prod;
    }

    public static function getNavName()
    {
        $brand_name = Yii::app()->params["app.nav_name"];
        return empty($brand_name) ? Yii::app()->name : $brand_name;
    }

    public static function getNavbarBrand()
    {
        $conf_nav_name = Yii::app()->params["app.nav_name"];
        $nav_name = empty($conf_nav_name) ? Yii::app()->name : $conf_nav_name;
        if(!empty(Yii::app()->params['app.nav_icon'])) {
            return CHtml::image(Yii::app()->params['app.nav_icon'], $nav_name, array(
                'width'=>'30',
                'height'=>30,
            ));
        } else {
            return $nav_name;
        }
    }

    public static function getMaxLabel($google, $bing, $yahoo, $backlinks)
    {
        $max = max($google, $bing, $yahoo, $backlinks);
        switch ($max) {
            case $google:
                return Yii::t("website", "Google Index");
            case $yahoo:
                return Yii::t("website", "Yahoo Index");
            case $bing:
                return Yii::t("website", "Bing Index");
            case $backlinks:
                return Yii::t("website", "Google Backlinks");
            default:
                return "Unknown";
        }
    }

    public static function are_real_backlinks($google, $bing, $yahoo, $backlinks)
    {
        if ($backlinks === 0) {
            return true;
        }
        $max = max($google, $bing, $yahoo);
        $allowed_backlinks = 1000;
        $margin = 0.01;
        if ($backlinks <= $allowed_backlinks) {
            return true;
        }
        return $max * 100 / $backlinks > $margin;
    }

    public static function url_privacy()
    {
        return !empty(Yii::app()->params['cookie_law.link']) ? strtr(Yii::app()->params['cookie_law.link'], array('{language}'=>Yii::app()->language)) : Yii::app()->createUrl("site/privacy");
    }

    public static function is_allowed_action()
    {
        $controllers = array(
            "site" => array("privacy", "terms"),
            "user" => "*",
            "profile" => "*",
        );
        if (!isset($controllers[Yii::app()->controller->id])) {
            return true;
        }

        $actions = $controllers[Yii::app()->controller->id];
        if (!is_array($actions)) {
            return false;
        }

        return !in_array(Yii::app()->controller->action->id, $actions);
    }

    public static function can_show_banner($banner_name)
    {
        $key = "site_cost.banner_$banner_name";
        return static::is_allowed_action() && !Yii::app()->errorHandler->error && !empty(Yii::app()->params[$key]);
    }

    public static function can_show_cookie_consent()
    {
        return Yii::app()->params['cookie_law.show'] && self::is_allowed_action();
    }
}