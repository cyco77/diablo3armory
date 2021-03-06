<?php
/**
*
* @Author Armando Tresova <xjsv24@gmail.com>
* @link http://www.armandotresova.com/projects/diablo_3_api_php.html
* @license Dual Licensed: MIT/GPL
*
* A Diablo 3 Web API wrapper written in PHP.
* This is meant to be very simple, easy to use and modify.
* It supports authenticated API calls and 'If-Modified-Since' header.
*
* Original Blizzard API Documentation: http://blizzard.github.com/d3-api-docs/
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class Diablo3 {
	private $battlenet_tag;
	private $host                = '.api.battle.net';
	private $media_host          = '.media.blizzard.com';
	private $battlenet_servers   = array('us', 'eu', 'tw', 'kr', 'cn');
	private $locales             = array('en_US', 'en_GB', 'es_MX', 'es_ES', 'it_IT', 'pt_PT', 'pt_BR',
		'fr_FR', 'ru_RU', 'pl_PL', 'de_DE', 'ko_KR', 'zh_TW', 'zh_CN');
	private $followerTypes       = array('enchantress', 'templar', 'scoundrel');
	private $artisanTypes        = array('blacksmith', 'jeweler');
	private $blizzardErrors      = array('OOPS', 'LIMITED', 'MAINTENANCE', 'NOTFOUND');
	private $current_locale;
	private $current_server;
	private $career_url;
	private $hero_url;
	private $item_url;
	private $follower_url;
	private $artisan_url;
	public $item_img_url;
	private $item_img_sizes      = array('small', 'large');
	public $skill_img_url;
	private $skill_img_sizes     = array('21', '42', '64');
	private $item_save_loc       = 'items/';       // Relative to DOCUMENT_ROOT
	private $skills_save_loc     = 'skills/';      // Relative to DOCUMENT_ROOT
	private $paperdolls_save_loc = 'paperdolls/';  // Relative to DOCUMENT_ROOT
	private $cache_loc           = 'cache/';           // Relative to DOCUMENT_ROOT
	private $use_cache           = false;                                 // Set to true to use 'If-Modified-Since' header
	private $skill_url;
	private $paperdoll_url;
	private $genders             = array('male', 'female');
	private $classes             = array('barbarian', 'crusader', 'witch-doctor', 'demon-hunter', 'monk', 'wizard');
	private $api_key             = '2u3qdrsrch9g542sbegj8zhmdy27xs28'; // Mashery Api Key (https://dev.battle.net/)
	private $no_battleTag        = false;
	private $fromCache           = false;

	public function __construct($battlenet_tag = '', $server = 'us', $locale = 'en_US', $apikey = '', $imagebasefolder = '') {
		if(!in_array($server, $this->battlenet_servers, true)) {
			$server = 'us';
		} else if($server == 'cn') {
			$server = '';

			// Override
			//
			$this->host       = 'www.battlenet.com.cn';     // 'cn.battle.net'
			$this->media_host = 'content.battlenet.com.cn'; // 'cn.media.blizzard.com'
		}

		if(!in_array($locale, $this->locales, true)) $locale = 'en_US';

		$this->current_locale = $locale;
		$this->current_server = $server;

		$this->api_key = $apikey;
				
		if (!empty($imagebasefolder)){
			$this->item_save_loc = $imagebasefolder . $this->item_save_loc;
			$this->skills_save_loc = $imagebasefolder . $this->skills_save_loc;
			$this->paperdolls_save_loc = $imagebasefolder . $this->paperdolls_save_loc;
		}

		if(!empty($battlenet_tag)) {
			$hash = strpos($battlenet_tag, '#');
			if($hash !== false) {
				$battlenet_tag = str_replace('#', '-', $battlenet_tag);
			}

			// Check if its a valid Battle.net tag
			//
			if(!$this->validateBattletag($battlenet_tag)) {
				error_log("Battle.net tag provided not valid. ({$battlenet_tag})");
				exit(0);
			}

			// Check if the api key is empty
			//
			if(empty($this->api_key)) {
				error_log("Battle.net API Key is Required");
				exit(0);
			}

			$this->battlenet_tag  = urlencode($battlenet_tag);
			$this->career_url     = 'https://'.$server.$this->host.'/d3/profile/'.$this->battlenet_tag.'/index';
			$this->hero_url       = 'https://'.$server.$this->host.'/d3/profile/'.$this->battlenet_tag.'/hero/';
			error_log($this->hero_url);
		} else {
			$this->no_battleTag = true;
		}

		$this->item_url      = 'https://'.$server.$this->host.'/d3/data/';
		$this->follower_url  = 'https://'.$server.$this->host.'/d3/data/follower/';
		$this->artisan_url   = 'https://'.$server.$this->host.'/d3/data/artisan/';
		$this->item_img_url  = 'https://'.$server.$this->media_host.'/d3/icons/items/';
		$this->skill_img_url = 'https://'.$server.$this->media_host.'/d3/icons/skills/';
		$this->skill_url     = 'https://'.$server.$this->host.'/d3/'.substr($locale, 0, -3).'/tooltip/';
		$this->paperdoll_url = 'https://'.$server.$this->host.'/d3/static/images/profile/hero/paperdoll/';
		
		$this->http_item_img_url  = 'http://'.$server.$this->media_host.'/d3/icons/items/';
		$this->http_skill_img_url = 'http://'.$server.$this->media_host.'/d3/icons/skills/';
	}

	/**
	 * validateBattletag
	 * Checks if the battle tag meets the requirements
	 * https://us.battle.net/support/en/article/battletag-naming-policy
	 *
	 * @param  string $battlenet_tag [description]
	 * @return boolean               [description]
	 */
	public function validateBattletag($battlenet_tag) {
		$pattern = '/^[\p{L}\p{Mn}][\p{L}\p{Mn}0-9]{2,11}-[0-9]{4,5}+$/u';
		return (preg_match($pattern, $battlenet_tag)) ? true : false;
	}

	/**
	 * cURLcheckBasics
	 * Checks to see if required cURL functions are available
	 *
	 * Parameters:
	 *     (name) - about this param
	 */
	private function cURLcheckBasics() {
		if(!function_exists("curl_init")   &&
			!function_exists("curl_setopt") &&
			!function_exists("curl_exec")   &&
			!function_exists("curl_close")) return false;
		else return true;
	}

	/**
	 * curlSaveImage
	 * Get image with cURL, save it in $item_save_loc and return the image location
	 *
	 * Parameters:
	 *     (location) - save location (items or skills)
	 *     (url)      - a valid URL
	 *     (icon)     - icon name
	 *     (size)     - image sizes
	 */
	private function curlSaveImage($location, $url, $icon, $size = '') {
		if(empty($url) || empty($icon)) {
			error_log('URL or Icon Cannot Be Empty');
			return false;
		}

		switch($location) {
			case 'items':
				$real_item_path  = $this->item_save_loc;
				$return_location = $this->item_save_loc;
				$size            = $size.'/';
				$ext             = '.png';
				break;
			case 'skills':
				$real_item_path  = $this->skills_save_loc;
				$return_location = $this->skills_save_loc;
				$size            = $size.'/';
				$ext             = '.png';
				break;
			case 'paperdolls':
				$real_item_path  = $this->paperdolls_save_loc;
				$return_location = $this->paperdolls_save_loc;
				$size            = '';
				$ext             = '.jpg';
				break;
			default:
				error_log('Location Cannot Be Empty');
				return false;
				break;
		}

		if(!file_exists($real_item_path.$size.$icon.$ext)) {
			
			if(is_dir($real_item_path.$size) && is_writable($real_item_path.$size)) {
				
				if(!$this->cURLcheckBasics()) {
					error_log("cURL is NOT Available");
					return false;
				}

				$fp   = fopen($real_item_path.$size.$icon.$ext, 'wb');
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL,            $url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
				curl_setopt($curl, CURLOPT_FILE,           $fp);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($curl, CURLOPT_TIMEOUT,        20);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($curl, CURLOPT_MAXREDIRS,      5);
				curl_setopt($curl, CURLOPT_HEADER,         false);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT,  true);
				curl_setopt($curl, CURLOPT_PROTOCOLS,      CURLPROTO_HTTP);

				curl_exec($curl);
				$error = curl_errno($curl);

				if($error) {
					error_log('cURL Error: '.$error);
					$data = false;
				} else {
					$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

					if($http_status >= 400 && $http_status <= 599) {
						$data = false;
					} else if($http_status >= 200 && $http_status <= 399) {
						$data = $return_location.$size.$icon.$ext;
					} else {
						$data = false;
					}
				}

				curl_close($curl);
				fclose($fp);

				return $data;
			} else {
				error_log("Wrong Image Size or Directory: '".$real_item_path.$size."' not writable");
				return false;
			}
		} else {
			return $return_location.$size.$icon.$ext;
		}
	}

	/**
	 * curlRequest
	 * Basic cURL request
	 *
	 * Parameters:
	 *     (url) - a valid URL
	 */
	private function curlRequest($url) {
		if(empty($url)) {
			error_log("URL Cannot Be Empty");
			return false;
		}

		if(!$this->cURLcheckBasics()) {
			error_log("cURL is NOT Available");
			return false;
		}

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,            $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($curl, CURLOPT_TIMEOUT,        25);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($curl, CURLOPT_MAXREDIRS,      5);
		curl_setopt($curl, CURLOPT_HEADER,         false);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT,  true);
		curl_setopt($curl, CURLOPT_PROTOCOLS,      CURLPROTO_HTTPS);
		curl_setopt($curl, CURLOPT_FILETIME,       true);

		$header    = array();
		$file_time = 0;
		if($this->use_cache) {
			$url_md5    = md5($url);
			$cache_file = $this->cache_loc.$url_md5;

			if(file_exists($cache_file) && is_readable($cache_file)) {
				$json_decode = json_decode(file_get_contents($cache_file), true);
				$file_time   = $json_decode['Last-Modified'];
				$file_data   = $json_decode['Data'];
			} else {
				error_log('Cache File "'.$cache_file.'" Does Not Exist Or Is Not Readable');
			}

			if($file_time > 0) {
				curl_setopt($curl, CURLOPT_TIMECONDITION, CURL_TIMECOND_IFMODSINCE);
				curl_setopt($curl, CURLOPT_TIMEVALUE,     $file_time);
			}
		}

		$data       = curl_exec($curl);
		$error_no   = curl_errno($curl);
		$curl_error = curl_error($curl);

		$this->fromCache = false;

		if($error_no) {
			error_log('cURL Error: '.$error_no.' ('.$curl_error.') URL: '.$url);
			$data = false;
		} else {
			$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			if($http_status >= 400 && $http_status <= 599) {
				error_log('Error Data Return: '.$data.', HTTP Status Code: '.$http_status.', URL: '.$url);
				$data = false;
			} else if($http_status >= 200 && $http_status <= 399) {
				if($this->use_cache) {
					if($http_status == 304) {
						if(file_exists($cache_file) && is_readable($cache_file)) {
							$file_data = json_decode(file_get_contents($cache_file), true);
							$data      = $file_data['Data'];
							$this->fromCache = true;
						}
					} else {
						$last_modified = curl_getinfo($curl, CURLINFO_FILETIME);
						if(is_dir($this->cache_loc) && is_writable($this->cache_loc)) {
							$data_write = json_encode(array('Last-Modified' => $last_modified, 'Data' => $data));
							file_put_contents($cache_file, $data_write);
						} else {
							error_log('Cache Directory Must Be Writable. HTTP Code: '.$http_status);
						}
					}
				}
			} else {
				error_log('Error Data Return: '.$data.' HTTP Status Code: '.$http_status.', URL: '.$url);
				$data = false;
			}
		}

		curl_close($curl);

		return $data;
	}

	/**
	 * resultsFromCache
	 * Checks to see if fromCache value
	 *
	 */
	public function resultsFromCache() {
		return $this->fromCache;
	}

	/**
	 * getJsonData
	 * Checks to see if required cURL functions are available
	 *
	 * Parameters:
	 *     (name) - about this param
	 */
	private function getJsonData($url) {
		if(empty($url)) return false;

		$url  = $url.'?locale='.$this->current_locale.'&apikey='.$this->api_key;
		$data = $this->curlRequest($url);

		if(!empty($data)) $data = json_decode($data, true);

		if(isset($data['code']) && isset($data['reason'])) {
			if(in_array($data['code'], $this->blizzardErrors, true)) {
				error_log('API Fail Reason: '.$data['reason'].', Code: '.$data['code'].' URL: '.$url);
			} else {
				error_log('API Fail Reason Unknown, URL: '.$url);
			}
			$data = false;
		}

		return $data;
	}

	/**
	* getAllItemImages
	* Gets all the item images from a hero ID. If no size is passed both will be processed
	*
	* @param  int    $heroId [description]
	* @param  string $size    [description]
	*
	*/
	public function getAllHeroItemImages($hero_id = null, $size = '') {
		if(empty($hero_id) || !preg_match('/^[0-9]+$/', $hero_id)) {
			error_log('Invalid/Empty Hero Id');
			return false;
		}

		$hero_data = $this->getHero($hero_id);
		
		if(is_array($hero_data)) {
			foreach($hero_data['items'] as $key) {
				
				if(empty($size)) {
					$this->getItemImage($key['icon'], 'small');
					$this->getItemImage($key['icon'], 'large');
				} else {
					if(in_array($size, $this->item_img_sizes, true)) {
						$this->getItemImage($key['icon'], $size);
					} else {
						error_log("Invalid Size");
						return false;
					}
				}
			}
			
			return true;
		} else {
			return false;
		}
	}

	/**
	 * getItemImage
	 * Item image
	 *
	 * Parameters:
	 *     (icon)      - String of item icon name
	 *     (imageSize) - Size of image (small or large)
	 */
	public function getItemImage($icon = null, $imageSize = 'small') {
		if(empty($icon) || !in_array($imageSize, $this->item_img_sizes, true)) {
			error_log('Icon Name Empty or Invalid Size');
			return false;
		}

		$data = $this->curlSaveImage('items', $this->item_img_url.$imageSize.'/'.$icon.'.png', $icon, $imageSize);
		return $data;
	}

	/**
	 * getAllSkillImages
	 * Get all the skill images from a heroe ID
	 *
	 * @param  int    $heroId  The heroe id
	 * @param  string $size    The size : 64, 42, 21
	 * @return string          Error message if no valid size is sent
	 *
	 */
	public function getAllHeroSkillImages($hero_id = null, $size = null) {
		if(empty($hero_id) || !preg_match('/^[0-9]+$/', $hero_id)) {
			error_log('Invalid/Empty Hero Id');
			return false;
		}

		$hero_data = $this->getHero($hero_id);

		if(is_array($hero_data)) {
			foreach($hero_data['skills']['active'] as $skills) {
				if(isset($skills['skill']['icon'])) {
					$skill_icon = $skills['skill']['icon'];

					// Checking the size
					//
					switch($size) {
						case 64:
							$this->getSkillImage($skill_icon, '64');
							break;
						case 42:
							$this->getSkillImage($skill_icon, '42');
							break;
						case 21:
							$this->getSkillImage($skill_icon, '21');
							break;
						case null:
							$this->getSkillImage($skill_icon, '64');
							$this->getSkillImage($skill_icon, '42');
							$this->getSkillImage($skill_icon, '21');
							break;
						default:
							error_log("Not a correct image size. Choose between 64, 42 or 21.");
							return false;
							break;
					}					
				}				
			}
			
			foreach($hero_data['skills']['passive'] as $skills) {
				if(isset($skills['skill']['icon'])) {
					$skill_icon = $skills['skill']['icon'];

					// Checking the size
					//
					switch($size) {
						case 64:
							$this->getSkillImage($skill_icon, '64');
							break;
						case 42:
							$this->getSkillImage($skill_icon, '42');
							break;
						case 21:
							$this->getSkillImage($skill_icon, '21');
							break;
						case null:
							$this->getSkillImage($skill_icon, '64');
							$this->getSkillImage($skill_icon, '42');
							$this->getSkillImage($skill_icon, '21');
							break;
						default:
							error_log("Not a correct image size. Choose between 64, 42 or 21.");
							return false;
							break;
					}					
				}				
			}
			
			return true;
		} else {
			return false;
		}
	}

	/**
	 * getSkillImage
	 * Gets skill image
	 *
	 * Parameters:
	 *     (icon)      - String of skill icon name
	 *     (imageSize) - Size of image (21, 42 or 64)
	 */
	public function getSkillImage($icon = null, $imageSize = '21') {
		if(empty($icon) || !in_array($imageSize, $this->skill_img_sizes, true)) return 'Icon Name Empty or Invalid Size';

		$data = $this->curlSaveImage('skills', $this->skill_img_url.$imageSize.'/'.$icon.'.png', $icon, $imageSize);
		return $data;
	}

	/**
	 * getSkillToolTip
	 * Get Skill or Skill Rune Tooltip
	 *
	 * Parameters:
	 *     (tooltipUrl) - String of tooltipUrl (e.g. rune/barbarian/frenzy/a)
	 *     (jsonp)      - True to return in jsonp format (boolean)
	 */
	public function getSkillToolTip($tooltipUrl = null, $jsonp = false) {
		if(empty($tooltipUrl)) return 'Tooltip Url Empty';

		$jsonp_ext = '';
		if($jsonp) {
			$jsonp_ext = '?format=jsonp';
		}

		$data = $this->curlRequest($this->skill_url.$tooltipUrl.$jsonp_ext);
		return $data;
	}

	/**
	 * getPaperDoll
	 * Get character paperdoll (background image)
	 *
	 * Parameters:
	 *     (class)  - Class (barbarian, witch-doctor, demon-hunter, monk, wizard)
	 *     (gender) - Gender (male or female)
	 */
	public function getPaperDoll($class = null, $gender = 'male') {
		if(empty($class) || !in_array($class, $this->classes, true) || !in_array($gender, $this->genders, true)) return 'No/Wrong class provided or wrong gender type.';

		$data = $this->curlSaveImage('paperdolls', $this->paperdoll_url.$class.'-'.$gender.'.jpg', $class.'-'.$gender);
		return $data;
	}

	/**
	 * getCareer
	 * Gets career data
	 *
	 */
	public function getCareer() {
		if($this->no_battleTag) {
			error_log('Function not available without a BattleTag.');
			return false;
		} else {
			$data = $this->getJsonData($this->career_url);
			return $data;
		}
	}

	/**
	 * getHero
	 * Gets hero data
	 *
	 * Parameters:
	 *     (hero_id) - Hero ID (integer)
	 */
	public function getHero($hero_id = null) {
		if($this->no_battleTag) {
			error_log('Function not available without a BattleTag.');
			return false;
		} else {
			if(empty($hero_id) || !preg_match('/^[0-9]+$/', $hero_id)) return 'Invalid/Empty Hero Id';

			$data = $this->getJsonData($this->hero_url.$hero_id);

			return $data;
		}
	}

	/**
	 * getItem
	 * Gets item data
	 *
	 * Parameters:
	 *     (item_data) - String of item data (e.g. 'item/COGHsoAIEgcIBBXIGEoRHYQRdRUdnWyzFB2qXu51MA04kwNAAFAKYJMD')
	 */
	public function getItem($item_data = null) {
		if(empty($item_data)) {
			error_log('Item Data Cannot Be Empty');
			return false;
		}

		$data = $this->getJsonData($this->item_url.$item_data);

		return $data;
	}

	/**
	 * getItemById
	 * Gets item data by ID
	 *
	 * Parameters:
	 *     (item_id) - String of item id (e.g. 'Unique_Helm_006_104')
	 */
	public function getItemById($item_id = null) {
		if(empty($item_id)) {
			error_log('Item ID Cannot Be Empty');
			return false;
		}

		$data = $this->getJsonData($this->item_url.'item/'.$item_id);

		return $data;
	}

	/**
	 * getFollower
	 * Gets follower data
	 *
	 * Parameters:
	 *     (follower_type) - String of the type of follower. Options available: 'enchantress', 'templar' & 'scoundrel'
	 */
	public function getFollower($follower_type = null) {
		if(empty($follower_type) || !in_array($follower_type, $this->followerTypes, true)) {
			error_log('Invalid/Empty Follower Type');
			return false;
		}

		$data = $this->getJsonData($this->follower_url.$follower_type);

		return $data;
	}

	/**
	 * getArtisan
	 * Gets artisan data
	 *
	 * Parameters:
	 *     (artisan_type) - String of the type of artisan. Options available: 'blacksmith' & 'jeweler'
	 */
	public function getArtisan($artisan_type = null) {
		if(empty($artisan_type) || !in_array($artisan_type, $this->artisanTypes, true)) {
			error_log('Invalid/Empty Artisan Type');
			return false;
		}

		$data = $this->getJsonData($this->artisan_url.$artisan_type);

		return $data;
	}

	public function __desctruct() {
		unset($this->battlenet_tag);
	}
}
