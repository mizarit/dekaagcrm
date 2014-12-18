<?php
/*
* This file is part of the OnlineAfspraken.nl Widget package
* (c) 2011 OnlineAfspraken <info@onlineafspraken.nl>
*
* @author Ricardo Matters <ricardo.matters@mizar-it.nl>
*
*/

/**
 * 
 * Implements the core features of a Widget, like:
 * Signing API request
 * Handling API request
 * Rendering templates and partials
 * Handling request parameters
 * 
 * @author Ricardo Matters <ricardo.matters@mizar-it.nl>
 *
 */
class WidgetCore
{
  // Placeholder for just one Widget object
  protected static $inst = null;

  // The language of the application
  public static $language = APP_LANGUAGE;

  // The locale setting of the application
  public static $locale = APP_CULTURE;

  // Set to true to log debug statements to Firebug
  protected static $debug = false;

  /**
   * 
	 * Creates and returns singleton Widget
	 * 
	 * @return Widget $widget;
	 */
  public static function getInstance()
  {
    if (!self::$inst)
    {
      $widget = new Widget;
      self::$inst = $widget;
    }

    if (!session_id()) session_start();

    //sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');

    return self::$inst;
  }

  /**
	 * 
	 * Entry-point of the widget
	 * Everything starts here :)
	 */
  public static function show()
  {
    $widget = Widget::getInstance();

    if (FB_USE_LOGIN) {
		?>
<div id="fb-root"></div>
<script type="text/javascript" src="http://connect.facebook.net/nl_NL/all.js"></script>
<script type="text/javascript">
FB.init({
  appId:'<?php echo FB_APP_ID; ?>',
  cookie:true,
  status:true,
  xfbml:true
});
</script><?php } ?>
<script type="text/javascript">
widget.options.URL = '<?php echo WIDGET_URL; ?>';
var mutationkeys = {};
var total = 0;

Calendar.DAY_NAMES = new Array(
'<?php echo __('Zondag'); ?>', '<?php echo __('Maandag'); ?>', '<?php echo __('Dinsdag'); ?>', '<?php echo __('Woensdag'); ?>', '<?php echo __('Donderdag'); ?>', '<?php echo __('Vrijdag'); ?>', '<?php echo __('Zaterdag'); ?>', '<?php echo __('Zondag'); ?>'
);

Calendar.TODAY_NAME = '<?php echo __('Vandaag'); ?>';

Calendar.SHORT_DAY_NAMES = new Array(
'<?php $s = __('Zondag'); echo $s[0]; ?>', '<?php $s = __('Maandag'); echo $s[0]; ?>', '<?php $s = __('Dinsdag'); echo $s[0]; ?>', '<?php $s = __('Woensdag'); echo $s[0]; ?>', '<?php $s = __('Donderdag'); echo $s[0]; ?>', '<?php $s = __('Vrijdag'); echo $s[0]; ?>', '<?php $s = __('Zaterdag'); echo $s[0]; ?>', '<?php $s = __('Zondag'); echo $s[0]; ?>'
);

Calendar.MONTH_NAMES = new Array(
'<?php echo __('Januari'); ?>', '<?php echo __('Februari'); ?>', '<?php echo __('Maart'); ?>', '<?php echo __('April'); ?>', '<?php echo __('Mei'); ?>', '<?php echo __('Juni'); ?>', '<?php echo __('Juli'); ?>', '<?php echo __('Augustus'); ?>',
'<?php echo __('September'); ?>', '<?php echo __('Oktober'); ?>', '<?php echo __('November'); ?>', '<?php echo __('December'); ?>'
);

Calendar.DAY_OFFSET = <?php echo DAY_OFFSET; ?>;

var i18nNoResources = '<?php echo __('Zonder resource'); ?>';
</script>
<?php 
if (!$widget->hasRequestParameter('step')) {
  $loadingStr = __('De gegevens worden opgehaald...');
  $url = str_replace('/lib/widget.php', '', WIDGET_REAL_URL);
  $theme = APP_THEME;
  echo <<<EOT
<div id="widget-container">
	<div id="widget-container-overlay"></div>
	<div id="widget-container-overlay-inner"><p><img src="{$url}/theme/{$theme}/img/ajax-loader.gif" alt="" /><br />{$loadingStr}</p></div>
	<div id="widget-container-inner">
EOT;
}
$step = $widget->hasRequestParameter('step') ? $widget->getRequestParameter('step') : 'appointment';
$method = 'execute'.ucfirst($step);

$widget->$method();

__log('', Widget::$debug);

if (!$widget->hasRequestParameter('step')) {
  echo <<<EOT
	</div>
</div>
EOT;
}
  }

