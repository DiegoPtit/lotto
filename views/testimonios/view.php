<?php

/** @var yii\web\View $this */
/** @var app\models\Testimonios $testimonio */
/** @var app\models\Comentarios[] $comentarios */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $testimonio->titulo ?? 'Testimonio';
$this->params['breadcrumbs'][] = ['label' => 'Testimonios', 'url' => ['/site/testimonios']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['showTermsModal'] = true;

$isVideo = false;
if ($testimonio->url_media) {
    $extension = strtolower(pathinfo($testimonio->url_media, PATHINFO_EXTENSION));
    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']);
}

$isLoggedIn = !Yii::$app->user->isGuest;
?>

<style>
    /* ==================== TESTIMONIO VIEW STYLES ==================== */
    .testimonio-view-page {
        padding: 40px 0;
        min-height: 80vh;
    }

    .testimonio-view-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    @media (min-width: 992px) {
        .testimonio-view-container {
            grid-template-columns: 1.2fr 1fr;
        }
    }

    /* ==================== TARJETA DE MEDIA ==================== */
    .media-card {
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .media-container {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 10;
        background: #1a1a1a;
    }

    .media-container img,
    .media-container video {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: #1a1a1a;
    }

    .media-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-size: 6rem;
        color: rgba(255, 255, 255, 0.4);
    }

    .media-card-footer {
        padding: 25px;
    }

    .testimonio-description {
        font-size: 1.05rem;
        line-height: 1.7;
        color: #444;
        margin-bottom: 20px;
        white-space: pre-wrap;
    }

    .testimonio-stats {
        display: flex;
        align-items: center;
        gap: 25px;
        flex-wrap: wrap;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1rem;
        color: #666;
    }

    .stat-item i {
        font-size: 1.25rem;
    }

    .stat-item.likes {
        color: #dc3545;
    }

    .stat-item.likes i {
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .stat-item.likes i:hover {
        transform: scale(1.2);
    }

    .stat-item.likes.liked i {
        animation: heartPulse 0.4s ease;
    }

    @keyframes heartPulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.3);
        }
    }

    .stat-item.comments {
        color: #0066cc;
    }

    .btn-like-testimonio {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: #ffffff;
        padding: 12px 25px;
        border-radius: 30px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-like-testimonio:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    }

    .btn-like-testimonio.liked {
        background: linear-gradient(135deg, #666 0%, #444 100%);
    }

    /* ==================== TARJETA DE COMENTARIOS ==================== */
    .comments-card {
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        max-height: 700px;
    }

    .comments-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        color: #ffffff;
    }

    .comments-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .comments-list {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .comment-item {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
        transition: all 0.3s ease;
    }

    .comment-item:hover {
        background: #eef3f8;
    }

    .comment-author {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .author-name {
        font-weight: 700;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .author-name i {
        color: #0066cc;
    }

    .comment-date {
        font-size: 0.8rem;
        color: #999;
    }

    .comment-message {
        font-size: 0.95rem;
        color: #555;
        line-height: 1.6;
        margin-bottom: 12px;
    }

    .comment-actions {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .btn-like-comment {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 20px;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .btn-like-comment:hover {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .btn-like-comment.liked {
        color: #dc3545;
    }

    .btn-delete-comment {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 20px;
        transition: all 0.3s ease;
        font-size: 0.85rem;
    }

    .btn-delete-comment:hover {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    /* Formulario de comentario */
    .comment-form-container {
        padding: 20px;
        border-top: 1px solid #eee;
        background: #fafbfc;
    }

    .comment-form {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .comment-form input,
    .comment-form textarea {
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        resize: none;
    }

    .comment-form input:focus,
    .comment-form textarea:focus {
        outline: none;
        border-color: #0066cc;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    }

    .btn-submit-comment {
        align-self: flex-end;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        color: #ffffff;
        padding: 12px 25px;
        border-radius: 30px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-submit-comment:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 20px rgba(0, 102, 204, 0.3);
    }

    .btn-submit-comment:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Empty comments state */
    .comments-empty {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }

    .comments-empty i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #ddd;
    }

    /* Back button */
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #0066cc;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 25px;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        color: #0052a3;
        transform: translateX(-5px);
    }

    /* Responsive */
    @media (max-width: 991px) {
        .comments-card {
            max-height: none;
        }
    }

    @media (max-width: 767px) {
        .testimonio-view-page {
            padding: 20px 0;
        }

        .media-card-footer {
            padding: 20px;
        }

        .comments-header {
            padding: 15px 20px;
        }

        .comments-list {
            padding: 15px;
        }

        .comment-form-container {
            padding: 15px;
        }
    }
</style>

<div class="testimonio-view-page">
    <div class="container">
        <?= Html::a('<i class="fas fa-arrow-left"></i> Volver a Testimonios', ['/site/testimonios'], ['class' => 'btn-back']) ?>

        <div class="testimonio-view-container">
            <!-- ==================== TARJETA DE MEDIA ==================== -->
            <div class="media-card">
                <div class="media-container">
                    <?php if ($testimonio->url_media): ?>
                        <?php if ($isVideo): ?>
                            <video controls playsinline preload="metadata">
                                <source src="<?= Yii::getAlias('@web') ?>/<?= Html::encode($testimonio->url_media) ?>"
                                    type="video/<?= $extension ?>">
                                Tu navegador no soporta la reproducción de videos.
                            </video>
                        <?php else: ?>
                            <img src="<?= Yii::getAlias('@web') ?>/<?= Html::encode($testimonio->url_media) ?>"
                                alt="<?= Html::encode($testimonio->titulo) ?>">
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="media-placeholder">
                            <i class="fas fa-trophy"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="media-card-footer">
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #1a1a1a; margin-bottom: 15px;">
                        <?= Html::encode($testimonio->titulo) ?>
                    </h1>

                    <?php if ($testimonio->descripcion): ?>
                        <p class="testimonio-description"><?= Html::encode($testimonio->descripcion) ?></p>
                    <?php endif; ?>

                    <div class="testimonio-stats">
                        <div class="stat-item comments">
                            <i class="fas fa-comments"></i>
                            <span id="commentsCount"><?= count($comentarios) ?></span> comentarios
                        </div>

                        <button type="button" class="btn-like-testimonio" id="btnLikeTestimonio"
                            data-id="<?= $testimonio->id ?>">
                            <i class="fas fa-heart"></i>
                            <span id="likesCount"><?= number_format($testimonio->contador_likes ?? 0) ?></span>
                            Me gusta
                        </button>
                    </div>
                </div>
            </div>

            <!-- ==================== TARJETA DE COMENTARIOS ==================== -->
            <div class="comments-card">
                <div class="comments-header">
                    <h3>
                        <i class="fas fa-comments"></i>
                        Comentarios
                    </h3>
                </div>

                <div class="comments-list" id="commentsList">
                    <?php if (!empty($comentarios)): ?>
                        <?php foreach ($comentarios as $comentario): ?>
                            <div class="comment-item" id="comment-<?= $comentario->id ?>">
                                <div class="comment-author">
                                    <span class="author-name">
                                        <i class="fas fa-user-circle"></i>
                                        <?= Html::encode($comentario->nombre) ?>
                                    </span>
                                    <span class="comment-date">
                                        <?= Yii::$app->formatter->asRelativeTime($comentario->created_at) ?>
                                    </span>
                                </div>
                                <p class="comment-message"><?= Html::encode($comentario->mensaje) ?></p>
                                <div class="comment-actions">
                                    <button type="button" class="btn-like-comment" data-id="<?= $comentario->id ?>">
                                        <i class="fas fa-heart"></i>
                                        <span class="like-count"><?= $comentario->contador_likes ?? 0 ?></span>
                                    </button>
                                    <?php if ($isLoggedIn): ?>
                                        <button type="button" class="btn-delete-comment" data-id="<?= $comentario->id ?>">
                                            <i class="fas fa-trash-alt"></i>
                                            Eliminar
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="comments-empty" id="commentsEmpty">
                            <i class="fas fa-comment-dots"></i>
                            <p>Sé el primero en comentar</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="comment-form-container">
                    <form class="comment-form" id="commentForm">
                        <input type="hidden" name="id_testimonio" value="<?= $testimonio->id ?>">
                        <input type="text" name="nombre" placeholder="Tu nombre" required maxlength="200">
                        <textarea name="mensaje" rows="3" placeholder="Escribe tu comentario..." required></textarea>
                        <button type="submit" class="btn-submit-comment" id="btnSubmitComment">
                            <i class="fas fa-paper-plane"></i>
                            Enviar Comentario
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const testimonioId = <?= $testimonio->id ?>;
        const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;
        const csrfToken = '<?= Yii::$app->request->getCsrfToken() ?>';

        // Like del testimonio
        const btnLike = document.getElementById('btnLikeTestimonio');
        btnLike.addEventListener('click', function () {
            fetch('<?= Url::to(['testimonios/like']) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'id=' + testimonioId + '&_csrf=' + encodeURIComponent(csrfToken)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('likesCount').textContent = data.likes;
                        btnLike.classList.add('liked');
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        // Enviar comentario
        const commentForm = document.getElementById('commentForm');
        commentForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = document.getElementById('btnSubmitComment');
            submitBtn.disabled = true;

            const formData = new FormData(commentForm);
            const params = new URLSearchParams(formData).toString() + '&_csrf=' + encodeURIComponent(csrfToken);

            fetch('<?= Url::to(['testimonios/add-comment']) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: params
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remover mensaje de empty
                        const emptyMsg = document.getElementById('commentsEmpty');
                        if (emptyMsg) emptyMsg.remove();

                        // Agregar nuevo comentario al principio
                        const commentsList = document.getElementById('commentsList');
                        const newComment = createCommentElement(data.comentario);
                        commentsList.insertAdjacentHTML('afterbegin', newComment);

                        // Actualizar contador
                        const countEl = document.getElementById('commentsCount');
                        countEl.textContent = parseInt(countEl.textContent) + 1;

                        // Limpiar formulario
                        commentForm.reset();

                        // Agregar event listeners al nuevo comentario
                        attachCommentEventListeners();
                    } else {
                        alert(data.message || 'Error al enviar el comentario');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al enviar el comentario');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                });
        });

        // Crear HTML de comentario
        function createCommentElement(comentario) {
            const deleteBtn = isLoggedIn ? `
            <button type="button" class="btn-delete-comment" data-id="${comentario.id}">
                <i class="fas fa-trash-alt"></i>
                Eliminar
            </button>
        ` : '';

            return `
            <div class="comment-item" id="comment-${comentario.id}">
                <div class="comment-author">
                    <span class="author-name">
                        <i class="fas fa-user-circle"></i>
                        ${escapeHtml(comentario.nombre)}
                    </span>
                    <span class="comment-date">${comentario.created_at}</span>
                </div>
                <p class="comment-message">${escapeHtml(comentario.mensaje)}</p>
                <div class="comment-actions">
                    <button type="button" class="btn-like-comment" data-id="${comentario.id}">
                        <i class="fas fa-heart"></i>
                        <span class="like-count">${comentario.contador_likes}</span>
                    </button>
                    ${deleteBtn}
                </div>
            </div>
        `;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Attach event listeners para likes y deletes
        function attachCommentEventListeners() {
            // Like comentarios
            document.querySelectorAll('.btn-like-comment').forEach(btn => {
                btn.onclick = function () {
                    const commentId = this.dataset.id;
                    const likeCountEl = this.querySelector('.like-count');

                    fetch('<?= Url::to(['testimonios/like-comment']) ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: 'id=' + commentId + '&_csrf=' + encodeURIComponent(csrfToken)
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                likeCountEl.textContent = data.likes;
                                btn.classList.add('liked');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                };
            });

            // Delete comentarios
            document.querySelectorAll('.btn-delete-comment').forEach(btn => {
                btn.onclick = function () {
                    if (!confirm('¿Estás seguro de eliminar este comentario?')) return;

                    const commentId = this.dataset.id;

                    fetch('<?= Url::to(['testimonios/delete-comment']) ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: 'id=' + commentId + '&_csrf=' + encodeURIComponent(csrfToken)
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('comment-' + commentId).remove();

                                // Actualizar contador
                                const countEl = document.getElementById('commentsCount');
                                countEl.textContent = Math.max(0, parseInt(countEl.textContent) - 1);

                                // Mostrar mensaje de empty si no hay más comentarios
                                const commentsList = document.getElementById('commentsList');
                                if (commentsList.querySelectorAll('.comment-item').length === 0) {
                                    commentsList.innerHTML = `
                                <div class="comments-empty" id="commentsEmpty">
                                    <i class="fas fa-comment-dots"></i>
                                    <p>Sé el primero en comentar</p>
                                </div>
                            `;
                                }
                            } else {
                                alert(data.message || 'Error al eliminar el comentario');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                };
            });
        }

        // Inicializar event listeners
        attachCommentEventListeners();
    });
</script>