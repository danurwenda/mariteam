<?php

defined('BASEPATH') OR
        exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link			http://www.codeigniter.com
 * @since        Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * Code Igniter Asset Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category		Helpers
 * @author       Philip Sturgeon < phil.sturgeon@styledna.net >
 */
// ------------------------------------------------------------------------

/**
 * General Asset Helper
 *
 * Helps generate links to asset files of any sort. Asset type should be the
 * name of the folder they are stored in.
 *
 * @access		public
 * @param		string    the name of the file or asset
 * @param		string    the asset type (name of folder)
 * @param		string    optional, module name
 * @return		string    full url to asset
 */
function other_asset_url($asset_name, $module_name = NULL) {
    $obj = & get_instance();
    $base_url = $obj->config->item('base_url');

    $asset_location = $base_url . 'vendor/';

    if (!empty($module_name)):
        $asset_location .= 'modules/' . $module_name . '/';
    endif;

    $asset_location .= $asset_name;

    return $asset_location;
}

// ------------------------------------------------------------------------

/**
 * Parse HTML Attributes
 *
 * Turns an array of attributes into a string
 *
 * @access		public
 * @param		array		attributes to be parsed
 * @return		string 		string of html attributes
 */
function _parse_asset_html($attributes = NULL) {

    if (is_array($attributes)):
        $attribute_str = '';

        foreach ($attributes as $key => $value):
            $attribute_str .= ' ' . $key . '="' . $value . '"';
        endforeach;

        return $attribute_str;
    endif;

    return '';
}

// ------------------------------------------------------------------------

/**
 * CSS Asset Helper
 *
 * Helps generate CSS asset locations.
 *
 * @access		public
 * @param		string    the name of the file or asset
 * @param		string    optional, module name
 * @return		string    full url to css asset
 */
function css_asset_url($asset_name, $module_name = NULL) {
    return other_asset_url($asset_name, $module_name);
}

// ------------------------------------------------------------------------

/**
 * CSS Asset HTML Helper
 *
 * Helps generate JavaScript asset locations.
 *
 * @access		public
 * @param		string    the name of the file or asset
 * @param		string    optional, module name
 * @param		string    optional, extra attributes
 * @return		string    HTML code for JavaScript asset
 */
function css_asset($asset_name, $module_name = NULL, $attributes = array()) {
    $attribute_str = _parse_asset_html($attributes);

    return '<link href="' . css_asset_url($asset_name, $module_name) . '" rel="stylesheet" type="text/css"' . $attribute_str . ' />';
}

// ------------------------------------------------------------------------

/**
 * Image Asset Helper
 *
 * Helps generate CSS asset locations.
 *
 * @access		public
 * @param		string    the name of the file or asset
 * @param		string    optional, module name
 * @return		string    full url to image asset
 */
function image_asset_url($asset_name, $module_name = NULL) {
    return other_asset_url($asset_name, $module_name);
}

// ------------------------------------------------------------------------

/**
 * Image Asset HTML Helper
 *
 * Helps generate image HTML.
 *
 * @access		public
 * @param		string    the name of the file or asset
 * @param		string    optional, module name
 * @param		string    optional, extra attributes
 * @return		string    HTML code for image asset
 */
function image_asset($asset_name, $module_name = '', $attributes = array()) {
    $attribute_str = _parse_asset_html($attributes);

    return '<img src="' . image_asset_url($asset_name, $module_name) . '"' . $attribute_str . ' />';
}

// ------------------------------------------------------------------------

/**
 * JavaScript Asset URL Helper
 *
 * Helps generate JavaScript asset locations.
 *
 * @access		public
 * @param		string    the name of the file or asset
 * @param		string    optional, module name
 * @return		string    full url to JavaScript asset
 */
function js_asset_url($asset_name, $module_name = NULL) {
    return other_asset_url($asset_name, $module_name);
}

// ------------------------------------------------------------------------

/**
 * JavaScript Asset HTML Helper
 *
 * Helps generate JavaScript asset locations.
 *
 * @access		public
 * @param		string    the name of the file or asset
 * @param		string    optional, module name
 * @return		string    HTML code for JavaScript asset
 */
function js_asset($asset_name, $module_name = NULL) {
    return '<script type="text/javascript" src="' . js_asset_url($asset_name, $module_name) . '"></script>';
}

function fancybox_asset_url($asset_name, $module_name = NULL) {
    return other_asset_url($asset_name, $module_name);
}

