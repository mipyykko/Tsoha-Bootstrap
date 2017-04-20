<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of util
 *
 * @author pyykkomi
 */
class Util {

    private static $months = 
            array("", "tammikuu", "helmikuu", "maaliskuu", "huhtikuu", "toukokuu", "kesäkuu",
                  "heinäkuu", "elokuu", "syyskuu", "lokakuu", "marraskuu", "joulukuu");
    
    public function getMonthAsString($timestamp) {
        $month = date("n", strtotime($timestamp));
        $year = date("Y", strtotime($timestamp));
        $date = "";
        if ((int)$month > 0 & (int)$month <= 12) {
            $date = self::$months[(int)$month]."sta ";
        }
        $date .= $year;
        return $date;
    }
    
    public function dbQuery($sql, $params, $multiple) {
        $query = DB::connection()->prepare($sql);
        $query->execute($params);
        if ($multiple) {
            return $query->fetchAll();
        }
        return $query->fetch();
    }
    
    public function parsetags($input) {
        if (!$input) {
            return null;
        }
        
        $tags = array();
        \preg_match_all("/(#[\p{Pc}\p{N}\p{L}\p{Mn}]+)/u", $input, $tags);
        if ($tags) {
            foreach ($tags[1] as $tag) { // TODO:fix
                $input = \str_replace($tag, "<a href=\"/pitterpatter/tag/" . \substr($tag, 1) . "\">" . $tag . "</a>", $input);
            }
        }
        return $input;
    }
}
