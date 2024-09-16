<?php

class MBTranslator
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_DONE = 'done';
    public const STATUS_ERROR = 'error';

    private const SUPPORTED_LANGUAGES = [
        'pt-br' => 'Brazilian Portuguese',
        'nl' => 'Dutch',
        'fr' => 'French',
        'de' => 'German',
        'it' => 'Italian',
        'ja' => 'Japanese',
        'pl' => 'Polish',
        'pt' => 'Portuguese',
        'es' => 'Spanish',
        'ru' => 'Russian',
    ];
    private const DEFAULT_LANGUAGE = 'en';

    private string $tableName;

    public function __construct() {
        global $wpdb;
        $this->tableName =  $wpdb->prefix . 'mb_translations';
    }

    public function get_translations(): array {
        global $wpdb;

        return $wpdb->get_results("
            SELECT tr.*, GROUP_CONCAT(tr_children.url) AS urls
            FROM " . $this->tableName . " AS tr
            LEFT JOIN " . $this->tableName . " AS tr_children
                ON tr.id = tr_children.parent_id
            WHERE tr.parent_id IS NULL
            GROUP BY tr.id;"
        );
    }

    public function create_record(array $data): void {
        global $wpdb;
        // data validation here

        $slug = sanitize_text_field($data['slug']);
        $lang = isset($data['lang']) ? sanitize_text_field($data['lang']) : self::DEFAULT_LANGUAGE;

        $wpdb->insert(
            $this->tableName,
            array_filter([
                'status' => self::STATUS_PENDING,
                'text' => sanitize_text_field($data['text'] ?? ''),
                'slug' => $slug,
                'parent_id' => sanitize_text_field($data['parent_id'] ?? ''),
                'lang' => $lang,
                'url' => $this->get_string_url($slug, $lang)
            ])
        );
    }

    public function update_record_status(int $recordId, string $status): bool {
        global $wpdb;

        return $wpdb->update(
            $this->tableName,
            ['status' => sanitize_text_field($status)],
            ['id' => $recordId]
        );
    }

    public function request_weglot_translate(string $text): array {
        // do api call
        return [];
    }

    public function add_translated_strings(int $recordId, array $translatedStrings): void {
        foreach ($translatedStrings as $translatedString) {
            $this->create_record([
                'text' => $translatedString['text'] ?? '',
                'slug' => $translatedString['slug'] ?? '',
                'parent_id' => $recordId,
                'lang' => $translatedString['lang'] ?? '',
            ]);
        }
    }

    public function find_record_url_by_slug(string $slug, string $lang = 'all'): array {
        if ($lang !== 'all' && !$this->validate_language($lang)) {
            // trigger error msg
            return [];
        }
        $where = "tr.url IS NOT NULL AND tr.slug = '{$slug}'";
        if ($lang !== 'all') {
            $where .= " AND tr.lang = '{$lang}'";
        }

        global $wpdb;
        $results = $wpdb->get_results("
            SELECT tr.lang, tr.url
            FROM " . $this->tableName . " AS tr
            WHERE " . $where
        );

        return array_reduce($results, function ($r, $i) { $r[$i->lang] = $i->url; return $r; }, []);
    }

    public function create_translations_table(): void {
        global $wpdb;

        $sql = "CREATE TABLE IF NOT EXISTS " . $this->tableName . " (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            slug varchar(255),
            text tinytext NOT NULL,
            url varchar(2083),
            lang varchar(10) NOT NULL DEFAULT 'en',
            status varchar(10) NOT NULL,
            parent_id bigint(20) unsigned,
            PRIMARY KEY (id),
            UNIQUE KEY unique_slug_lang (slug, lang)
        );";

        $wpdb->query($sql);
    }

    public function drop_translations_table(): void {
        global $wpdb;
        $wpdb->query('DROP TABLE IF EXISTS ' . $this->tableName);
    }

    private function get_api_key(): string {
        // get api key from weglot key
        return '';
    }

    private function validate_language(string $lang): bool {
        return isset(self::SUPPORTED_LANGUAGES[$lang]);
    }

    private function get_string_url(string $slug, string $lang): string {
        // mb-translator/translation/hello/de
        return sprintf('%s/%s/%s/', rest_url('/mb-translator/translation'), $slug, $lang);
    }
}