  /**
	 * 
	 * Returns a rendered template
   *
	 * @param string $partial
	 * @param array $attributes
	 * 
	 * @return string $html
	 */
  public function renderPartial($partial = '', $attributes = array())
  {
    extract($attributes);
    ob_start();
    include(dirname(__FILE__).'/../templates/'.$partial.'.php');
    return ob_get_clean();
  }


  /**
	 * 
	 * Sends an API request to the OnlineAfspraken.nl REST server
	 * 
	 * @param string $method
	 * @param array $parameters
	 * 
	 * @return array $records
	 */
  public function sendRequest($method, $parameters = array())
  {
    $url = $this->createRequestURL($method, $parameters);

    $response = @file_get_contents($url);
    /*if ($method == 'setAppointment') {
      echo $url." \n";
      echo $response;
    }*/
    if (!$response) {
      return false;
      //$this->throwException('Could not load API URL', 'B'.__LINE__);
    }
    $xml = simplexml_load_string($response);
    if ($xml->Status->Status == 'failed') {
      $this->error = $xml->Status->Message;
      return false;
      //$this->throwException($xml->Status->Message, $xml->Status->Code);
    }

    $records = array();

    $records['Debug']['Url'] = $url;
    $records['Debug']['Response'] = $response;

    if ($xml->Objects) {
      foreach ($xml->Objects[0] as $k => $object) {
        foreach ($object as $key => $attributes) {
          $record[$key] = (string)$attributes;
        }

        $records[$k][] = $record;
      }
    }
    else {
      foreach ($xml as $k => $object) {
        if ($k == 'Status') continue;

        foreach ($object as $key => $attributes) {
          $record[$key] = (string)$attributes;
        }

        if (isset($record)) {
          $records[$k][] = $record;
        }
      }
    }

    return $records;
  }

  /**
	 * 
	 * Throws an exception when things go terribly wrong
	 * 
	 * @param string $message
	 * @param string $code
	 */
  public function throwException($message, $code)
  {
    echo 'ERROR '.$message.' Code '.$code;
    exit;
  }

  /**
	 * 
	 * Creates a valid API call URL, based on a method with parameters
	 * 
	 * @param string $method
	 * @param array $parameters
	 * 
	 * @return string $API_REST_url;
	 */
  public function createRequestURL($method, $parameters = array())
  {
    $salt = time();

    $signature = $this->sign(array_merge(array('method'=>$method), $parameters), API_SECRET, $salt);

    //var_dump($parameters);
    $url = API_URL.'?api_salt='.$salt.'&api_signature='.$signature.'&api_key='.API_KEY.'&method='.$method;
    foreach ($parameters as $key => $value) {
      $url .= '&'.urlencode($key).'='.urlencode($value);
    }

    __log($url);

    return $url;
  }

  /**
	 * 
	 * Signs a set of parameters
	 * 
	 * @param array $params, associative array with parameters, like AgendaId=>1, etc.
	 * @param string $api_secret, see the API settings screen for this value
	 * @param string $api_salt, ususally the timestamp
	 * 
	 * @return string $signature
	 */
  public function sign($params, $api_secret, $api_salt)
  {
    ksort($params);
    $sign_str = '';
    foreach ($params as $key => $value) {
      $sign_str .= str_replace(' ', '_', $key).$value;
    }

    $sign_str .= $api_secret.$api_salt;

    __log('sign string '.$sign_str);

    return sha1(str_replace(' ', '', $sign_str));
  }


