<?php

/** @var yii\web\View $this */
/** @var app\models\Testimonios[] $testimonios */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Administrar Testimonios';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    /* ==================== ADMIN TESTIMONIOS STYLES ==================== */
    .admin-testimonios-page {
        padding: 30px 0;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 35px;
    }

    .page-header h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-header h1 i {
        color: #0066cc;
    }

    .btn-crear-testimonio {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #28a745 0%, #20833a 100%);
        color: #ffffff;
        padding: 14px 28px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }

    .btn-crear-testimonio:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        color: #ffffff;
        text-decoration: none;
    }

    /* Grid de testimonios admin */
    .testimonios-admin-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
    }

    /* Tarjeta de testimonio admin */
    .testimonio-admin-card {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .testimonio-admin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
    }

    /* Media thumbnail */
    .testimonio-admin-media {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 10;
        overflow: hidden;
    }

    .testimonio-admin-media img,
    .testimonio-admin-media video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .testimonio-admin-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-size: 3.5rem;
        color: rgba(255, 255, 255, 0.4);
    }

    .video-indicator {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .likes-indicator {
        position: absolute;
        bottom: 12px;
        left: 12px;
        background: rgba(255, 255, 255, 0.95);
        color: #dc3545;
        padding: 8px 14px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Contenido de la tarjeta */
    .testimonio-admin-body {
        padding: 20px;
    }

    .testimonio-admin-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 12px 0;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .testimonio-admin-meta {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 0.85rem;
        color: #888;
        margin-bottom: 18px;
    }

    .testimonio-admin-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Acciones */
    .testimonio-admin-actions {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .btn-admin-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 0;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .btn-admin-action.btn-view {
        background: #e3f2fd;
        color: #1976d2;
    }

    .btn-admin-action.btn-view:hover {
        background: #1976d2;
        color: #ffffff;
    }

    .btn-admin-action.btn-edit {
        background: #fff3e0;
        color: #f57c00;
    }

    .btn-admin-action.btn-edit:hover {
        background: #f57c00;
        color: #ffffff;
    }

    .btn-admin-action.btn-delete {
        background: #ffebee;
        color: #dc3545;
    }

    .btn-admin-action.btn-delete:hover {
        background: #dc3545;
        color: #ffffff;
    }

    /* Empty state */
    .testimonios-admin-empty {
        text-align: center;
        padding: 80px 20px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
    }

    .testimonios-admin-empty i {
        font-size: 5rem;
        color: #ddd;
        margin-bottom: 20px;
    }

    .testimonios-admin-empty h3 {
        font-size: 1.4rem;
        color: #666;
        margin-bottom: 15px;
    }

    .testimonios-admin-empty p {
        color: #999;
        margin-bottom: 25px;
    }

    /* Flash messages */
    .alert-admin {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-admin.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-admin.error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Responsive */
    @media (max-width: 767px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-crear-testimonio {
            width: 100%;
            justify-content: center;
        }

        .testimonios-admin-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-testimonios-page">
    <div class="container">
        <!-- Flash Messages -->
        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert-admin success">
                <i class="fas fa-check-circle"></i>
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert-admin error">
                <i class="fas fa-exclamation-circle"></i>
                <?= Yii::$app->session->getFlash('error') ?>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="page-header">
            <h1>
                <i class="fas fa-star"></i>
                Administrar Testimonios
            </h1>
            <?= Html::a(
                '<i class="fas fa-plus"></i> Crear Nuevo Testimonio',
                ['testimonios/create'],
                ['class' => 'btn-crear-testimonio']
            ) ?>
        </div>

        <?php if (!empty($testimonios)): ?>
            <div class="testimonios-admin-grid">
                <?php foreach ($testimonios as $testimonio):
                    $isVideo = false;
                    if ($testimonio->url_media) {
                        $extension = strtolower(pathinfo($testimonio->url_media, PATHINFO_EXTENSION));
                        $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']);
                    }

                    // Contar comentarios
                    $comentariosCount = \app\models\Comentarios::find()
                        ->where(['id_testimonio' => $testimonio->id, 'is_deleted' => 0])
                        ->count();
                    ?>
                    <div class="testimonio-admin-card" id="testimonio-card-<?= $testimonio->id ?>">
                        <!-- Media -->
                        <div class="testimonio-admin-media">
                            <?php if ($testimonio->url_media): ?>
                                <?php if ($isVideo): ?>
                                    <video muted playsinline preload="metadata">
                                        <source src="<?= Yii::getAlias('@web') . '/' . Html::encode($testimonio->url_media) ?>"
                                            type="video/mp4">
                                    </video>
                                    <div class="video-indicator">
                                        <i class="fas fa-play"></i> Video
                                    </div>
                                <?php else: ?>
                                    <img src="<?= Yii::getAlias('@web') . '/' . Html::encode($testimonio->url_media) ?>"
                                        alt="<?= Html::encode($testimonio->titulo) ?>">
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="testimonio-admin-placeholder">
                                    <i class="fas fa-trophy"></i>
                                </div>
                            <?php endif; ?>

                            <div class="likes-indicator">
                                <i class="fas fa-heart"></i>
                                <?= number_format($testimonio->contador_likes ?? 0) ?>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="testimonio-admin-body">
                            <h3 class="testimonio-admin-title"><?= Html::encode($testimonio->titulo) ?></h3>

                            <div class="testimonio-admin-meta">
                                <span>
                                    <i class="fas fa-comments"></i>
                                    <?= $comentariosCount ?> comentarios
                                </span>
                                <span>
                                    <i class="fas fa-calendar"></i>
                                    <?= Yii::$app->formatter->asDate($testimonio->created_at, 'short') ?>
                                </span>
                            </div>

                            <div class="testimonio-admin-actions">
                                <?= Html::a(
                                    '<i class="fas fa-eye"></i> Ver',
                                    ['testimonios/view', 'id' => $testimonio->id],
                                    ['class' => 'btn-admin-action btn-view', 'target' => '_blank']
                                ) ?>
                                <?= Html::a(
                                    '<i class="fas fa-edit"></i> Editar',
                                    ['testimonios/update', 'id' => $testimonio->id],
                                    ['class' => 'btn-admin-action btn-edit']
                                ) ?>
                                <button type="button" class="btn-admin-action btn-delete"
                                    onclick="deleteTestimonio(<?= $testimonio->id ?>)">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="testimonios-admin-empty">
                <i class="fas fa-comment-slash"></i>
                <h3>No hay testimonios registrados</h3>
                <p>Comienza creando el primer testimonio de un ganador</p>
                <?= Html::a(
                    '<i class="fas fa-plus"></i> Crear Primer Testimonio',
                    ['testimonios/create'],
                    ['class' => 'btn-crear-testimonio']
                ) ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function deleteTestimonio(id) {
        if (!confirm('¿Estás seguro de eliminar este testimonio? Esta acción no se puede deshacer.')) {
            return;
        }

        fetch('<?= Url::to(['testimonios/delete']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'id=' + id + '&_csrf=<?= Yii::$app->request->getCsrfToken() ?>'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remover la tarjeta con animación
                    const card = document.getElementById('testimonio-card-' + id);
                    if (card) {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => card.remove(), 300);
                    }
                } else {
                    alert(data.message || 'Error al eliminar el testimonio');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el testimonio');
            });
    }
</script>