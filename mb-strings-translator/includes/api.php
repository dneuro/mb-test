<?php

class MBTranslatorApi
{
    public function init(): void {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void {
        register_rest_route(
            'mb-translator/',
            '/translation/(?P<slug>[a-zA-Z]+)/(?P<lang>[a-zA-Z]+)',
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_translation'],
            ]
        );
    }

    public function get_translation(WP_REST_Request $request): WP_REST_Response {
        $lang = sanitize_text_field($request->get_param('lang'));
        $slug = sanitize_text_field($request->get_param('slug')) ?: 'all';

        $translator = $this->create_translator();

        $response = new WP_REST_Response($translator->find_record_url_by_slug($slug, $lang));

        $response->set_status(200);
        $response->header('Content-Type', 'application/json');

        return $response;
    }

    private function create_translator(): MBTranslator {
        return new MBTranslator();
    }
}