  /**
   * 
   * Handles a request done by the client script ( ie. the javascript calls ), by passing them to
   * the server using a REST-call.
   * 
   * This method start the communication between your server and the OnlineAfspraken.nl REST server
   * 
   * @return array $response Array containing dates, times and a localised human-readable string of the selected date
   */
  public function handleRequest()
  {
    $date = $this->getRequestParameter('date');

    $agendas = $this->sendRequest('getAgendas');
    $agenda = array_shift($agendas['Agenda']);

    $appTypeIds = array();
    if ($this->getRequestParameter('apptypeId') == -1) {
      $appointmentTypes = $this->sendRequest('getAppointmentTypes', array());

      if (isset($appointmentTypes['AppointmentType'])) {
        foreach ($appointmentTypes['AppointmentType'] as $key => $appointmentType) {
          if ((bool)$appointmentType['CanBeBookedByConsumer']) {  
            $appTypeIds[] = $appointmentType['Id'];
          }
        }
      }
    }
    else {
      $appTypeIds[] = $this->getRequestParameter('apptypeId');
    }
    
    $response['BookableTimeStr'] = '';
    $response['BookableDays'] = array();
    $response['BookableTimes'] = array();

    setlocale(LC_ALL, 'nl_NL');
    
    foreach($appTypeIds as $appTypeId) {
    
      $y = date('Y');
      if (date('m') > 10) {
        $y++;
      }
      
      $validMutations = array();
      if (isset($_SESSION['booking']['answers'])) {
        $resources = $this->sendRequest('getResources', array('AgendaId' => $agenda['Id']));
        $resourceNames = array();
        foreach ($resources['Resource'] as $resource) {
          $resourceNames[$resource['Id']] = $resource['Name'];
        }
        foreach($_SESSION['booking']['answers'] as $question_id => $answer_id) {
          $question = DeKaagFormRow::model()->findByPk($question_id);
          if ($question) {
            $mutations = json_decode($question->mutations, true);
            $test = $mutations[$answer_id];
            if (isset($test['resource'])) {
              $validMutations[] = $test;
            }
          }
        } 
      }
      $validResources = array();
      if (count($validMutations) > 0) {
        foreach ($validMutations as $key => $validMutation) {
          foreach ($resourceNames as $resourceId => $resourceName) {
            if (fnmatch($validMutation['resource'], $resourceName)) {
              $validResources[$key][] = $resourceId;
              $response['resourceMatch_'.$key] = $validMutation['resource'];
            }
          }
          $response['validResource_'.$key] = $validResources[$key];
        }
      }
      else {
        $validResources = array(array(-1));
      }
    
      // TODO: handle multiple resource requirements  
     
      $response['answers'] = $_SESSION['booking']['answers'];
    
      $tmp = array();
      foreach ($validResources as $a => $b) {
        foreach ($b as $validResource) {
          /*$bookableDays = $this->sendRequest('getBookableDays', array(
            'AgendaId' => $agenda['Id'],
            'StartDate' => date('Y-m-01'),
            'EndDate' => $y.'-12-31',
            'AppointmentTypeId' => $appTypeId,
            'ResourceId'=> $validResource
          ));
          
          if ($bookableDays && isset($bookableDays['BookableDay'])) {
            foreach ($bookableDays['BookableDay'] as $bookableDay) {
              if (isset($bookableDay['Date'])) {
                $date = $bookableDay['Date'];
                $response['BookableDays'][date('Y-n-j', strtotime($bookableDay['Date']))][] = $appTypeId;
                $response['BookableDayStr'][date('Y-n-j', strtotime($bookableDay['Date']))] = strftime('<span>%A</span> %e %B %Y', strtotime($bookableDay['Date']));
              }
            }
          } */  
          $bookableDays = $this->sendRequest('getBookableDays', array(
            'AgendaId' => $agenda['Id'],
            'StartDate' => date('Y-m-01'),
            'EndDate' => $y.'-12-31',
            'AppointmentTypeId' => $appTypeId,
            'ResourceId'=> $validResource
          ));
          
          if ($bookableDays && isset($bookableDays['BookableDay'])) {
            foreach ($bookableDays['BookableDay'] as $bookableDay) {
              if (isset($bookableDay['Date'])) {
                $date = $bookableDay['Date'];
                $tmp[$a][date('Y-n-j', strtotime($bookableDay['Date']))][] = $appTypeId;
              }
            }
          }       
        }
      }
      if (count($tmp) == 0) {
        $response['BookableDays'] = array();
        $response['BookableDayStr'] = array();
      }
      else {
        $checks = count($validResources); // number of sets that must be valid
        // now, we count every date in every set, only count that match $checks are valid dates
        $tmpDates = array();
        foreach ($tmp as $set => $dates) {
          foreach ($dates as $date => $atid) {
            if (!isset($tmpDates[$date])) {
              $tmpDates[$date] = 1;
            }
            else {
              $tmpDates[$date]++;
            }
          }
        }
        foreach ($tmpDates as $date => $count) {
          if ($count >= $checks) {
            $response['BookableDays'][$date][] = $appTypeId;
            $response['BookableDayStr'][$date] = strftime('<span>%A</span> %e %B %Y', strtotime($date));
          }
        }
      }
    }
   // var_dump($response);
    
    $bookableTimes = $this->sendRequest('getBookableTimes', array(
      'AgendaId' => $agenda['Id'],
      'Date' => $date,
      'AppointmentTypeId' => $appTypeId,
      'ResourceId'=> $this->getRequestParameter('resourceId')
    ));

    if ($bookableTimes) {
      if (isset($bookableTimes['BookableTime'])) {
        foreach ($bookableTimes['BookableTime'] as $bookableTime) {
          if (isset($bookableTime['StartTime'])) {
            if  (!in_array($bookableTime['StartTime'], $response['BookableTimes'])) {
              
              // validate the resource id
              /*if ($mutation && isset($mutation['resource']) && $mutation['resource'] != '') {
                $resourceName = $resourceNames[$bookableTime['ResourceId']];
                if(fnmatch($mutation['resource'], $resourceName)) {
                  $response['BookableTimes'][] = $bookableTime['StartTime'];
                }
              }
              else {
                $response['BookableTimes'][] = $bookableTime['StartTime'];
              }*/
              $response['BookableTimes'][] = $bookableTime['StartTime'];
            }
          }
        }
      }

      if (count($response['BookableTimes']) > 0) {
        setlocale(LC_TIME, Widget::$locale);
        $response['BookableTimeStr'] = __('Beschikbare tijden op').' '.strftime('%A %e %B %Y', strtotime($date));
      }
   }
   $tmp = $tmp2 = array();
   foreach ($response['BookableDays'] as $date => $days) {
     $tmp[strtotime($date)] = $days;
   }
   ksort($tmp);
   foreach ($tmp as $date => $days) {
     $tmp2[date('Y-n-j', $date)] = $days;
   }
   
   $response['BookableDays'] = $tmp2;
   
   
   // great, no locale support on WP site 
    $replacements = array(
      'Monday' => 'maandag',
      'Tuesday' => 'dinsdag',
      'Wednesday' => 'woensdag',
      'Thursday' => 'donderdag',
      'Friday' => 'vrijdag',
      'Saturday' => 'zaterdag',
      'Sunday' => 'zondag',
      'January' => 'januar',
      'February' => 'februari',
      'March' => 'maart',
      'April' => 'april',
      'May' => 'mei',
      'June' => 'juni',
      'July' => 'juli',
      'August' => 'augustus',
      'September' => 'september',
      'October' => 'oktober',
      'November' => 'november',
      'December' => 'december'
    );
    foreach ($replacements as $a => $b) {
      $response['BookableTimeStr'] = str_replace($a, $b, $response['BookableTimeStr']);
      foreach ($response['BookableDayStr'] as $k => $v) {
        $v = str_replace($a, $b, $v);
        $response['BookableDayStr'][$k] = $v;
      }
    }
    
    
    return $response;
  }

