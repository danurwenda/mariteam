<?php

defined('BASEPATH') OR
        exit('No direct script access allowed');

function formatDateDiff($start, $end = null) {
    if (!($start instanceof DateTime)) {
        $start = new DateTime($start);
    }

    if ($end === null) {
        $end = new DateTime();
    }

    if (!($end instanceof DateTime)) {
        $end = new DateTime($start);
    }

    $interval = $end->diff($start);
    $doPlural = function($nb, $str) {
        return $nb > 1 ? $str . 's' : $str;
    }; // adds plurals 

    $format = array();
    if ($interval->y !== 0) {
        $format[] = "%y " . $doPlural($interval->y, "year");
    }
    if ($interval->m !== 0) {
        $format[] = "%m " . $doPlural($interval->m, "month");
    }
    if ($interval->d !== 0) {
        $format[] = "%d " . $doPlural($interval->d, "day");
    }
    if ($interval->h !== 0) {
        $format[] = "%h " . $doPlural($interval->h, "hour");
    }
    if ($interval->i !== 0) {
        $format[] = "%i " . $doPlural($interval->i, "minute");
    }
    if ($interval->s !== 0) {
        if (!count($format)) {
            return "less than a minute ago";
        } else {
            $format[] = "%s " . $doPlural($interval->s, "second");
        }
    }

    // We use the two biggest parts 
    if (count($format) > 1) {
        $format = array_shift($format) . " and " . array_shift($format);
    } else if(count($format)==1) {
        $format = array_pop($format);
    }else{
        return 'Just now';
    }

    // Prepend 'since ' or whatever you like 
    return $interval->format($format);
}

function sqlDateToUTC($sqlDate) {
    $date = new DateTime($sqlDate, new DateTimeZone('UTC'));
    return $date->getTimestamp() * 1000;
}

/**
 * Beda frekuensi beda penyajian tooltip
 * @param type $chart
 * @return type
 */
function parseTooltipFormatter($series, $forDetail = false) {
    $dateFormat = '';
    $periodHandle = '';
    //assuming semua series pakai frekuensi yang sama
    $frek_id = isset($series[0]) ? $series[0]->frekuensi : 1;
    if ($frek_id == 7) {
        $dateFormat = "Highcharts.dateFormat('%Y', this.x)";
    } else if ($frek_id == 4 || $frek_id == 5) {
        $periodHandle = "
                var month = Highcharts.dateFormat('%m',this.x);
                var nth;
                if(month=='01'){
                    nth = '1';
                }
                else if(month=='04'){
                    nth = '2';
                }
                else if(month=='07'){
                    nth = '3';
                }
                else if(month=='10'){
                    nth = '4';
                }";
        $dateFormat = "'Q'+nth +Highcharts.dateFormat(' %Y', this.x)"; //got problem
    } else
    if ($frek_id == 2) {
        $periodHandle = "
                var day = Highcharts.dateFormat('%e',this.x);
                var nth;
                if(day<8){
                    nth = '1';
                }
                else if(day<15){
                    nth = '2';
                }
                else if(day<22){
                    nth = '3';
                }
                else if(day<29){
                    nth = '4';
                }
                else{
                nth = '5';
                }";
        $dateFormat = "'Minggu ke '+nth +' '+Highcharts.dateFormat('%B %Y', this.x)"; //got problem
    } else
    if ($frek_id == 3 || $frek_id == 8) {
        $dateFormat = "Highcharts.dateFormat('%B %Y', this.x)";
    } else
    if ($frek_id == 1) {
        $dateFormat = "Highcharts.dateFormat('%e %b %Y', this.x)";
    }

    if (count($series) == 1) {
        if ($forDetail) {
            $unit = $series[0]->unit;
            return "function() {" . $periodHandle . "
                                return '<b>'+ this.series.name +'</b><br/>'+"
                    . $dateFormat . "+' : ' + Highcharts.numberFormat(this.y, 2)+ ' $unit';
                            }";
        } else {
            return "function() {" . $periodHandle . "
                                return '<b>'+ this.series.name +'</b><br/>'+"
                    . $dateFormat . "+' : ' + Highcharts.numberFormat(this.y, 2)+ ' ' +this.series.yAxis.axisTitle.textStr;
                            }";
        }
    } else {
        return "function() {" .
                $periodHandle
                . "return '<b>'+ this.series.name +'</b><br/>'+" . $dateFormat . "+': '+ Highcharts.numberFormat(this.y, 2)+' '+ this.series.yAxis.axisTitle.textStr;
                }";
    }
}
