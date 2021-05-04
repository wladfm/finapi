<?php
class Currency extends DBObject
{
    public string $pref;
    public string $name;

    public function __construct()
    {
        parent::__construct('currency', 'code');
    }

    /**
     * Возвращает возможные комбинации конвертации
     * @return string
     * @throws Exception
     */
    public static function getPairs(): string
    {
        // Список валют
        $list = static::query();
        // Строка-список валют
        $str_pairs = '';
        // Обработанные комбинации
        $arr_pairs = [];

        foreach ($list as $currency) {
            /** @var static $currency */
            foreach ($list as $currency2) {
                /** @var static $currency2 */
                // Пропускаем ту же валюту
                if($currency->get_id() == $currency2->get_id()) continue;
                // Формируем комбинацию префикса
                $pref = $currency->pref . $currency2->pref;
                // Если ранее добавили комбинацию, то пропускаем
                if(isset($arr_pairs[$pref])) continue;
                // Добавляем новую комбинацию
                $arr_pairs[$pref] = $pref;
                $str_pairs .= (empty($str_pairs) ? '' : ',') . $pref;
            }
        }

        return $str_pairs;
    }
}
?>