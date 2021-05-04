<?php
class SheduledManagerFin
{
    /** Url для запроса котировок */
    const URL = 'https://currate.ru/api/?get=rates';
    /** API-KEY */
    protected string $API_KEY;

    public function __construct()
    {
    }

    /**
     * Запуск скрипта
     * @return null|string
     * @throws Exception
     */
    public function start()
    {
        //return null;
        $msg = null;

        do {
            $setting = reset(get_option('widget_fin_widget'));
            if($setting === false) {
                $msg = 'Не настроен токкен';
                break;
            }
            if(!isset($setting['token'])) {
                $msg = 'Не настроен токкен';
                break;
            }
            $this->API_KEY = strval($setting['token']);
            // Запрашиваем котировки
            $quotation = [];
            $date_query_db = current_time("Y-m-d H:i:s");
            $date_query = str_replace(' ', 'T', $date_query_db);
            if(null !== ($msg = $this->queryAPI($quotation, $date_query))) break;
            // Проверяем полученный ответ
            if(!isset($quotation['status'])) {
                $msg = 'В ответе не указан статус запроса';
                break;
            }
            if($quotation['status'] != 200) {
                $msg = 'Не удалось получить ответ. Код статуса - ' . $quotation['status'];
                break;
            }
            if(!isset($quotation['data'])) {
                $msg = 'В ответе нет поля `data`';
                break;
            }
            // Обрабатываем полученный ответ
            foreach ($quotation['data'] as $k => $q) {
                $currencyVal = new CurrencyVal();
                $currencyVal->pairs = strval($k);
                $currencyVal->value = floatval($q);
                $currencyVal->date_query = $date_query_db;
                $currencyVal->store();
                unset($currencyVal);
            }
        } while(false);

        return $msg;
    }

    /**
     * Запрос на курсы валют
     * @param $quotation
     * @param string $date_query
     * @return string|null
     * @throws Exception
     */
    protected function queryAPI(&$quotation, $date_query = '')
    {
        if(empty($date_query)) {
            $date_query = current_time("Y-m-dTH:i:s");
        }
        $url = static::URL . '&pairs=' . Currency::getPairs() . '&date=' . $date_query . '&key=' . $this->API_KEY;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $quotation = json_decode($result, true);

        $msg = null;
        if(json_last_error() != JSON_ERROR_NONE) {
            $msg = 'Ошибка декодирования JSON (' . json_last_error() . ')';
        }

        return $msg;
    }
}
?>