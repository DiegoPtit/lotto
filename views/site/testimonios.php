<?php

/** @var yii\web\View $this */
/** @var app\models\Testimonios[] $testimonios */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Testimonios de Ganadores';
$this->params['breadcrumbs'][] = $this->title;
$this->params['showTermsModal'] = true;
?>

<style>
    /* ==================== TESTIMONIOS PAGE STYLES ==================== */
    .testimonios-page {
        padding: 40px 0;
        min-height: 80vh;
    }

    .testimonios-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .testimonios-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 15px;
        background: linear-gradient(135deg, #0066cc 0%, #00ccff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .testimonios-subtitle {
        font-size: 1.1rem;
        color: #666;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Grid de testimonios */
    .testimonios-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
    }

    /* Tarjeta de testimonio */
    .testimonio-card {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        aspect-ratio: 4 / 3;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .testimonio-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
    }

    /* Media (Imagen o Video) */
    .testimonio-media {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }

    .testimonio-media img,
    .testimonio-media video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .testimonio-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: rgba(255, 255, 255, 0.5);
    }

    /* Overlay con gradiente */
    .testimonio-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top,
                rgba(0, 0, 0, 0.85) 0%,
                rgba(0, 0, 0, 0.3) 40%,
                transparent 100%);
        opacity: 0;
        transition: opacity 0.4s ease;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 25px;
    }

    .testimonio-card:hover .testimonio-overlay {
        opacity: 1;
    }

    /* Contenido del hover */
    .testimonio-hover-content {
        transform: translateY(20px);
        transition: transform 0.4s ease;
    }

    .testimonio-card:hover .testimonio-hover-content {
        transform: translateY(0);
    }

    .testimonio-titulo {
        color: #ffffff;
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 15px 0;
        line-height: 1.3;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    .btn-ver-testimonio {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        color: #ffffff;
        padding: 12px 24px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-ver-testimonio:hover {
        background: linear-gradient(135deg, #0052a3 0%, #003d7a 100%);
        transform: scale(1.05);
        color: #ffffff;
        text-decoration: none;
    }

    /* Badge de likes */
    .likes-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 8px 14px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #dc3545;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        z-index: 5;
        transition: transform 0.3s ease;
    }

    .likes-badge:hover {
        transform: scale(1.1);
    }

    .likes-badge i {
        font-size: 1rem;
    }

    /* Badge de video */
    .video-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(10px);
        color: #ffffff;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 5;
    }

    /* Empty state */
    .testimonios-empty {
        text-align: center;
        padding: 80px 20px;
        color: #666;
    }

    .testimonios-empty i {
        font-size: 5rem;
        color: #ddd;
        margin-bottom: 20px;
    }

    .testimonios-empty h3 {
        font-size: 1.5rem;
        color: #999;
        margin-bottom: 10px;
    }

    .testimonios-empty p {
        font-size: 1rem;
        color: #aaa;
    }

    /* Responsive */
    @media (max-width: 767px) {
        .testimonios-title {
            font-size: 1.8rem;
        }

        .testimonios-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .testimonio-card {
            aspect-ratio: 16 / 9;
        }
    }

    @media (max-width: 480px) {
        .testimonios-page {
            padding: 25px 0;
        }

        .testimonios-header {
            margin-bottom: 30px;
        }
    }
</style>

<div class="testimonios-page">
    <div class="container">
        <div class="testimonios-header">
            <h1 class="testimonios-title">
                <i class="fas fa-star me-2"></i>
                Testimonios de Ganadores
            </h1>
            <p class="testimonios-subtitle">
                Conoce las historias de nuestros ganadores y descubre cómo cambiaron sus vidas
            </p>
        </div>

        <?php if (!empty($testimonios)): ?>
            <div class="testimonios-grid">
                <?php foreach ($testimonios as $testimonio):
                    $isVideo = false;
                    if ($testimonio->url_media) {
                        $extension = strtolower(pathinfo($testimonio->url_media, PATHINFO_EXTENSION));
                        $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']);
                    }
                    ?>
                    <div class="testimonio-card">
                        <!-- Media (Imagen o Video) -->
                        <div class="testimonio-media">
                            <?php if ($testimonio->url_media): ?>
                                <?php if ($isVideo): ?>
                                    <video muted playsinline preload="metadata">
                                        <source src="<?= Yii::getAlias('@web') ?>/<?= Html::encode($testimonio->url_media) ?>"
                                            type="video/<?= $extension ?>">
                                    </video>
                                <?php else: ?>
                                    <img src="<?= Yii::getAlias('@web') ?>/<?= Html::encode($testimonio->url_media) ?>"
                                        alt="<?= Html::encode($testimonio->titulo) ?>">
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="testimonio-placeholder">
                                    <i class="fas fa-trophy"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Badge de video -->
                        <?php if ($isVideo): ?>
                            <div class="video-badge">
                                <i class="fas fa-play"></i>
                                Video
                            </div>
                        <?php endif; ?>

                        <!-- Badge de likes -->
                        <div class="likes-badge">
                            <i class="fas fa-heart"></i>
                            <span><?= number_format($testimonio->contador_likes ?? 0) ?></span>
                        </div>

                        <!-- Overlay con título y botón -->
                        <div class="testimonio-overlay">
                            <div class="testimonio-hover-content">
                                <h3 class="testimonio-titulo"><?= Html::encode($testimonio->titulo) ?></h3>
                                <?= Html::a(
                                    '<i class="fas fa-eye"></i> Ver Testimonio',
                                    ['testimonios/view', 'id' => $testimonio->id],
                                    ['class' => 'btn-ver-testimonio']
                                ) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="testimonios-empty">
                <i class="fas fa-comment-slash"></i>
                <h3>Aún no hay testimonios</h3>
                <p>Pronto nuestros ganadores compartirán sus historias contigo</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Reproducir video al hacer hover
    document.addEventListener('DOMContentLoaded', function () {
        const testimonioCards = document.querySelectorAll('.testimonio-card');

        testimonioCards.forEach(card => {
            const video = card.querySelector('video');
            if (video) {
                card.addEventListener('mouseenter', function () {
                    video.play().catch(e => console.log('Autoplay prevented'));
                });

                card.addEventListener('mouseleave', function () {
                    video.pause();
                    video.currentTime = 0;
                });
            }
        });
    });
</script>