  /**
	 * 
	 * Returns a JSON object with the response of an API call
	 * 
	 * This method returns a string to you your client app, based on the result of a previous API call
	 * 
	 * @param string $response JSON-string
	 */
  public function sendResponse($response)
  {
    // Make sure we have no IE caching
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    // Set the response headers to serve JSON data
    header('Content-type: application/json');
    echo $this->handleResponse($response);
  }

  /**
	 * 
	 * Wrapper for JSON-encondig a response
	 * 
	 * This method can be extended if needed.
	 * 
	 * @param string $response JSON-string
	 */
  public function handleResponse($response)
  {
    return json_encode($response);
  }

  /**
	 * 
	 * Returns an escaped POST variable, when present
	 * @param unknown_type $name
	 */
  public function getRequestParameter($name)
  {
    if ($this->hasRequestParameter($name)) {
      /*return mysql_real_escape_string($_POST[$name]);*/
      return $_POST[$name];
    }

    return null;
  }
  
  public function setRequestParameter($name, $value)
  {
    $_POST[$name] = $value;

    return $value;
  }

  /**
	 * 
	 * Checks for the existence of a POST variable
	 * @param unknown_type $name
	 */
  public function hasRequestParameter($name)
  {
    return isset($_POST[$name]);
  }
}

/**
 * 
 * Generates a select-tag
 * 
 * @param string $name The name and id of the select-tag
 * @param string $value The selected value of the select-tag
 * @param array $options The options for the select-tag
 * @param array $config The attributes for the select-tag
 */
