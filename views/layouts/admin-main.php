<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

// Register Font Awesome for icons
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [
    'integrity' => 'sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==',
    'crossorigin' => 'anonymous',
    'referrerpolicy' => 'no-referrer'
]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Admin Sidebar -->
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h4><?= Html::encode(Yii::$app->name) ?> Admin</h4>
            <button class="btn-close-sidebar" onclick="toggleSidebar()" aria-label="Cerrar menú">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <?= Html::a('<i class="fas fa-home"></i> <span>Inicio</span>', ['panel/index'], ['class' => 'sidebar-link']) ?>
            <?= Html::a('<i class="fas fa-ticket-alt"></i> <span>Administrar Rifas</span>', ['rifas/index'], ['class' => 'sidebar-link']) ?>
            <?= Html::a('<i class="fas fa-comments"></i> <span>Administrar Testimonios</span>', ['testimonios/index'], ['class' => 'sidebar-link']) ?>
            <?= Html::a('<i class="fas fa-users"></i> <span>Administrar Usuarios</span>', ['usuarios/index'], ['class' => 'sidebar-link']) ?>
            <?= Html::a('<i class="fas fa-credit-card"></i> <span>Administrar Métodos de Pago</span>', ['metodos-pago/index'], ['class' => 'sidebar-link']) ?>
            <?= Html::a('<i class="fas fa-file-contract"></i> <span>Administrar Políticas de Uso</span>', ['politicas/index'], ['class' => 'sidebar-link']) ?>
            <?= Html::a('<i class="fas fa-trophy"></i> <span>Histórico de Ganadores</span>', ['ganadores/index'], ['class' => 'sidebar-link']) ?>

            <hr class="sidebar-divider">

        </nav>
    </aside>

    <!-- Header -->
    <header id="header" class="main-header">
        <div class="header-container">
            <div class="header-brand">
                <?= Html::a(Html::encode(Yii::$app->name) . ' Admin', ['/panel/index'], ['class' => 'brand-link']) ?>
            </div>
            <button class="btn-hamburger" onclick="toggleSidebar()" aria-label="Abrir menú">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main id="main" class="flex-shrink-0 main-content" role="main">
        <div class="container">
            <?= $content ?>
        </div>
    </main>

    <!-- App Footer -->
    <footer id="app-footer" class="app-footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="footer-copyright">
                        Lotto Admin Panel V1.0.0
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-copyright">
                        &copy; <a href="https://github.com/DiegoPtit" target="_blank" rel="noopener noreferrer"
                            class="footer-link">
                            <i class="fab fa-github"></i> github.com/DiegoPtit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const body = document.body;

            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            body.classList.toggle('sidebar-open');
        }

        // Close sidebar when clicking a link (optional, for better UX on mobile)
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarLinks = document.querySelectorAll('.sidebar-link:not(.sidebar-logout)');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 992) { // Only auto-close on mobile/tablet
                        toggleSidebar();
                    }
                });
            });
        });
    </script>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>