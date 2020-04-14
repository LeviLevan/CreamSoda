<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'creamsoda' );

/** Имя пользователя MySQL */
define( 'DB_USER', 'root' );

/** Пароль к базе данных MySQL */
define( 'DB_PASSWORD', 'admin' );

/** Имя сервера MySQL */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'e^>tyi~=QiUpT(%LGtY)MLCRD9H_1xT_iUZWyo$Q,c4/fxvOvOd:!};<R<*4aq;j' );
define( 'SECURE_AUTH_KEY',  'T=:2L6^YVbw(:r>=:~::^$eg_yLU+B9xbo,Hxppk#n+uX}NEpZK):{D0HkRs.39k' );
define( 'LOGGED_IN_KEY',    'o^8:ih]TFt@[FHp$Qu._:$9CzVLb$].cVk5,(UX/Ni+6>`%|@>-o`BF#l>Q|=I.:' );
define( 'NONCE_KEY',        '~|C9TKnS 1B*Q?IKr>7Hub5#2*9SjO4,oP`9Tl%N3YPgY }`D!-}RM%cQ`1FTw$@' );
define( 'AUTH_SALT',        '@DG}luSl+URz/p-Su$@x_TG,xW[S,%M8W$Lkz~0nT` 8GY@^.vRMr7y1Gre8bR.=' );
define( 'SECURE_AUTH_SALT', ':t+mYm&CLwV%/^DACk(wO6c}K!D%GrYp#bZrPuy*>F?C:*1kW&-/E-A&^!$*[.L,' );
define( 'LOGGED_IN_SALT',   ';])b.PD$|!.Hkr;9FZoKZ0fDXp.-*4h/n1j~yAJK%D<e9.IX~OVB_r4?.&3G/{dh' );
define( 'NONCE_SALT',       'SDMWyr|8T0Z`-YSU<(*9|e0s`928W-%KkcQw-ZSe8&POFzm6r:b0#4!/ nz$b!k-' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once( ABSPATH . 'wp-settings.php' );
