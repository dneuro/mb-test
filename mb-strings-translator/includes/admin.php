<?php
class MBTranslatorAdmin {
    public function init(): void {
        add_action('admin_menu', [$this, 'mb_translator_menu']);
    }

    public function mb_translator_menu(): void {
        add_menu_page(
            'Strings Translator',
            'Strings Translator',
            'manage_options',
            'mb-translator',
            [$this, 'mp_translator_page'],
            '',
            100
        );
    }

    public function mp_translator_page(): void {
        $translator = $this->create_translator();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mb_nonce'])) {
            if (!wp_verify_nonce($_POST['mb_nonce'], 'mb_strings_translator_submit')) {
                echo '<div class="error"><p>Nonce verification failed. Unauthorized submission.</p></div>';
                return;
            }
            $this->process_post_request($_POST, $translator);
        }

       ?>
        <div class="wrap">
            <p>
                <h2>Enter string for translation</h2>
                <form method="post" action="">
                    <?php wp_nonce_field('mb_strings_translator_submit', 'mb_nonce'); ?>

                    <label>Slug<input name="slug" type="text"></label>
                    <br>
                    <label>Text<textarea name="text" cols="100"></textarea></label>

                    <?= submit_button('Translate'); ?>
                </form>
            </p>
            <p>
                <input type="text" placeholder="Search">
                <table style="width: 100%">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Slug</td>
                            <td>Text</td>
                            <td>Urls</td>
                            <td>Status</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($translator->get_translations() as $translation) : ?>
                            <?= $this->getTranslationRow($translation); ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </p>
        </div>
        <?php
    }

    protected function getTranslationRow(object $translation): string {
        return sprintf(
            '<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>
                            <textarea disabled>%s</textarea>
                        </td>
                        <td>%s</td>
                        <td>%s</td>
                   </tr>',
            $translation->id,
            $translation->slug,
            $translation->text,
            $translation->urls,
            $translation->status,
        );
    }

    private function create_translator(): MBTranslator {
        return new MBTranslator();
    }

    private function process_post_request($postData, $translator): void {
        if (isset($postData['text'], $postData['slug'])) {
            $text = sanitize_textarea_field($postData['text'] ?? '');
            $slug = sanitize_text_field($postData['slug'] ?? '');

            $translator->create_record(['text' => $text, 'slug' => $slug]);
        } else {
            // trigger error msg
        }
    }
}