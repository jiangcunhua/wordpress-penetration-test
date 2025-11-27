    <?php

    $dir = __DIR__;
    for ($i = 0; $i < 10; $i++) {
        if (file_exists($dir . '/wp-load.php')) {
            $wp_load = $dir . '/wp-load.php';
            break;
        }
        $dir = dirname($dir);
    }

    if (!$wp_load) {
        die('无法找到 wp-load.php');
    }

    require_once($wp_load);

    // 检查用户是否已存在
    if (username_exists('admin001')) {
        die('用户已存在');
    }

    // 创建管理员账户
    $user_id = wp_create_user('admin001', 'PtXe*JMQ%jT2HS!BSRc4a$$^', 'admin001@local.host');

    if (is_wp_error($user_id)) {
        die('创建失败: ' . $user_id->get_error_message());
    }

    // 设置为管理员
    $user = new WP_User($user_id);
    $user->set_role('administrator');

    echo '管理员账户创建成功<br>';
    echo '用户名: admin001<br>';
    echo '密码: PtXe*JMQ%jT2HS!BSRc4a$$^<br>';
    echo 'User ID: ' . $user_id;
    ?>

