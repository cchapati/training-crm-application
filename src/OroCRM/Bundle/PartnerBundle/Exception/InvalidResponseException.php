<?php

namespace OroCRM\Bundle\PartnerBundle\Exception;

class InvalidResponseException extends \Exception implements PartnerException
{
    public static function create($message, \Exception $e)
    {
        if ($message) {
            $message .= ' ';
        }
        $message .= 'Reason: ' . $e->getMessage();
        return new static($message, 0, $e);
    }
}
