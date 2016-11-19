<?php

namespace Josh\Database\Helper;

trait Conditions
{

    /**
     * Get type of condition
     * 
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  $condition
     * @return bool|string
     */
    public function getTypeofCondition($condition)
    {
        if($condition == '=') {
            return 'equals';
        }

        return false;
    }

    /**
     * Equals condition
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  $value1
     * @param  $value2
     * @return bool
     */
    public function equals($value1, $value2)
    {
        if($value1 === $value2) {
            return true;
        }

        return false;
    }

}