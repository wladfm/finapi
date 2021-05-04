<?php
class CurrencyQuery extends AJAX_Handler {
    public array $response;

    function callback()
    {
        $result = [];
        if(null !== ($msg = $this->getGraph())) {
            $result['error'] = $msg;
        } else {
            $result['graph'] = $this->response;
            $currency1_value = $_POST['currency_first_value'] ?? 0;
            $currency2_value = $_POST['currency_second_value'] ?? 0;
            if($currency1_value == 0 && $currency2_value == 0) {
                $currency1_value = 1;
            }
            $elem = end($this->response);
            if($elem !== false) {
                /** @var CurrencyVal $elem */
                $currency = floatval($elem->value);
                $kf = 10000;
                if($currency1_value == 0) {
                    $currency1_value = ceil($currency2_value * $kf / $currency) / $kf;
                } else {
                    $currency2_value = ceil($currency1_value * $currency * $kf) / $kf;
                }
                $result['currency1_value'] = $currency1_value;
                $result['currency2_value'] = $currency2_value;
            }
        }
        wp_send_json_success($result);
    }

    protected function getGraph()
    {
        $msg = null;
        $this->response = [];

        do {
            // Определяем выбранную валюту
            $currency1 = $_POST['currency_first'] ?? null;
            $currency2 = $_POST['currency_second'] ?? null;
            if(empty($currency1)) {
                $msg = 'Не указана Валюта 1';
                break;
            }
            if(empty($currency2)) {
                $msg = 'Не указана Валюта 2';
                break;
            }
            // Загружаем объекты валют
            $currencyFirst = Currency::id($currency1);
            if(!$currencyFirst->get_init()) {
                $msg = 'Не найдена Валюта 1 с ИД `' . $currency1 . '`';
                break;
            }
            $currencySecond = Currency::id($currency2);
            if(!$currencySecond->get_init()) {
                $msg = 'Не найдена Валюта 2 с ИД `' . $currency2 . '`';
                break;
            }

            // Формируем запрос
            $pref = $currencyFirst->pref . $currencySecond->pref;
            $date_diff = $_POST['diff'] ?? 0;
            $date_start = date("Y-m-d 00:00:00", strtotime(current_time("Y-m-d H:i:s") . " - " . $date_diff . " day"));
            $date_end = current_time("Y-m-d 23:59:59");
            $this->response = CurrencyVal::query('`pairs`=\'' . $pref . '\' AND `date_query` BETWEEN \'' . $date_start . '\' AND \'' . $date_end . '\'');
        } while(false);

        return $msg;
    }
}
?>