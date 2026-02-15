<?php

namespace App\Helper;

class LoggerHelper
{
    public static function log($message,$line = null,$method = null,$file = null)
    {
        $logFilePath = storage_path('logs/app.log');

        $logMessage = "[" . date('Y-m-d H:i:s') . "] @ ". $method .' @ '.  $line. ": ". $file . "\n" . $message . "\n";

        file_put_contents($logFilePath, $logMessage, FILE_APPEND);
    }
}