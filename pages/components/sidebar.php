<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Menú para admin (único rol)
$menu = [
        'principal' => [
                ['name' => 'Dashboard', 'page' => 'dashboard', 'icon' => 'home'],
                ['name' => 'Horarios', 'page' => 'horarios', 'icon' => 'calendar'],
        ],
        'gestión' => [
                ['name' => 'Docentes', 'page' => 'docentes', 'icon' => 'user'],
                ['name' => 'Carreras', 'page' => 'carreras', 'icon' => 'briefcase'],
                ['name' => 'Materias', 'page' => 'materias', 'icon' => 'book'],
                ['name' => 'Alumnos', 'page' => 'alumnos', 'icon' => 'users'],
                ['name' => 'Grupos', 'page' => 'grupos', 'icon' => 'users'],
                ['name' => 'Aulas', 'page' => 'aulas', 'icon' => 'building'],
        ]
];

// SVG Icons
$icons = [
        'home' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>',
        'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
        'book' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>',
        'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>',
        'building' => '<rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M12 6h.01"></path><path d="M12 10h.01"></path><path d="M12 14h.01"></path><path d="M16 10h.01"></path><path d="M16 14h.01"></path><path d="M8 10h.01"></path><path d="M8 14h.01"></path>',
];
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                </svg>
            </div>
            <div class="sidebar-logo-text">
                <h2>Horarios</h2>
                <p>Administrador</p>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($menu as $section => $items): ?>
            <div class="nav-section">
                <div class="nav-section-title"><?php echo ucfirst($section); ?></div>
                <?php foreach ($items as $item): ?>
                    <a href="<?php echo $item['page']; ?>.php" class="nav-link <?php echo $current_page === $item['page'] ? 'active' : ''; ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <?php echo $icons[$item['icon']]; ?>
                        </svg>
                        <span><?php echo $item['name']; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">A</div>
            <div class="user-info">
                <span class="user-name">Admin Sistema</span>
            </div>
            <button class="btn-logout" onclick="logout()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </button>
        </div>
    </div>
</aside>