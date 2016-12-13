<?php

namespace Josh\Database\Helper;

trait Conditions
{

    /**
     * Equals condition
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  $value1
     * @param  $value2
     * @return bool
     */
    public function doCondition($condition, $value1 , $value2)
    {
        switch($condition){
            case '=':
            case '==': return $value1 == $value2; break;
            case '>': return $value1 > $value2; break;
            case '<': return $value1 < $value2; break;
            case '!=': return $value1 != $value2; break;
            case '<=': return $value1 <= $value2; break;
            case '>=': return $value1 >= $value2; break;
        }

        return false;
    }

}