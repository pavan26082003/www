<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat - User List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --hover-color: #2e59d9;
            --bg-color: #f8f9fc;
            --card-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        body {
            background-color: var(--bg-color);
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
        }
        
        .chat-container {
            height: calc(100vh - 120px);
            overflow-y: auto;
        }
        
        .user-card {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
            border-left: 3px solid var(--primary-color);
        }
        
        .user-card.active {
            background-color: #f0f5ff;
            border-left: 3px solid var(--primary-color);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }
        
        .unread-count {
            font-size: 0.75rem;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box i {
            position: absolute;
            top: 12px;
            left: 15px;
            color: #d1d3e2;
        }
        
        .search-input {
            padding-left: 40px;
            border-radius: 10rem;
            background-color: #f8f9fc;
            border: 1px solid #d1d3e2;
        }
        
        .last-message {
            font-size: 0.85rem;
            color: #6c757d;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .timestamp {
            font-size: 0.75rem;
            color: #adb5bd;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-primary text-white p-0">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="#">
                                <i class="fas fa-comments me-2"></i> Chats
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">
                                <i class="fas fa-users me-2"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">
                                <i class="fas fa-cog me-2"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Chat Users</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i> New Chat
                        </button>
                    </div>
                </div>

                <!-- Search Box -->
                <div class="mb-4">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control search-input" placeholder="Search users...">
                    </div>
                </div>

                <!-- User List -->
                <div class="card shadow mb-4">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush chat-container">
                            <?php foreach ($users as $user): ?>
                                <a href="<?= site_url('admin/chat/'.$user->id) ?>" 
                                   class="list-group-item list-group-item-action user-card <?= ($active_user == $user->id) ? 'active' : '' ?>">
                                    <div class="d-flex align-items-center">
                                        <img src="<?= $user->image ? base_url($user->image) : 'https://ui-avatars.com/api/?name='.urlencode($user->first_name.' '.$user->last_name).'&background=random' ?>" 
                                             class="rounded-circle user-avatar me-3">
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">
                                                    <?= htmlspecialchars($user->first_name.' '.$user->last_name) ?>
                                                    <?php if($user->user_type == 'Brand'): ?>
                                                        <span class="badge bg-info ms-1">Brand</span>
                                                    <?php endif; ?>
                                                </h6>
                                                <small class="timestamp">
                                                    <?= date('h:i A', strtotime($user->last_message_time ?? 'now')) ?>
                                                </small>
                                            </div>
                                            <p class="mb-0 last-message">
                                                <?php if(!empty($user->last_message)): ?>
                                                    <?= strlen($user->last_message) > 50 ? substr(htmlspecialchars($user->last_message), 0, 50).'...' : htmlspecialchars($user->last_message) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No messages yet</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <?php if($user->unread_count > 0): ?>
                                            <span class="badge bg-danger rounded-pill unread-count">
                                                <?= $user->unread_count ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple search functionality
        document.querySelector('.search-input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.user-card').forEach(card => {
                const userName = card.querySelector('h6').textContent.toLowerCase();
                card.style.display = userName.includes(searchTerm) ? 'block' : 'none';
            });
        });

        // Mark as read when clicked
        document.querySelectorAll('.user-card').forEach(card => {
            card.addEventListener('click', function() {
                const unreadBadge = this.querySelector('.unread-count');
                if (unreadBadge) {
                    unreadBadge.remove();
                    // Here you would typically make an AJAX call to mark messages as read
                }
            });
        });
    </script>
</body>
</html>