<?php
class CurrencySetting extends DBObject
{
    public string $name;
    public string $value;

    public function __construct()
    {
        parent::__construct('currency_setting', 'ID');
    }

    public static function getSetting($name)
    {
        $class = new static();

        do {
            $list = static::query('`name`=\'' . strval($name) . '\' LIMIT 1');
            if(array_key_first($list) === null || !isset($list[array_key_first($list)])) break;
            $class = $list[array_key_first($list)];
        } while(false);

        return $class;
    }
}
?>