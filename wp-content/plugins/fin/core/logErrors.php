<?php
class logErrors
{
    public $filename = null;

    public function __construct($filename)
    {
        if(empty($filename) || !is_string($filename))
            return;
        $this->filename = plugin_dir_path(__FILE__) . 'logs/' . $filename . '.log';
    }

    /**
     * Сохранение лога
     * @param string $msg
     */
    public function store($msg = null) {
        do {
            if(empty($this->filename) || !is_string($this->filename))
                return;
            if(empty($msg) || !is_string($msg))
                break;
            $msg = current_time("Y-m-d H:i:s") . "\t" . $msg . "\r\n";
            if(!file_exists($this->filename)) {
                $fp = fopen($this->filename, "w");
            } else {
                $fp = fopen($this->filename, "a");
            }
            fwrite($fp, $msg);
            fclose($fp);
        } while(false);
    }

    /**
     * Статическое сохранение лога
     * @param string $filename
     * @param string $msg
     */
    public static function set($filename, $msg)
    {
        $log = new static($filename);
        $log->store($msg);
    }
}
?>