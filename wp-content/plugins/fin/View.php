<?php
class View
{
    public function __construct()
    {
    }

    public function add()
    {
        $this->viewListsCurrency();
    }

    /**
     * Возвращает поля выбора конвертируемых валют
     */
    protected function viewListsCurrency()
    {
        $code = '<div class="row">';
        $code .= '<div class="col-md-6"><input type="number" id="currency_first_value" placeholder="1"></div>';
        $code .= '<div class="col-md-6"><select class="c-select" id="currency_first"><option value="" disabled selected>Валюта 1</option>' . $this->getOptionsListCurrency() . '</select></div>';
        $code .= '</div>';
        echo $code;

        $code2 = '<div class="row">';
        $code2 .= '<div class="col-md-6"><input type="number" id="currency_second_value" placeholder="1"></div>';
        $code2 .= '<div class="col-md-6"><select class="c-select" id="currency_second"><option value="" disabled selected>Валюта 2</option>' . $this->getOptionsListCurrency() . '</select></div>';
        $code2 .= '</div>';
        echo $code2;

        $days = '<div class="row"><div class="col-md-4"><a class="count-day day-active" id="count_days_1" data-value="1">1 день</a></div>';
        $days .= '<div class="col-md-4"><a class="count-day" id="count_days_7" data-value="7">7 дней</a></div>';
        $days .= '<div class="col-md-4"><a class="count-day" id="count_days_30" data-value="30">30 дней</a></div></div>';
        echo $days;

        echo '<div id="graph_container"></div>';
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getListCurrency()
    {
        return Currency::query();
    }

    /**
     * @param null|int|string $selected
     * @return string
     * @throws Exception
     */
    protected function getOptionsListCurrency($selected = null)
    {
        $code = '';

        foreach ($this->getListCurrency() as $currency) {
            /** @var Currency $currency */
            $str_selected = !empty($selected) && intval($selected) == $currency->code ? ' selected' : '';
            $code .= '<option value="' . $currency->get_id() . '"' . $str_selected . '>' . $currency->name . '(' . $currency->pref . ')' . '</option>';
        }

        return $code;
    }
}
?>