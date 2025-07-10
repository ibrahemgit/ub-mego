<?php 

/* google sheet script */
add_action('admin_notices', function() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e('<b style="font-size:25px">Hello Abdelmajeed Team</b>'); ?></p>
    </div>
    <?php
});

add_action('wp_ajax_submit_to_google_form_action', 'handle_submit_to_google_form');
add_action('wp_ajax_nopriv_submit_to_google_form_action', 'handle_submit_to_google_form');
function handle_submit_to_google_form() {
    $name  = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $url   = isset($_POST['url']) ? esc_url_raw($_POST['url']) : '';
    $zone   = isset($_POST['zone']) ? sanitize_text_field($_POST['zone']) : '';

    if (empty($name) || empty($phone) || empty($title) || empty($url)) {
        wp_send_json_error('الرجاء تعبئة جميع الحقول.');
    }

    if (submit_to_google_form($name, $phone, $title, $url, $zone)) {
        wp_send_json_success();
    } else {
        wp_send_json_error('فشل في الإرسال. يرجى المحاولة لاحقًا.');
    }

    wp_die();
}

function submit_to_google_form($email, $phone, $title, $url, $zone) {
    $form_url = 'https://docs.google.com/forms/d/e/1FAIpQLScdtBEgLd5vqZmnhJPhNklCgK-t0Y3WyZWCvQJK8biVTzfEtg/formResponse';

    $post_fields = array(
        'entry.885653522' => $email,
        'entry.479861034' => $phone,
        'entry.564818666' => $title,
        'entry.978247542' => $url,
        'entry.1046139998' => $zone,   // إضافة حقل الـ zone
    );

    $post_fields = array_map('sanitize_text_field', $post_fields);

    $response = wp_remote_post($form_url, array(
        'body' => $post_fields,
        'timeout' => 15,
        'blocking' => true
    ));

    if (is_wp_error($response)) {
        error_log('❌ خطأ في إرسال Google Form: ' . $response->get_error_message());
        return false;
    }

    return true;
}
?>
