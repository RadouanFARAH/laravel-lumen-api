<?php


namespace App\Exceptions;


class LiteResponseException extends \Exception
{
    protected $code;
    protected $data;
    protected $message;

    /**
     * LiteResponseException constructor.
     *
     * @param $code
     * @param $data
     * @param $message
     */
    public function __construct($code, $message, $data = null)
    {
        parent::__construct($message, $code);
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
