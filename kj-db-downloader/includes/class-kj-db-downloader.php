<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

class KJ_DB_Downloader {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_download_db', array($this, 'download_db'));
    }

    public function add_admin_menu() {
        add_menu_page(
            __('KJ DB Downloader', 'kj-db-downloader'),
            __('KJ DB Downloader', 'kj-db-downloader'),
            'manage_options',
            'kj-db-downloader',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Download Database', 'kj-db-downloader'); ?></h1>
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
                <input type="hidden" name="action" value="download_db">
                <?php submit_button(__('Download Database', 'kj-db-downloader')); ?>
            </form>
        </div>
        <?php
    }

    public function download_db() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized user', 'kj-db-downloader'));
        }

        global $wpdb;
        $dbname = $wpdb->dbname;
        $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);
        
        $sql = '';
        foreach ($tables as $table) {
            $table_name = $table[0];
            $table_create = $wpdb->get_row("SHOW CREATE TABLE $table_name", ARRAY_N);
            $sql .= $table_create[1] . ";\n\n";
            
            $table_data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_N);
            foreach ($table_data as $row) {
                $sql .= "INSERT INTO $table_name VALUES(";
                $values = [];
                foreach ($row as $value) {
                    $values[] = is_null($value) ? 'NULL' : '"' . esc_sql($value) . '"';
                }
                $sql .= implode(', ', $values) . ");\n";
            }
            $sql .= "\n\n";
        }

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment;filename="' . $dbname . '.sql"');
        echo $sql;
        exit;
    }
}
