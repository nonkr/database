<?php

namespace Josh\Database\Exceptions;

class NotFoundException extends \Exception
{

    /**
     * NotFoundException constructor.
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  string $message
     * @param  int    $code
     */
    public function __construct($message = "", $code = 404)
    {
        return parent::__construct($message, $code);
    }

}