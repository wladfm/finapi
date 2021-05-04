<?php
/*
    Plugin Name: Курсы валют
*/

class Fin_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'Fin_Widget',
            __('Курсы валют', 'fin_widget_domain'),
            array( 'description' => __( 'Курсы валют', 'fin_widget_domain' ), 'token' => '')
        );

        // Подключаем файлы
        $this->init();
    }

    /**
     * Подключение необходимых файлов
     */
    protected function init()
    {
        $this->upgrade();
        // Инициализация "ядра"
        core::init_core();

        core::init_dir('scheduled');

        include( plugin_dir_path(__FILE__) . '/View.php');
    }

    /**
     * Добавление таблиц для работы виджета (при необходимости)
     */
    protected function upgrade()
    {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
        // Справочник курсов
        $table_currency = $wpdb->get_blog_prefix() . 'currency';
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $table_currency . "'") != $table_currency) {
            $sql = "CREATE TABLE `{$table_currency}` (
                        `code` INT(11) UNSIGNED NOT NULL,
                        `pref` VARCHAR(5) NOT NULL,
                        `name` VARCHAR(255) NOT NULL,
                        PRIMARY KEY  (`code`),
                        UNIQUE INDEX `pref` (`pref`) USING BTREE
                    ) {$charset_collate};";
            dbDelta( $sql );

            $wpdb->insert($table_currency, ['code' => '643', 'pref' => 'RUB', 'name' => 'Российский рубль']);
            $wpdb->insert($table_currency, ['code' => '756', 'pref' => 'CHF', 'name' => 'Швейцарский франк']);
            $wpdb->insert($table_currency, ['code' => '840', 'pref' => 'USD', 'name' => 'Доллар США']);
            $wpdb->insert($table_currency, ['code' => '933', 'pref' => 'BYN', 'name' => 'Белорусский рубль']);
            $wpdb->insert($table_currency, ['code' => '978', 'pref' => 'EUR', 'name' => 'Евро']);
        }

        $table_val = $wpdb->get_blog_prefix() . 'currency_val';
        if ($wpdb->get_var("SHOW TABLES LIKE '" . $table_val . "'") != $table_val) {
            $sql = "CREATE TABLE `{$table_val}` (
                        `ID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `pairs` VARCHAR (10) NOT NULL,
                        `value` FLOAT(12,10) NOT NULL,
                        `date_query` DATETIME NOT NULL,
                        PRIMARY KEY  (`ID`),
                        INDEX `pairs` (`pairs`) USING BTREE,
                        INDEX `date_query` (`date_query`) USING BTREE
                    ) {$charset_collate};";
            dbDelta( $sql );
        }
    }

    /**
     * Отображение виджета
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        if(!isset($instance['token']) || empty($instance['token'])) {
            echo '<p style="color: red">ОШИБКА. Не настроен токкен</p>';
            return;
        }

        echo $args['before_widget'];
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

        /**
         * Отрисовываем страницу виджета
         */
        $view = new View();
        $view->add();

        echo $args['after_widget'];
    }

    /**
     * Добавление виджета на страницу в админ-панели
     * @param array $instance
     * @return string|void
     */
    public function form( $instance ) {
        $title = __( 'Курсы валют', 'fin_widget_domain' );
        $token = esc_attr( $instance['token'] );
        ?>


        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

        <label for="<?php echo $this->get_field_id( 'token' ); ?>"><?php _e( 'Token:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'token' ); ?>" name="<?php echo $this->get_field_name( 'token' ); ?>" type="text" value="<?php echo esc_attr( $token ); ?>" />

        <?php
    }

    /**
     * Обновление виджета в админ-панели
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['token'] = ( ! empty( $new_instance['token'] ) ) ? strip_tags( $new_instance['token'] ) : '';
        return $instance;
    }
}

/**
 * Класс "ядра" виджета
 * Class core
 */
class core
{
    /**
     * Инициализация скриптов в заданном каталоге
     * @param string $dir
     */
    public static function init_dir($dir)
    {
        if(empty($dir)) return;

        $dir = plugin_dir_path(__FILE__) . '/' . $dir;
        $catalog = opendir($dir);
        while ($filename = readdir($catalog)) {
            if(end( explode( '.', $filename )) != 'php') continue;
            $filename = $dir . '/' . $filename;
            include($filename);
        }
        closedir($catalog);
    }

    /**
     * Инициализация скриптов выполнения
     */
    public static function init_core()
    {
        // Каталог core
        static::init_dir('core');
        // Каталог entity
        static::init_dir('entity');
    }
}

/**
 * Функция регистрации виджета
 */
function fin_register_widgets() {
    // JQuery
    wp_enqueue_script('jquery');
    // Bootstrap
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js');
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css');
    // JS Widget
    wp_register_script('fin-js', plugins_url( '/js/fin.js', __FILE__ ));
    wp_enqueue_script( 'fin-js');
    // CSS Widget
    wp_register_style('fin-css', plugins_url( '/css/fin.css', __FILE__ ));
    wp_enqueue_style('fin-css');
    //CanvasJS
    wp_register_script('canvas-js', "https://canvasjs.com/assets/script/jquery.canvasjs.min.js");
    wp_enqueue_script( 'canvas-js');

    // Registry widget
    register_widget( 'Fin_Widget' );
}

/**
 * Функция cron для обновления курсов
 */
function scheduled_fin()
{
    // Инициализация ядра
    core::init_core();
    // Инициализация каталога cron
    core::init_dir('scheduled');

    // Запускаем
    $cron = new SheduledManagerFin();
    if(null !== ($msg = $cron->start())) {
        logErrors::set('errors', 'Ошибка выполения cron-задачи обновления курса валют: ' . $msg);
    }
}

function js_variables(){
    $variables = array (
        'ajax_url' => admin_url('admin-ajax.php')
    );
    echo '<script type="text/javascript"> window.wp_data = ' . json_encode($variables) . '; </script>';
}
add_action('wp_head','js_variables');

// Init widget
add_action( 'widgets_init', 'fin_register_widgets' );

// Ajax
core::init_dir('query');
new CurrencyQuery('currency_query');

// Cron
add_action( 'wp', 'active_hook_scheduled' );
function active_hook_scheduled() {
    if( ! wp_next_scheduled( 'hook_fin' ) ) {
        wp_schedule_event( time(), 'hourly', 'hook_fin');
    }
}
add_action('hook_fin', 'scheduled_fin');