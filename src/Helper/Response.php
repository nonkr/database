<?php

namespace Josh\Database\Helper;

trait Response
{

    /**
     * Return json response
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  $datas
     */
    public function responseJson($datas)
    {
        header('Content-Type: application/json;charset="UTF-8";');
        echo json_encode($datas);
    }

}