<?php

namespace WP2Static;

class CrawlCache {

    public static function createTable() : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_crawl_cache';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            hashed_url CHAR(32) NOT NULL,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (hashed_url)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    public static function addUrl( string $url ) : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_crawl_cache';

        $wpdb->insert(
            $table_name,
            array(
                'time' => current_time( 'mysql' ),
                'hashed_url' => md5( $url ),
            )
        );
    }

    // TODO: enable date filter as option/alternate method
    public static function getUrl( string $url ) : string {
        global $wpdb;

        $hashed_url = md5( $url );

        $table_name = $wpdb->prefix . 'wp2static_crawl_cache';

        $sql = $wpdb->prepare(
            "SELECT hashed_url FROM $table_name WHERE" .
            ' hashed_url = %s LIMIT 1',
            $hashed_url
        );

        $id = $wpdb->get_var( $sql );

        return (string) $id;
    }

    public static function rmUrl( string $url ) : void {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp2static_crawl_cache';

        $wpdb->delete(
            $table_name,
            [
                'hashed_url' => md5( $url ),
            ]
        );
    }
}