function __select_tag($name, $value, $options, $config = array())
{
  $attrs = '';
  foreach ($config as $key => $v) {
    $attrs .= $key.'="'.$v.'" ';
  }
  $ret = '<select name="'.$name.'" id="'.$name.'" '.trim($attrs).'>'.PHP_EOL;
  foreach ($options as $key => $v) {
    $selected = $key == $value ? ' selected="selected"': '';
    $ret .= '<option value="'.$key.'"'.$selected.'>'.__($v).'</option>'.PHP_EOL;
  }
  return $ret.'</select>'.PHP_EOL;
}

if (!function_exists('__')) {
  function __($text) {
    return $text;
  }
}

/**
 * 
 * Tries to lookup a translatable string in an i18n file
 * 
 * If not found, the key is returned.
 * 
 * @param string $string
 */
/*function __($string)
{
static $i18n = false;
if (!$i18n) {
$i18nStrings = array();
include_once(dirname(__FILE__).'/../config/i18n/'.Widget::$language.'.php');
$i18n = $i18nStrings;
}

return isset($i18n[$string]) ? $i18n[$string] : $string;
}*/

/**
 * 
 * Logs a mesage, and optionally dumps it to Firebug
 * 
 * @param string $string
 * @param boolean $dump
 */
function __log($string, $dump = false)
{
  static $logs = array();
  $logs[] = $string;
  if ($dump) {
    echo '<script type="text/javascript">'.PHP_EOL;
    echo '/* <![CDATA[ */'.PHP_EOL;
    foreach ($logs as $log) {
      if (trim($log) == '') continue;
      echo "console.log('".addslashes($log)."');".PHP_EOL;
    }
    echo '/* ]]> */'.PHP_EOL;
    echo '</script>'.PHP_EOL;
  }
}

