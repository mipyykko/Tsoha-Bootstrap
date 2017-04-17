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
    
    public function __construct($attributes) {
        parent::__construct($attributes);
    }
    
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
}
