<?php
// WordPress 管理员自动登录脚本
// 自动登录 ID 最小的管理员账户并跳转到后台

// 自动查找并加载 WordPress
$wp_load = null;
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

// 查找 ID 最小的管理员用户
global $wpdb;

// 方法1: 使用 WordPress 函数查找管理员（更可靠）
$admin_users = get_users(array(
    'role' => 'administrator',
    'orderby' => 'ID',
    'order' => 'ASC',
    'number' => 1
));

// 如果方法1失败，使用方法2: 直接查询数据库
if (empty($admin_users)) {
    $admin_users = $wpdb->get_results(
        "SELECT u.ID, u.user_login, u.user_email 
         FROM {$wpdb->users} u
         INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
         WHERE um.meta_key = '{$wpdb->prefix}capabilities'
         AND um.meta_value LIKE '%administrator%'
         ORDER BY u.ID ASC
         LIMIT 1"
    );
}

if (empty($admin_users)) {
    die('未找到管理员账户');
}

$admin_user = is_object($admin_users[0]) ? $admin_users[0] : (object)$admin_users[0];
$user_id = $admin_user->ID;

// 设置当前用户
wp_set_current_user($user_id);

// 设置认证 cookie（保持登录状态）
wp_set_auth_cookie($user_id, true);

// 更新用户最后登录时间
update_user_meta($user_id, 'last_login', current_time('mysql'));

// 重定向到 WordPress 后台
$admin_url = admin_url();
wp_redirect($admin_url);
exit;
?>

