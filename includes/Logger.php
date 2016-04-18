<?php

class Logger
{
    const ERROR = 1;
    const ALERT = 2;
    const INFO  = 3;
    const MESSAGE_RECEIVE  = 4;

    public static function alert($message, $context = [])
    {
        self::log(self::ALERT, $message, $context);
    }

    public static function error($message, $context = [])
    {
        self::log(self::ERROR, $message, $context);
    }

    public static function info($message, $context = [])
    {
        self::log(self::INFO, $message, $context);
    }

    public static function messageReceive($message, $context = [])
    {
        self::log(self::MESSAGE_RECEIVE, $message, $context);
    }

    private static function log($level, $message, $context = [])
    {
        switch ($level) {
            case self::INFO:
                $label    = 'INFO';
                $filename = 'info';
                break;

            case self::ALERT:
                $label    = 'ALERT';
                $filename = 'alert';
                break;

            case self::ERROR:
                $label    = 'ERROR';
                $filename = 'error';
                $mailMessage = 'Une erreur critique est survenu: '.$message;
                // wp_mail('jean-baptiste@alhena-conseil.com', 'Wordpress plugin : Erreur critique', $mailMessage);
                break;

            case self::MESSAGE_RECEIVE:
                $label    = 'MESSAGE RECEIVE';
                $filename = 'messageReceive';
                $mailMessage = 'Une erreur critique est survenu: '.$message;
                break;

            default:
                $label    = 'UNDEFINED';
                $filename = 'error';
                self::error('##! NO LEVEL INDICATED ACCORDING STANDARD !##');
                break;
        }

        $fileLocation = plugin_dir_path(__FILE__).'../logs/'.$filename.'.log';
        $logContent = sprintf('[%s] # %s : %s', date('Y-m-d H:i:s'), $label, $message);
        error_log($logContent.PHP_EOL, 3, $fileLocation);

        if ($context !== []) {
            error_log('CONTEXT : '.print_r($context, true).PHP_EOL, 3, $fileLocation);
        }
    }
}