function fancybox_asset($asset_name, $module_name = NULL) {
    return '<script type="text/javascript" src="' . fancybox_asset_url($asset_name, $module_name) . '"></script>';
}

//json_asset
//dimas & ucenk : 2/17/2011
function json_asset_url($asset_name, $module_name = NULL) {
    return other_asset_url($asset_name, $module_name);
}

function json_asset($asset_name, $module_name = NULL) {
    return json_asset_url($asset_name, $module_name);
}

function libReq($level = 0) {
    if ($level == 1 || $level == 2) {
        return
                //css basic
                css_asset('index-style-telkom.css') .
                //jquery
                js_asset('jquery-1.4.4.js') .
                js_asset('jquery-ui-1.8.9.custom.min.js') .
                css_asset('jquery-ui-1.8.9.custom.css');
    } else if ($level == 3) {
        return
                //css basic
                css_asset('index-style-telkom.css') .
                //jquery
                js_asset('jquery-1.4.4.js') .
                js_asset('jquery-ui-1.8.9.custom.min.js') .
                css_asset('jquery-ui-1.8.9.custom.css') .
                //highcharts
                js_asset('highchart/highcharts.js') .
                js_asset('highchart/modules/exporting.js') .
                js_asset("menu.js") .
                //ext* except sparkline
                js_asset('ext/adapter/ext/ext-base.js') .
                js_asset('ext/ext-all.js') .
                js_asset('ext/examples/ux/CheckColumn.js') .
                //map
                js_asset('OpenLayers/lib/OpenLayers.js') .
                js_asset('geoext/lib/GeoExt.js');
    } else if ($level == 4) {
        return
                //css basic
                css_asset('index-style-telkom.css') .
                //ext* for sparkline
                js_asset('ext/adapter/ext/ext-base.js') .
                js_asset('ext/ext-all.js') .
                js_asset('ext/examples/ux/CheckColumn.js') .
                js_asset('Ext.ux.Sparkline.js');
    } else if ($level == 5) {
        return
                //css basic
                css_asset('index-style-telkom.css') .
                //jquery
                js_asset('jquery-1.4.4.js') .
                js_asset('jquery-ui-1.8.9.custom.min.js') .
                css_asset('jquery-ui-1.8.9.custom.css') .
                //highcharts
                js_asset('highchart/highcharts.js') .
                js_asset('highchart/modules/exporting.js') .
                js_asset("menu.js");
    } else if ($level == 6) {//compare
        return
                //css basic
                css_asset('index-style-telkom.css') .
                //jquery
                js_asset('jquery-1.4.4.js') .
                js_asset('jquery-ui-1.8.9.custom.min.js') .
                css_asset('jquery-ui-1.8.9.custom.css') .
                //ext* except sparkline
                js_asset('ext/adapter/ext/ext-base.js') .
                js_asset('ext/ext-all.js') .
                js_asset('ext/examples/ux/CheckColumn.js') .
                js_asset('data/iu.js') .
                //highcharts
                js_asset('highchart/highcharts.js') .
                js_asset('highchart/modules/exporting.js') .
                js_asset("menu.js");
    }
}

function getDefaultImageFileName($pid) {
    $base = "vendor/img/product/" . $pid;
    if (is_dir($base)) {
        $dh = opendir($base);
        $found = false;
        while ((!$found) && (false !== ($dir = readdir($dh)))) {
            if (is_file($base . "/" . $dir) && $dir !== '.' && $dir !== '..') {
                $found = true;
                return $dir;
            }
        }
        if ($found) {
            return $dir;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function excerpt($text, $query) {
//words
    $words = join('|', explode(' ', preg_quote($query)));

//lookahead/behind assertions ensures cut between words
    $s = '\s\x00-/:-@\[-`{-~'; //character set for start/end of words
    preg_match_all('#(?<=[' . $s . ']).{1,50}((' . $words . ').{1,50})+(?=[' . $s . '])#uis', $text, $matches, PREG_SET_ORDER);
    //if (count($matches) == 0)
        //echo 'kosongg';
//delimiter between occurences
    $results = array();

    foreach ($matches as $line) {
        $results[] = htmlspecialchars($line[0], 0, 'UTF-8');
    }
    $ellips = ' ... ';
    $result = count($results) > 0 ? $ellips . join($ellips, $results) . $ellips : '';

//highlight
    $result = preg_replace('#' . $words . '#iu', "<span class=\"highlighted\">\$0</span>", $result);

    return $result;
}

?>
