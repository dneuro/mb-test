<?php
class MBTranslatorCron
{
    public function init(): void {
        add_action('init', [$this, 'add_mb_translator_cron_job']);
        add_action('mb_process_strings_translation', [$this, 'process_strings_translation']);
    }

    public function add_mb_translator_cron_job(): void {
        if (!wp_next_scheduled( 'mb_process_strings_translation')) {
            $timestamp = strtotime('today midnight');
            wp_schedule_event( $timestamp, 'daily', 'mb_process_strings_translation' );
        }
    }

    public function process_strings_translation(): void {
        $translator = $this->create_translator();

        // TODO
        // $pendingRecords = find all records with status pending
        // foreach($pendingRecords as $record) {
        //      $translatedStrings = $translator->request_weglot_translate();
        //      if ($translatedStrings) {
        //          $translator->update_record_status($recordId, 'done');
        //          $translator->add_translated_strings($recordId, $translatedStrings);
        //      }
        //  }
        //

    }

    private function create_translator(): MBTranslator {
        return new MBTranslator();
    }
}