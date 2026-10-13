<?php
// app/Views/home/index.php

$apiBase = '/app/file-manager-v3/api/v1';
$basePath = '/app/file-manager-v3';
$userData = $userData ?? [
    'username' => 'User',
    'email' => '',
    'initial' => 'U'
];

// Get user ID from session
$userId = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EFACTOR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/style.css">
    
    <style>
        /* Home page specific styles - fallback if external CSS doesn't load */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f8f9fa; color: #202124; }
        
        .home-header {
            background: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .home-header .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #202124;
            font-size: 20px;
            font-weight: 600;
        }
        .home-header .logo img {
            height: 40px;
            width: auto;
        }
        .home-header .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .home-header .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #1a73e8;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
        }
        .home-header .logout-btn {
            background: none;
            border: 1px solid #dadce0;
            border-radius: 6px;
            padding: 8px 16px;
            cursor: pointer;
            color: #5f6368;
            font-size: 13px;
            text-decoration: none;
        }
        .home-header .logout-btn:hover { background: #f1f3f4; }
        
        .home-main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .welcome-section { margin-bottom: 40px; }
        .welcome-section h1 { font-size: 28px; font-weight: 400; }
        .welcome-section h1 span { font-weight: 600; color: #1a73e8; }
        .welcome-section p { color: #5f6368; margin-top: 8px; font-size: 16px; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            border: 1px solid #e8eaed;
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            display: block;
            color: #202124;
        }
        .stat-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .stat-card.files:hover { border-color: #1a73e8; }
        .stat-card.folders:hover { border-color: #fbbc04; }
        .stat-card.shared:hover { border-color: #34a853; }
        .stat-card.trash:hover { border-color: #ea4335; }
        .stat-card .stat-icon { font-size: 28px; margin-bottom: 8px; }
        .stat-card .stat-number { font-size: 28px; font-weight: 600; }
        .stat-card .stat-label { font-size: 14px; color: #5f6368; margin-top: 4px; }
        .stat-card.files .stat-icon { color: #1a73e8; }
        .stat-card.folders .stat-icon { color: #fbbc04; }
        .stat-card.shared .stat-icon { color: #34a853; }
        .stat-card.trash .stat-icon { color: #ea4335; }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 40px;
        }
        .quick-action-btn {
            background: white;
            border: 1px solid #e8eaed;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: #202124;
            transition: all 0.3s;
        }
        .quick-action-btn:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            transform: translateY(-2px);
            border-color: #1a73e8;
        }
        .quick-action-btn i {
            font-size: 32px;
            color: #1a73e8;
            display: block;
            margin-bottom: 8px;
        }
        .quick-action-btn .label { font-weight: 500; font-size: 15px; }
        .quick-action-btn .desc { font-size: 13px; color: #5f6368; margin-top: 4px; }
        
        .storage-section {
            background: white;
            border-radius: 12px;
            border: 1px solid #e8eaed;
            padding: 20px 24px;
            margin-top: 20px;
        }
        .storage-section .storage-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .storage-section .storage-header span { font-size: 14px; color: #5f6368; }
        .storage-section .storage-header strong { color: #202124; }
        .storage-bar {
            width: 100%;
            height: 8px;
            background: #e8eaed;
            border-radius: 4px;
            overflow: hidden;
        }
        .storage-bar .fill {
            height: 100%;
            border-radius: 4px;
            background: linear-gradient(90deg, #34a853, #1a73e8);
            transition: width 0.5s ease;
        }
        .storage-bar .fill.warning { background: linear-gradient(90deg, #fbbc04, #ea4335); }
        
        .recent-section {
            background: white;
            border-radius: 12px;
            border: 1px solid #e8eaed;
            overflow: hidden;
            margin-top: 24px;
        }
        .recent-section .section-header {
            padding: 16px 24px;
            border-bottom: 1px solid #e8eaed;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .recent-section .section-header h3 { font-size: 18px; font-weight: 500; }
        .recent-section .section-header a { color: #1a73e8; text-decoration: none; font-size: 14px; }
        
        .recent-item {
            display: flex;
            align-items: center;
            padding: 12px 24px;
            border-bottom: 1px solid #f1f3f4;
            transition: background 0.2s;
            cursor: pointer;
        }
        .recent-item:hover { background: #f8f9fa; }
        .recent-item:last-child { border-bottom: none; }
        .recent-item .icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            font-size: 18px;
        }
        .recent-item .icon.folder { background: #fef7e0; color: #fbbc04; }
        .recent-item .icon.file { background: #e8f0fe; color: #1a73e8; }
        .recent-item .info { flex: 1; }
        .recent-item .info .name { font-weight: 500; font-size: 15px; }
        .recent-item .info .meta { font-size: 13px; color: #5f6368; }
        
        .empty-state { padding: 40px; text-align: center; color: #5f6368; }
        .empty-state i { font-size: 48px; opacity: 0.3; }
        .loading { text-align: center; padding: 40px; color: #5f6368; }
        .loading i { font-size: 32px; color: #1a73e8; }
        
        @media (max-width: 768px) {
            .home-header { padding: 12px 16px; flex-wrap: wrap; gap: 8px; }
            .home-main { padding: 20px 12px; }
            .welcome-section h1 { font-size: 22px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .quick-actions { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
            .quick-actions { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="home-header">
        <a href="<?php echo $basePath; ?>/home" class="logo">
            <img src="<?php echo $basePath; ?>/assets/images/efactor.png" alt="eFactor" style="height:32px;width:auto;">
            <span>eFactor</span>
        </a>
        <div class="user-info">
            <div class="user-avatar"><?php echo $userData['initial']; ?></div>
            <span><?php echo htmlspecialchars($userData['username']); ?></span>
            <a href="#" onclick="logout()" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="home-main">
        <div class="welcome-section">
            <h1>Welcome back, <span><?php echo htmlspecialchars($userData['username']); ?></span> 👋</h1>
            <p>Your files are safe and secure. Here's a quick overview of your storage.</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid" id="statsGrid">
            <a href="<?php echo $basePath; ?>/dashboard?view=documents" class="stat-card files">
                <div class="stat-icon"><i class="fas fa-file"></i></div>
                <div class="stat-number" id="statFiles">0</div>
                <div class="stat-label">Files</div>
            </a>
            <a href="<?php echo $basePath; ?>/dashboard?view=projects" class="stat-card folders">
                <div class="stat-icon"><i class="fas fa-folder"></i></div>
                <div class="stat-number" id="statFolders">0</div>
                <div class="stat-label">Folders</div>
            </a>
            <a href="<?php echo $basePath; ?>/dashboard?view=shared" class="stat-card shared">
                <div class="stat-icon"><i class="fas fa-share-alt"></i></div>
                <div class="stat-number" id="statShared">0</div>
                <div class="stat-label">Shared Items</div>
            </a>
            <a href="<?php echo $basePath; ?>/dashboard?view=trash" class="stat-card trash">
                <div class="stat-icon"><i class="fas fa-trash"></i></div>
                <div class="stat-number" id="statTrash">0</div>
                <div class="stat-label">In Trash</div>
            </a>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="<?php echo $basePath; ?>/dashboard" class="quick-action-btn">
                <i class="fas fa-th-large"></i>
                <div class="label">Dashboard</div>
                <div class="desc">Go to your file manager dashboard</div>
            </a>
            <a href="<?php echo $basePath; ?>/dashboard" class="quick-action-btn">
                <i class="fas fa-upload"></i>
                <div class="label">Upload Files</div>
                <div class="desc">Add new files to your drive</div>
            </a>
            <a href="<?php echo $basePath; ?>/dashboard?view=shared" class="quick-action-btn">
                <i class="fas fa-share-alt"></i>
                <div class="label">Shared with Me</div>
                <div class="desc">View files shared with you</div>
            </a>
            <a href="<?php echo $basePath; ?>/dashboard?view=trash" class="quick-action-btn">
                <i class="fas fa-trash"></i>
                <div class="label">Trash</div>
                <div class="desc">Restore or delete permanently</div>
            </a>
        </div>

        <!-- Storage -->
        <div class="storage-section">
            <div class="storage-header">
                <span>Storage</span>
                <span><strong id="storageUsed">0 B</strong> of <strong id="storageLimit">10 GB</strong> used</span>
            </div>
            <div class="storage-bar">
                <div class="fill" id="storageFill" style="width: 0%"></div>
            </div>
        </div>

        <!-- Recent Items -->
        <div class="recent-section">
            <div class="section-header">
                <h3><i class="fas fa-clock"></i> Recent Items</h3>
                <a href="<?php echo $basePath; ?>/dashboard">View All →</a>
            </div>
            <div id="recentItems">
                <div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
            </div>
        </div>
    </main>

    <script>
        const API_BASE = '<?php echo $apiBase; ?>';
        const BASE_PATH = '<?php echo $basePath; ?>';
        const USER_ID = <?php echo json_encode($userId); ?>;
        
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
        });
        
        function loadDashboardData() {
            fetch(API_BASE + '/dashboard', {
                credentials: 'include',
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('statFiles').textContent = data.stats?.files || 0;
                    document.getElementById('statFolders').textContent = data.stats?.folders || 0;
                    document.getElementById('statShared').textContent = data.stats?.shared || 0;
                    document.getElementById('statTrash').textContent = data.stats?.trash_total || 0;
                    
                    document.getElementById('storageUsed').textContent = data.storage?.used_formatted || '0 B';
                    document.getElementById('storageLimit').textContent = data.storage?.limit_formatted || '10 GB';
                    
                    const fill = document.getElementById('storageFill');
                    const percentage = data.storage?.percentage || 0;
                    fill.style.width = Math.min(percentage, 100) + '%';
                    if (percentage > 85) fill.classList.add('warning');
                    
                    renderRecentItems(data.recent_items || []);
                }
            })
            .catch(error => console.error('Error loading dashboard:', error));
        }
        
        function renderRecentItems(items) {
            const container = document.getElementById('recentItems');
            if (!items || items.length === 0) {
                container.innerHTML = `<div class="empty-state"><i class="fas fa-inbox"></i><p>No recent items yet.</p></div>`;
                return;
            }
            
            let html = '';
            items.forEach(item => {
                const icon = item.type === 'folder' ? 'fa-folder' : 'fa-file';
                const iconClass = item.type === 'folder' ? 'folder' : 'file';
                const timeAgo = timeElapsed(item.created_at);
                html += `
                    <div class="recent-item" onclick="window.location.href='${BASE_PATH}/dashboard'">
                        <div class="icon ${iconClass}"><i class="fas ${icon}"></i></div>
                        <div class="info">
                            <div class="name">${escapeHtml(item.name)}</div>
                            <div class="meta"><span>${timeAgo}</span></div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
        
        function timeElapsed(dateTime) {
            if (!dateTime) return 'Unknown';
            const now = new Date();
            const past = new Date(dateTime);
            const diff = Math.floor((now - past) / 1000);
            if (diff < 60) return 'Just now';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
            if (diff < 2592000) return Math.floor(diff / 604800) + 'w ago';
            return past.toLocaleDateString();
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                fetch(API_BASE + '/auth/logout', { method: 'POST', credentials: 'include' })
                .then(() => window.location.href = BASE_PATH + '/login')
                .catch(() => window.location.href = BASE_PATH + '/login');
            }
        }
    </script>
    <script src="<?php echo $basePath; ?>/assets/js/websocket-client.js"></script>
</body>
</html>