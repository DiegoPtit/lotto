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

// Register Font Awesome for social icons
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

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h4><?= Html::encode(Yii::$app->name) ?></h4>
            <button class="btn-close-sidebar" onclick="toggleSidebar()" aria-label="Cerrar menú">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <?= Html::a('<i class="fas fa-home"></i> <span>Inicio</span>', ['/site/index'], ['class' => 'sidebar-link']) ?>
            <?= Html::a('<i class="fas fa-info-circle"></i> <span>Acerca de</span>', ['/site/about'], ['class' => 'sidebar-link']) ?>
            <?= Html::a('<i class="fas fa-envelope"></i> <span>Contacto</span>', ['/site/contact'], ['class' => 'sidebar-link']) ?>

            <hr class="sidebar-divider">


        </nav>
    </aside>

    <!-- Header -->
    <header id="header" class="main-header">
        <div class="header-container">
            <div class="header-brand">
                <?= Html::a(Html::encode(Yii::$app->name), Yii::$app->homeUrl, ['class' => 'brand-link']) ?>
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

    <!-- Brand Footer -->
    <footer id="brand-footer" class="brand-footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h3 class="footer-brand-title"><?= Html::encode(Yii::$app->name) ?></h3>
                    <p class="footer-brand-description">
                        <?= Html::encode(Yii::$app->name) ?> es tu espacio para participar en sorteos confiables,
                        emocionantes y seguros. Creemos en la transparencia y en darte la oportunidad de ganar
                        premios increíbles sin riesgos.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h4 class="footer-social-title">Síguenos</h4>
                    <div class="footer-social-links">
                        <a href="#" class="social-link social-facebook" aria-label="Facebook" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link social-instagram" aria-label="Instagram" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link social-tiktok" aria-label="TikTok" title="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <?php
    // Modal de Términos y Condiciones
    if (isset($this->params['showTermsModal']) && $this->params['showTermsModal']) {
        $latestTerms = \app\models\TermsVersions::getLatestTerms();
        if ($latestTerms) {
            ?>
            <!-- Modal de Términos y Condiciones -->
            <div id="termsModal" class="terms-modal" data-version="<?= Html::encode($latestTerms->version) ?>"
                data-terms-id="<?= $latestTerms->id ?>">
                <div class="terms-modal-content">
                    <div class="terms-modal-header">
                        <h3 class="terms-modal-title"><?= Html::encode($latestTerms->titulo) ?></h3>
                        <span class="terms-modal-close" onclick="closeTermsModal()">&times;</span>
                    </div>
                    <div class="terms-modal-body">
                        <div class="terms-version-info">
                            <small>Versión: <?= Html::encode($latestTerms->version) ?> |
                                Actualizado: <?= Yii::$app->formatter->asDate($latestTerms->created_at, 'long') ?></small>
                        </div>
                        <div id="termsContent" class="terms-content">
                            <?= $latestTerms->contenido ?>
                        </div>
                        <button id="readMoreBtn" class="btn-read-more" onclick="toggleReadMore()">
                            Leer más <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="terms-modal-footer">
                        <button class="btn-accept-terms" onclick="acceptTerms()">
                            Aceptar Términos y Condiciones
                        </button>
                    </div>
                </div>
            </div>

            <style>
                /* Estilos del Modal */
                .terms-modal {
                    display: none;
                    position: fixed;
                    z-index: 9999;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    overflow: auto;
                    background-color: rgba(0, 0, 0, 0.6);
                    backdrop-filter: blur(5px);
                    animation: fadeIn 0.3s ease;
                }

                .terms-modal.show {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .terms-modal-content {
                    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
                    margin: 20px;
                    padding: 0;
                    border-radius: 12px;
                    max-width: 700px;
                    width: 90%;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                    animation: slideDown 0.4s ease;
                }

                .terms-modal-header {
                    padding: 20px 25px;
                    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                    color: white;
                    border-radius: 12px 12px 0 0;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .terms-modal-title {
                    margin: 0;
                    font-size: 1.5rem;
                    font-weight: bold;
                }

                .terms-modal-close {
                    color: white;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: transform 0.2s;
                }

                .terms-modal-close:hover {
                    transform: scale(1.2);
                }

                .terms-modal-body {
                    padding: 25px;
                    max-height: 500px;
                    overflow-y: auto;
                }

                .terms-version-info {
                    margin-bottom: 15px;
                    color: #6c757d;
                    font-size: 0.9rem;
                }

                .terms-content {
                    max-height: 300px;
                    overflow: hidden;
                    position: relative;
                    transition: max-height 0.4s ease;
                    margin-bottom: 15px;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                    white-space: normal;
                }

                .terms-content.expanded {
                    max-height: none;
                }

                .terms-content::after {
                    content: '';
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    height: 60px;
                    background: linear-gradient(to bottom, transparent, white);
                    pointer-events: none;
                    transition: opacity 0.3s ease;
                }

                .terms-content.expanded::after {
                    opacity: 0;
                }

                .btn-read-more {
                    background: linear-gradient(135deg, #28a745 0%, #20833a 100%);
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 0.95rem;
                    transition: all 0.3s ease;
                    display: block;
                    margin: 0 auto;
                }

                .btn-read-more:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
                }

                .btn-read-more i {
                    transition: transform 0.3s ease;
                }

                .btn-read-more.expanded i {
                    transform: rotate(180deg);
                }

                .terms-modal-footer {
                    padding: 20px 25px;
                    background: #f8f9fa;
                    border-radius: 0 0 12px 12px;
                    text-align: center;
                }

                .btn-accept-terms {
                    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 1rem;
                    font-weight: bold;
                    transition: all 0.3s ease;
                }

                .btn-accept-terms:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                    }

                    to {
                        opacity: 1;
                    }
                }

                @keyframes slideDown {
                    from {
                        transform: translateY(-50px);
                        opacity: 0;
                    }

                    to {
                        transform: translateY(0);
                        opacity: 1;
                    }
                }

                /* Estilos para el contenido HTML */
                .terms-content h1,
                .terms-content h2,
                .terms-content h3 {
                    margin-top: 1rem;
                    margin-bottom: 0.5rem;
                    color: #333;
                }

                .terms-content p {
                    margin-bottom: 1rem;
                    line-height: 1.6;
                    color: #555;
                }

                .terms-content ul,
                .terms-content ol {
                    margin-bottom: 1rem;
                    padding-left: 1.5rem;
                }

                .terms-content li {
                    margin-bottom: 0.5rem;
                }

                .terms-content strong {
                    font-weight: bold;
                    color: #333;
                }

                .terms-content em {
                    font-style: italic;
                }
            </style>

            <script>
                // Gestión de Términos y Condiciones con localStorage
                const TermsManager = {
                    storageKey: 'lotto_terms_accepted',

                    getAcceptedTerms() {
                        try {
                            const data = localStorage.getItem(this.storageKey);
                            return data ? JSON.parse(data) : null;
                        } catch (e) {
                            return null;
                        }
                    },

                    saveAcceptance(version, termsId) {
                        const data = {
                            version: version,
                            termsId: termsId,
                            acceptedAt: new Date().toISOString()
                        };
                        localStorage.setItem(this.storageKey, JSON.stringify(data));
                    },

                    hasAcceptedCurrentVersion(currentVersion) {
                        const accepted = this.getAcceptedTerms();
                        if (!accepted) return false;
                        return accepted.version === currentVersion;
                    },

                    isAccepted() {
                        const termsModal = document.getElementById('termsModal');
                        if (!termsModal) return true; // No hay modal, asumir aceptado
                        const currentVersion = termsModal.dataset.version;
                        return this.hasAcceptedCurrentVersion(currentVersion);
                    }
                };

                // Exponer globalmente para uso en otros scripts
                window.TermsManager = TermsManager;

                // Mostrar el modal al cargar la página si no ha aceptado
                document.addEventListener('DOMContentLoaded', function () {
                    const termsModal = document.getElementById('termsModal');
                    if (termsModal) {
                        const currentVersion = termsModal.dataset.version;

                        // Solo mostrar si no ha aceptado la versión actual
                        if (!TermsManager.hasAcceptedCurrentVersion(currentVersion)) {
                            setTimeout(() => {
                                termsModal.classList.add('show');
                                checkContentHeight();
                            }, 500);
                        }
                    }
                });

                function closeTermsModal() {
                    const termsModal = document.getElementById('termsModal');
                    termsModal.classList.remove('show');
                }

                function toggleReadMore() {
                    const termsContent = document.getElementById('termsContent');
                    const readMoreBtn = document.getElementById('readMoreBtn');

                    termsContent.classList.toggle('expanded');
                    readMoreBtn.classList.toggle('expanded');

                    if (termsContent.classList.contains('expanded')) {
                        readMoreBtn.innerHTML = 'Leer menos <i class="fas fa-chevron-up"></i>';
                    } else {
                        readMoreBtn.innerHTML = 'Leer más <i class="fas fa-chevron-down"></i>';
                    }
                }

                function checkContentHeight() {
                    const termsContent = document.getElementById('termsContent');
                    const readMoreBtn = document.getElementById('readMoreBtn');

                    if (termsContent && readMoreBtn) {
                        if (termsContent.scrollHeight <= 300) {
                            readMoreBtn.style.display = 'none';
                            termsContent.style.maxHeight = 'none';
                        }
                    }
                }

                function acceptTerms() {
                    const termsModal = document.getElementById('termsModal');
                    const version = termsModal.dataset.version;
                    const termsId = termsModal.dataset.termsId;

                    // Guardar en localStorage
                    TermsManager.saveAcceptance(version, termsId);

                    // Cerrar el modal
                    closeTermsModal();
                }

                // Cerrar modal al hacer clic fuera de él
                window.addEventListener('click', function (event) {
                    const termsModal = document.getElementById('termsModal');
                    if (event.target == termsModal) {
                        closeTermsModal();
                    }
                });
            </script>
            <?php
        }
    }
    ?>

    <!-- App Footer -->

    <footer id="app-footer" class="app-footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="footer-links">
                        <?= Html::a('Términos y Condiciones', ['/site/politics', 'type' => 'terms'], ['class' => 'footer-link']) ?>
                        <span class="footer-separator">|</span>
                        <?= Html::a('Política de Privacidad', ['/site/politics', 'type' => 'privacy'], ['class' => 'footer-link']) ?>
                        <span class="footer-separator">|</span>
                        <?= Html::a('Política de Cookies', ['/site/politics', 'type' => 'cookies'], ['class' => 'footer-link']) ?>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-user-actions mb-2">
                        <?php if (Yii::$app->user->isGuest): ?>
                            <?= Html::a('<i class="fas fa-sign-in-alt"></i> Iniciar Sesión', ['/site/login'], ['class' => 'footer-link']) ?>
                        <?php else: ?>
                            <span class="me-2 text-secondary">
                                <i class="fas fa-user-circle"></i> <?= Html::encode(Yii::$app->user->identity->nombre) ?>
                            </span>
                            <span class="footer-separator">|</span>
                            <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline-block ms-1']) ?>
                            <button type="submit" class="footer-link bg-transparent border-0 p-0">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </button>
                            <?= Html::endForm() ?>
                        <?php endif; ?>
                    </div>
                    <div class="footer-copyright">
                        &copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?>. Todos los derechos reservados.
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