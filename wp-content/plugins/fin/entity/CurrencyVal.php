<?php
class CurrencyVal extends DBObject
{
    public string $pairs;
    public float $value;
    public string $date_query;

    public function __construct()
    {
        parent::__construct('currency_val', 'ID');
    }

    /**
     * Сохранение объекта
     */
    public function store(): void
    {
        if(empty($this->date_query)) {
            $this->date_query = current_time("Y-m-d H:i:s");
        }
        parent::store();
    }
}
?>