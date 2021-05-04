<?php
class DBObject
{
    /**
     * @var wpdb wpdb
     */
    protected $wpdb;
    /**
     * Имя таблицы
     * @var string $table_name
     */
    protected string $table_name;
    /**
     * Ключ таблицы
     * @var string $key_name
     */
    protected string $key_name;

    /**
     * DBObject constructor.
     * @param string $table Имя таблицы без префикса
     * @param string $key
     * @throws Exception
     */
    public function __construct($table, $key)
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->setTableName($this->get_pref_table() . $table);
        $this->setKeyName($key);
        if(!empty($this->wpdb->error)) {
            logErrors::set('db', $this->error);
            throw new \Exception($this->error);
        }
    }

    /**
     * Префикс таблиц
     * @return string
     */
    public function get_pref_table(): string
    {
        return $this->wpdb->get_blog_prefix();
    }

    /**
     * @return wpdb
     */
    public function getWpdb(): wpdb
    {
        return $this->wpdb;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->table_name;
    }

    /**
     * @return string
     */
    public function getKeyName(): string
    {
        return $this->key_name;
    }

    /**
     * @param string $key_name
     */
    public function setKeyName(string $key_name): void
    {
        $this->key_name = $key_name;
        $this->$key_name = '';
    }

    /**
     * @param string $table_name
     */
    public function setTableName(string $table_name): void
    {
        $this->table_name = $table_name;
    }

    /**
     * Возвращает значение ключа
     * @return mixed
     */
    public function get_id()
    {
        $key = $this->getKeyName();
        return $this->$key;
    }

    /**
     * Присвоение ИД записи
     * @param $id
     */
    public function set_id($id): void
    {
        $key = $this->getKeyName();
        $this->$key = $id;
    }

    /**
     * Инициализирован ли объект
     * @return bool
     */
    public function get_init()
    {
        $key = $this->getKeyName();
        return isset($this->$key) && !empty($this->$key);
    }

    /**
     * Связывание массива и объекта
     * @param array $hash
     */
    public function bind($hash): void
    {
        foreach ($hash as $key => $elem) {
            if(isset($this->$key)) {
                $this->$key = $elem;
            }

            if(property_exists($this, $key)) {
                $this->$key = $elem;
            }
        }
    }

    /**
     * Загрузка объекта по ИД
     * @param mixed $id
     * @throws Exception
     */
    public function load($id): void
    {
        $msg = null;

        do {
            $results = $this->wpdb->get_results('SELECT * FROM `' . $this->getTableName() . '` WHERE `' . $this->getKeyName() . '` = \'' . $id . '\'', ARRAY_A);
            if(array_key_first($results) === null || !isset($results[array_key_first($results)])) {
                $msg = 'Не найден элемент';
                break;
            }
            $this->bind($results[array_key_first($results)]);
            $this->set_id($id);
        } while(false);

        if($msg !== null) {
            $log_msg = 'Ошибка загрузки объекта класса ' . get_class($this) . ' с ИД `' . $id . '`: ' . $msg;
            logErrors::set('db', $log_msg);
            throw new \Exception($log_msg);
        }
    }

    /**
     * Запрос к таблице
     * @param string $where
     * @return array
     * @throws Exception
     */
    public static function query($where = '')
    {
        $list = [];

        $elem = new static();
        $sql = "SELECT * FROM `" . $elem->getTableName() . "`" . (!empty($where) ? " WHERE " . $where : "");
        $results = $elem->getWpdb()->get_results($sql, ARRAY_A);
        foreach ($results as $result) {
            if(!isset($result[$elem->getKeyName()])) continue;
            $class = new static();
            $class->bind($result);
            $list[] = $class;
            unset($class);
        }
        unset($elem);

        return $list;
    }

    /**
     * Сохранение объекта
     */
    public function store(): void
    {
        if(!$this->get_init()) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    /**
     * Новая запись в таблицу
     */
    protected function insert()
    {
        $this->wpdb->insert($this->getTableName(), $this->getValuesFields());
    }

    /**
     * Обновление записи
     */
    protected function update()
    {
        if(!$this->get_init()) return;
        $key = $this->getKeyName();
        $this->wpdb->update( $this->getTableName(), $this->getValuesFields(), [$key => $this->$key]);
    }

    /**
     * Статический метод поиска по ИД
     * @param $id
     * @return static
     * @throws Exception
     */
    public static function id($id)
    {
        $class = new static();
        if(null !== ($msg = $class->load($id))) {
            $class = new static();
        }
        return $class;
    }

    /**
     * Возврат списка полей со значениями для записи/обновления
     * @return array
     */
    protected function getValuesFields()
    {
        $not_field = ['wpdb', 'table_name', 'key_name'];
        $fields = [];
        foreach ($this as $k => $v) {
            if(!in_array($k, $not_field)) {
                $fields[$k] = strval($v);
            }
        }
        if(empty($fields[$this->getKeyName()])) $fields[$this->getKeyName()] = null;

        return $fields;
    }
}
?>