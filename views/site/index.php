<?php

/** @var yii\web\View $this */
/** @var app\models\Rifas[] $mejoresRifas */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::$app->name;

// Señal para que main.php muestre el modal de términos y condiciones
$this->params['showTermsModal'] = true;

// Lógica para determinar el ranking de ventas
$ventasPorRifa = [];
foreach ($mejoresRifas as $r) {
    if ($r instanceof \app\models\Rifas) {
        // Contar BoletoNumeros vendidos (pagados) por esta rifa
        $count = \app\models\BoletoNumeros::find()
            ->joinWith('boleto b')
            ->where(['b.id_rifa' => $r->id])
            ->andWhere(['b.estado' => \app\models\Boletos::ESTADO_PAGADO])
            ->count();
        $ventasPorRifa[$r->id] = (int) $count;
    }
}
// Ordenar de mayor a menor ventas
arsort($ventasPorRifa);
$rankingIds = array_keys($ventasPorRifa);
?>

<style>
    /* ==================== GLOBAL RESET ==================== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* ==================== RANKING & GLOW STYLES ==================== */
    @keyframes breatheGlow {
        0% {
            box-shadow: 0 0 5px rgba(0, 102, 204, 0.1);
            border-color: rgba(0, 102, 204, 0.3);
        }

        50% {
            box-shadow: 0 0 20px rgba(0, 102, 204, 0.4);
            border-color: rgba(0, 102, 204, 0.8);
        }

        100% {
            box-shadow: 0 0 5px rgba(0, 102, 204, 0.1);
            border-color: rgba(0, 102, 204, 0.3);
        }
    }

    .rifa-card.top-seller-glow {
        animation: fadeInUp 0.8s ease-out forwards, breatheGlow 4s infinite ease-in-out;
        border: 1px solid rgba(0, 102, 204, 0.5);
        position: relative;
    }

    .ranking-badge {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: #ffffff;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 5;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        white-space: nowrap;
    }

    .text-muted-blue {
        color: #6c8caf;
        /* Todo muted pero azulado */
    }

    /* ==================== LANDING PAGE LAYOUT ==================== */
    .site-index {
        background: #ffffff;
        min-height: 100vh;
    }

    .container-custom {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* ==================== RIFAS SECTION ==================== */
    .rifas-section {
        padding: 60px 0;
        background: #ffffff;
    }

    .section-header {
        margin-bottom: 40px;
    }

    .section-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 10px;
        text-align: center;
    }

    .section-description {
        font-size: 1rem;
        color: #666;
        text-align: center;
        max-width: 600px;
        margin: 0 auto;
    }

    /* ==================== MARQUEE PRECIO (LED EFFECT) ==================== */
    @keyframes scrollText {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-50%);
        }
    }

    .rifa-precio-badge {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: rgba(0, 102, 204, 0.95);
        /* Azul sólido con mínima transparencia */
        color: #ffffff;
        height: 36px;
        overflow: hidden;
        display: flex;
        align-items: center;
        z-index: 5;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        letter-spacing: 1px;
        backdrop-filter: blur(2px);
    }

    .marquee-track {
        display: flex;
        align-items: center;
        white-space: nowrap;
        animation: scrollText 5s linear infinite;
        /* 3s = Rápido */
    }

    .marquee-track span {
        padding-right: 40px;
        /* Más espacio entre repeticiones para legibilidad */
    }

    /* ==================== RIFAS GRID ==================== */
    .rifas-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
        padding-top: 20px;
        /* Espacio para los badges flotantes */
    }

    @media (min-width: 768px) {
        .rifas-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .rifas-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }


    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translate3d(0, 40px, 0);
        }

        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }

    /* Aplica la animación a las tarjetas pero inicia invisible */
    .rifa-card {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        position: relative;
        /* NECESARIO para que el badge se posicione respecto a la tarjeta */
        overflow: visible;
        /* NECESARIO para que el badge se vea fuera */
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        opacity: 0;
        /* Inicio invisible */
        animation: fadeInUp 0.8s ease-out forwards;
        z-index: 1;
    }


    .rifa-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        border-color: #0066cc;
        z-index: 10;
        /* Elevar al hacer hover */
    }

    .rifa-card-image {
        position: relative;
        width: 100%;
        height: 240px;
        overflow: hidden;
        background: #f5f5f5;
        border-radius: 8px 8px 0 0;
        /* <-- AGREGADO para mantener bordes redondos arriba */
    }

    .rifa-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .rifa-card:hover .rifa-card-image img {
        transform: scale(1.05);
    }

    .rifa-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f5f5f5 0%, #eeeeee 100%);
    }

    .rifa-placeholder i {
        font-size: 4rem;
        color: #cccccc;
    }

    .rifa-precio {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #0066cc;
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 1.125rem;
        box-shadow: 0 2px 8px rgba(0, 102, 204, 0.3);
    }

    .rifa-card-body {
        padding: 24px;
    }

    .rifa-titulo {
        font-size: 1.375rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 12px;
        line-height: 1.3;
        min-height: 2.6em;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .rifa-descripcion {
        font-size: 0.9375rem;
        color: #666;
        margin-bottom: 20px;
        line-height: 1.6;
        min-height: 3em;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* ==================== PROGRESS BAR ==================== */
    .rifa-progreso {
        margin-bottom: 20px;
    }

    .progreso-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        font-size: 0.875rem;
        color: #666;
    }

    .progreso-info span:first-child {
        font-weight: 600;
        color: #0066cc;
    }

    .progreso-barra {
        width: 100%;
        height: 8px;
        background: #f0f0f0;
        border-radius: 4px;
        overflow: hidden;
    }

    .progreso-fill {
        height: 100%;
        background: linear-gradient(90deg, #0066cc 0%, #0052a3 100%);
        border-radius: 4px;
        transition: width 0.5s ease;
    }

    /* ==================== BUTTONS ==================== */
    .btn-participar {
        width: 100%;
        padding: 14px 24px;
        background: #0066cc;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-participar:hover {
        background: #0052a3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }

    .btn-participar:active {
        transform: translateY(0);
    }

    /* ==================== STEPS SECTION ==================== */
    .steps-section {
        padding: 60px 0;
        border-top: 1px solid #e0e0e0;
        border-bottom: 1px solid #e0e0e0;
    }

    .steps-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
        margin-top: 40px;
    }

    @media (min-width: 640px) {
        .steps-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .steps-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .step-card {
        background: #ffffff;
        border: 1px solid #f0f0f0;
        border-radius: 16px;
        padding: 40px 24px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }

    .step-card:hover {
        border-color: #0066cc;
        box-shadow: 0 4px 15px rgba(0, 102, 204, 0.08);
        transform: translateY(-2px);
    }

    .step-icon {
        margin-bottom: 20px;
        color: #0066cc;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 102, 204, 0.05);
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .step-card:hover .step-icon {
        background: rgba(0, 102, 204, 0.1);
        transform: scale(1.1);
    }

    .step-icon i {
        font-size: 2rem;
    }

    .step-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .step-description {
        font-size: 0.95rem;
        color: #64748b;
        line-height: 1.5;
        max-width: 250px;
        margin: 0 auto;
    }

    /* ==================== VERIFICADOR SECTION (REDISEÑO TOTAL) ==================== */
    #verificador-custom-section .verificador-section {
        padding: 80px 0;
    }

    /* Caja contenedora estilo tarjeta limpia */
    #verificador-custom-section .verificador-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        padding: 40px;
        max-width: 700px;
        margin: 0 auto;
        text-align: center;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    #verificador-custom-section .verificador-content h3 {
        font-size: 2rem;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    #verificador-custom-section .verificador-content p {
        font-size: 1.1rem;
        color: #64748b;
        margin-bottom: 30px;
    }

    #verificador-custom-section .input-wrapper {
        position: relative;
        margin-bottom: 20px;
    }

    /* Reset total para el input */
    #verificador-custom-section .custom-input {
        width: 100% !important;
        height: 64px !important;
        padding: 0 20px 0 60px !important;
        /* Espacio para el icono a la izquierda */
        font-size: 1.25rem !important;
        border: 2px solid #e2e8f0 !important;
        border-radius: 12px !important;
        background-color: #f8fafc !important;
        color: #1e293b !important;
        transition: all 0.3s ease !important;
        box-shadow: none !important;
        outline: none !important;
        margin: 0 !important;
        box-sizing: border-box !important;
    }

    #verificador-custom-section .custom-input:focus {
        border-color: #0066cc !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.1) !important;
    }

    /* Icono dentro del input */
    #verificador-custom-section .input-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.5rem;
        color: #94a3b8;
        pointer-events: none;
    }

    /* Botón moderno */
    #verificador-custom-section .btn-buscar-moderno {
        width: 100% !important;
        height: 56px !important;
        background: #0066cc !important;
        color: #ffffff !important;
        font-size: 1.1rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        border: none !important;
        border-radius: 12px !important;
        cursor: pointer !important;
        transition: transform 0.2s, background 0.2s, box-shadow 0.2s !important;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3) !important;
    }

    #verificador-custom-section .btn-buscar-moderno:hover {
        background: #0052a3 !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 102, 204, 0.4) !important;
    }

    #verificador-custom-section .btn-buscar-moderno:active {
        transform: translateY(0);
    }

    /* Responsive */
    @media (max-width: 640px) {
        #verificador-custom-section .verificador-card {
            padding: 30px 20px;
            border-radius: 16px;
        }

        #verificador-custom-section .verificador-content h3 {
            font-size: 1.5rem;
        }

        #verificador-custom-section .custom-input {
            height: 56px !important;
            font-size: 1rem !important;
        }
    }

    .mb-4 {
        margin-bottom: 24px !important;
    }

    /* ==================== MODAL DE PARTICIPACIÓN ==================== */
    .participation-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(5px);
        animation: fadeIn 0.3s ease;
    }

    .participation-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .participation-modal-content {
        background: #ffffff;
        border-radius: 12px;
        max-width: 1200px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.4s ease;
    }

    .participation-modal-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .participation-modal-title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: bold;
    }

    .participation-modal-close {
        color: white;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.2s;
        line-height: 1;
    }

    .participation-modal-close:hover {
        transform: scale(1.2);
    }

    .participation-modal-body {
        padding: 30px 25px;
        padding-bottom: 100px;
        /* Extra padding for super footer */
        box-sizing: border-box;
        width: 100%;
    }

    /* Force all modal elements to use border-box */
    .participation-modal-body,
    .participation-modal-body * {
        box-sizing: border-box;
    }

    .participation-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 25px;
    }

    @media (min-width: 1024px) {
        .participation-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    .participation-col-left,
    .participation-col-right {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .step-card-modal {
        background: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
    }

    .step-card-header {
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .step-number {
        width: 32px;
        height: 32px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.125rem;
    }

    .step-card-header h4 {
        margin: 0;
        font-size: 1.125rem;
        font-weight: 700;
    }

    .step-card-body {
        padding: 20px;
    }

    .step-card-footer {
        padding: 15px 20px;
        background: #ffffff;
        border-top: 1px solid #e0e0e0;
        text-align: center;
    }

    /* Quantity Pad */
    .quantity-pad {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        width: 100%;
    }

    .qty-btn {
        padding: 16px;
        border: 1px solid #d0d0d0;
        background: #ffffff;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .qty-btn:hover {
        background: #f5f5f5;
        border-color: #0066cc;
    }

    .qty-btn:active {
        transform: scale(0.95);
    }

    .qty-add {
        color: #0066cc;
    }

    .qty-display {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: #ffffff;
        border: 2px solid #0066cc;
        border-radius: 6px;
        font-size: 1.5rem;
        font-weight: 700;
        color: #0066cc;
    }

    .quantity-status {
        display: block;
        font-size: 0.9375rem;
        color: #666;
    }

    .quantity-status.status-assigned {
        color: #28a745;
        font-weight: 600;
    }

    /* Registration Form */
    .registration-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: 100%;
    }

    .form-group-modal {
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: 100%;
    }

    .form-group-modal label {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #1a1a1a;
    }

    .form-group-modal label i {
        margin-right: 6px;
        color: #0066cc;
    }

    .form-group-modal input,
    .form-group-modal select {
        padding: 12px 14px;
        border: 1px solid #d0d0d0;
        border-radius: 6px;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-group-modal input:focus,
    .form-group-modal select:focus {
        outline: none;
        border-color: #0066cc;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    }

    .form-divider {
        border: none;
        border-top: 1px solid #e0e0e0;
        margin: 10px 0;
    }

    .proof-preview {
        margin-top: 10px;
    }

    .proof-preview img {
        max-width: 100%;
        max-height: 200px;
        border-radius: 6px;
        border: 1px solid #e0e0e0;
    }

    /* Payment Methods */
    .payment-methods {
        display: flex;
        flex-direction: column;
        gap: 15px;
        width: 100%;
    }

    .loading-spinner {
        text-align: center;
        padding: 30px;
        color: #666;
    }

    .loading-spinner i {
        font-size: 2rem;
        margin-bottom: 10px;
        display: block;
    }

    .payment-method-card {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        overflow: hidden;
        width: 100%;
    }

    .payment-method-header {
        background: #f5f5f5;
        padding: 12px 15px;
        font-weight: 600;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .payment-method-header i {
        color: #0066cc;
    }

    .payment-method-body {
        padding: 15px;
    }

    .payment-field {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .payment-field:last-child {
        border-bottom: none;
    }

    .field-label {
        font-weight: 600;
        color: #666;
        font-size: 0.875rem;
    }

    .field-value {
        font-weight: 500;
        color: #1a1a1a;
        font-size: 0.9375rem;
        margin-right: 10px;
    }

    .btn-copy {
        padding: 6px 12px;
        background: #f5f5f5;
        border: 1px solid #d0d0d0;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-copy:hover {
        background: #0066cc;
        border-color: #0066cc;
        color: #ffffff;
    }

    .btn-copy.copied {
        background: #28a745;
        border-color: #28a745;
        color: #ffffff;
    }

    /* Super Footer */
    .total-super-footer {
        position: fixed;
        bottom: -100px;
        left: 0;
        right: 0;
        background: #ffffff;
        border-top: 1px solid #e0e0e0;
        color: #1a1a1a;
        padding: 20px;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
        transition: bottom 0.3s ease;
        z-index: 10001;
        /* Mayor que el modal (10000) */
    }

    .total-super-footer.show {
        bottom: 0;
    }

    .super-footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .super-footer-amount {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .amount-label {
        font-size: 1rem;
        font-weight: 600;
        color: #0066cc;
        /* Azul */
    }

    .amount-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #000000;
        /* Negro */
    }

    .btn-process-ticket {
        padding: 12px 30px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-process-ticket:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }

    .btn-process-ticket i {
        margin-right: 8px;
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Estilos para el detalle de la rifa en el modal */
    /* Estilos para el detalle de la rifa en el modal */
    .raffle-info-container {
        display: flex;
        flex-direction: row;
        margin-bottom: 25px;
        background: #f8f9fa;
        border-radius: 10px;
        align-items: stretch;
        /* Equal height columns */
        overflow: hidden;
        /* Encapsulate image corners */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        min-height: 200px;
    }

    .raffle-info-left {
        width: 30%;
        flex: 0 0 30%;
        position: relative;
        padding: 0;
        margin: 0;
    }

    .raffle-info-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 0;
        /* Corner radius handled by container */
    }

    .raffle-placeholder-modal {
        width: 100%;
        height: 100%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: #adb5bd;
    }

    .raffle-info-right {
        flex: 1;
        width: 70%;
        padding: 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* Center vertically */
        align-items: center;
        /* Center horizontally */
        text-align: center;
    }

    .raffle-info-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 15px 0;
        line-height: 1.2;
    }

    .raffle-info-desc {
        font-size: 1rem;
        color: #555;
        white-space: pre-wrap;
        line-height: 1.6;
    }

    /* ==================== RAFFLE METADATA SECTION ==================== */
    .raffle-metadata {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 25px;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        border: 1px solid #e0e0e0;
    }

    .raffle-meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: center;
        align-items: center;
    }

    .raffle-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        flex: 1;
        min-width: 200px;
        justify-content: center;
    }

    .raffle-meta-item i {
        font-size: 1.1rem;
        color: #0066cc;
    }

    .raffle-meta-label {
        font-size: 0.85rem;
        color: #666;
        font-weight: 500;
    }

    .raffle-meta-value {
        font-size: 0.95rem;
        color: #1a1a1a;
        font-weight: 700;
    }

    /* Countdown Timer Styles */
    .raffle-countdown-container {
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 102, 204, 0.25);
    }

    .raffle-countdown-label {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.9rem;
        margin-bottom: 10px;
        font-weight: 500;
    }

    .raffle-countdown {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .countdown-unit {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 55px;
    }

    .countdown-value {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 1.5rem;
        font-weight: 800;
        color: #ffffff;
        font-family: 'Courier New', monospace;
        min-width: 50px;
        text-align: center;
    }

    .countdown-label {
        font-size: 0.7rem;
        color: rgba(255, 255, 255, 0.8);
        text-transform: uppercase;
        margin-top: 5px;
        letter-spacing: 0.5px;
    }

    .raffle-countdown-cta {
        color: #ffc107;
        font-weight: 700;
        font-size: 1rem;
        margin: 0;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .raffle-sorteo-date {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 20px;
        background: #ffffff;
        border-radius: 8px;
        border: 2px dashed #28a745;
    }

    .raffle-sorteo-date i {
        color: #28a745;
        font-size: 1.2rem;
    }

    .raffle-sorteo-date span {
        font-weight: 600;
        color: #1a1a1a;
    }

    .raffle-sorteo-value {
        color: #28a745;
        font-weight: 700;
    }

    /* ==================== PRIZES SECTION (STACKED STEPS) ==================== */
    .raffle-prizes-section {
        margin-bottom: 25px;
    }

    .raffle-prizes-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .raffle-prizes-title i {
        color: #ffc107;
    }

    .prizes-stack {
        display: flex;
        flex-direction: column;
        gap: 0;
        position: relative;
    }

    .prize-card {
        position: relative;
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
        margin-bottom: -8px;
        z-index: 1;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .prize-card:last-child {
        margin-bottom: 0;
    }

    .prize-card:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0, 102, 204, 0.15);
        z-index: 10;
    }

    .prize-card:nth-child(1) {
        z-index: 5;
        border-left: 4px solid #ffd700;
    }

    .prize-card:nth-child(2) {
        z-index: 4;
        border-left: 4px solid #c0c0c0;
        margin-left: 10px;
    }

    .prize-card:nth-child(3) {
        z-index: 3;
        border-left: 4px solid #cd7f32;
        margin-left: 20px;
    }

    .prize-card:nth-child(n+4) {
        z-index: 2;
        border-left: 4px solid #0066cc;
        margin-left: 30px;
    }

    .prize-order {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .prize-card:nth-child(1) .prize-order {
        background: linear-gradient(135deg, #ffd700 0%, #ffb300 100%);
        color: #1a1a1a;
    }

    .prize-card:nth-child(2) .prize-order {
        background: linear-gradient(135deg, #c0c0c0 0%, #a0a0a0 100%);
        color: #1a1a1a;
    }

    .prize-card:nth-child(3) .prize-order {
        background: linear-gradient(135deg, #cd7f32 0%, #a0522d 100%);
        color: #ffffff;
    }

    .prize-card:nth-child(n+4) .prize-order {
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        color: #ffffff;
    }

    .prize-info {
        flex: 1;
    }

    .prize-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 4px 0;
    }

    .prize-description {
        font-size: 0.85rem;
        color: #666;
        margin: 0;
        line-height: 1.4;
    }

    .prize-value {
        font-size: 0.9rem;
        font-weight: 700;
        color: #28a745;
        white-space: nowrap;
    }

    @media (max-width: 767px) {
        .raffle-meta-row {
            flex-direction: column;
        }

        .raffle-meta-item {
            min-width: 100%;
        }

        .raffle-countdown {
            gap: 6px;
        }

        .countdown-unit {
            min-width: 45px;
        }

        .countdown-value {
            font-size: 1.2rem;
            padding: 8px 10px;
            min-width: 40px;
        }

        .prize-card,
        .prize-card:nth-child(2),
        .prize-card:nth-child(3),
        .prize-card:nth-child(n+4) {
            margin-left: 0;
        }
    }

    @media (max-width: 767px) {
        .raffle-info-container {
            flex-direction: column;
            min-height: auto;
        }

        .raffle-info-left {
            width: 100%;
            height: 250px;
            /* Fixed height for mobile image */
            flex: none;
        }

        .raffle-info-right {
            width: 100%;
            padding: 20px;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 767px) {


        .section-title {
            font-size: 1.5rem;
        }

        .rifa-card-image {
            height: 200px;
        }

        .super-footer-content {
            flex-direction: column;
            text-align: center;
        }

        .super-footer-amount {
            flex-direction: column;
            gap: 5px;
        }

        .btn-process-ticket {
            width: 100%;
        }
    }

    /* ==================== MODAL RESPONSIVE - PANTALLAS REDUCIDAS ==================== */
    @media (max-width: 767px) {

        /* Modal Container */
        .participation-modal-content {
            max-width: 100%;
            max-height: 95vh;
            margin: 10px;
            border-radius: 10px;
            overflow-x: hidden;
        }

        .participation-modal-header {
            padding: 15px 18px;
        }

        .participation-modal-title {
            font-size: 1.2rem;
        }

        .participation-modal-body {
            padding: 20px 15px;
            padding-bottom: 120px; /* Extra padding for stacked super footer */
            overflow-x: hidden;
        }

        /* Force all modal children to respect container width */
        .participation-modal-body * {
            box-sizing: border-box;
            max-width: 100%;
        }

        /* Grid and Columns */
        .participation-grid {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .participation-col-left,
        .participation-col-right {
            width: 100%;
            max-width: 100%;
        }

        /* Step Card Modal */
        .step-card-modal {
            width: 100%;
            max-width: 100%;
        }

        .step-card-body {
            width: 100%;
            overflow-x: hidden;
        }

        /* Quantity Pad - Force full width */
        .quantity-pad {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .qty-btn {
            width: 100%;
            min-width: 0;
            padding: 12px 8px;
            font-size: 0.9rem;
        }

        .qty-display {
            width: 100%;
            min-width: 0;
            padding: 12px 8px;
            font-size: 1.3rem;
        }

        /* Registration Form - Force full width */
        .registration-form {
            width: 100%;
        }

        .form-group-modal {
            width: 100%;
        }

        .form-group-modal input,
        .form-group-modal select {
            width: 100%;
            min-width: 0;
        }

        /* Payment Methods - Force full width */
        .payment-methods {
            width: 100%;
        }

        .payment-method-card {
            width: 100%;
        }

        .payment-field {
            width: 100%;
            flex-wrap: wrap;
        }

        .field-value {
            word-break: break-all;
            overflow-wrap: break-word;
        }

        /* Raffle Info Container */
        .raffle-info-left {
            height: 180px;
        }

        .raffle-placeholder-modal {
            font-size: 2.5rem;
        }

        .raffle-info-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .raffle-info-desc {
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Metadata Section */
        .raffle-metadata {
            padding: 15px;
            gap: 12px;
            margin-bottom: 20px;
        }

        .raffle-meta-item {
            padding: 8px 12px;
        }

        .raffle-meta-item i {
            font-size: 1rem;
        }

        .raffle-meta-label {
            font-size: 0.8rem;
        }

        .raffle-meta-value {
            font-size: 0.85rem;
        }

        /* Countdown Timer */
        .raffle-countdown-container {
            padding: 15px;
        }

        .raffle-countdown-label {
            font-size: 0.8rem;
            margin-bottom: 8px;
        }

        .raffle-countdown-cta {
            font-size: 0.9rem;
        }

        /* Sorteo Date */
        .raffle-sorteo-date {
            padding: 10px 15px;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .raffle-sorteo-date i {
            font-size: 1rem;
        }

        .raffle-sorteo-date span {
            font-size: 0.85rem;
        }

        /* Prizes Section */
        .raffle-prizes-section {
            margin-bottom: 20px;
        }

        .raffle-prizes-title {
            font-size: 1rem;
            margin-bottom: 12px;
        }

        .prize-card {
            padding: 12px 15px;
            gap: 12px;
        }

        .prize-order {
            width: 30px;
            height: 30px;
            font-size: 0.85rem;
        }

        .prize-title {
            font-size: 0.9rem;
        }

        .prize-description {
            font-size: 0.8rem;
        }

        .prize-value {
            font-size: 0.8rem;
        }

        /* Participation Grid */
        .participation-grid {
            gap: 20px;
        }

        .participation-col-left,
        .participation-col-right {
            gap: 20px;
        }

        /* Step Cards in Modal */
        .step-card-header {
            padding: 12px 15px;
            gap: 10px;
        }

        .step-number {
            width: 28px;
            height: 28px;
            font-size: 1rem;
        }

        .step-card-header h4 {
            font-size: 1rem;
        }

        .step-card-body {
            padding: 15px;
        }

        .step-card-footer {
            padding: 12px 15px;
        }

        /* Quantity Pad */
        .quantity-pad {
            gap: 8px;
        }

        .qty-btn {
            padding: 12px;
            font-size: 0.9rem;
        }

        .qty-display {
            padding: 12px;
            font-size: 1.3rem;
        }

        .quantity-status {
            font-size: 0.85rem;
        }

        /* Registration Form */
        .registration-form {
            gap: 14px;
        }

        .form-group-modal {
            gap: 6px;
        }

        .form-group-modal label {
            font-size: 0.875rem;
        }

        .form-group-modal input,
        .form-group-modal select {
            padding: 10px 12px;
            font-size: 0.875rem;
        }

        /* Payment Methods */
        .payment-method-header {
            padding: 12px;
        }

        .payment-method-body {
            padding: 12px;
        }

        .payment-field {
            padding: 8px 0;
            flex-wrap: wrap;
            gap: 8px;
        }

        .field-label {
            font-size: 0.8rem;
        }

        .field-value {
            font-size: 0.85rem;
        }

        .btn-copy {
            padding: 5px 10px;
            font-size: 0.8rem;
        }
    }

    /* ==================== MODAL RESPONSIVE - PANTALLAS MUY PEQUEÑAS ==================== */
    @media (max-width: 480px) {

        /* Modal Container */
        .participation-modal {
            padding: 5px;
            overflow-x: hidden;
        }

        .participation-modal-content {
            margin: 5px;
            border-radius: 8px;
            width: calc(100% - 10px);
            max-width: calc(100% - 10px);
        }

        .participation-modal-header {
            padding: 12px 15px;
        }

        .participation-modal-title {
            font-size: 1.1rem;
        }

        .participation-modal-close {
            font-size: 24px;
        }

        .participation-modal-body {
            padding: 15px 10px;
            padding-bottom: 130px; /* Extra padding for stacked super footer on small screens */
            width: 100%;
        }

        /* Force smaller padding on step card body */
        .step-card-body {
            padding: 12px 10px;
        }

        /* Quantity Pad - Even smaller */
        .quantity-pad {
            gap: 6px;
        }

        .qty-btn {
            padding: 10px 6px;
            font-size: 0.85rem;
        }

        .qty-display {
            padding: 10px 6px;
            font-size: 1.1rem;
        }

        /* Form inputs */
        .form-group-modal input,
        .form-group-modal select {
            padding: 10px 8px;
            font-size: 0.85rem;
        }

        /* Raffle Info Container */
        .raffle-info-left {
            height: 150px;
        }

        .raffle-info-right {
            padding: 15px;
        }

        .raffle-info-title {
            font-size: 1.1rem;
        }

        .raffle-info-desc {
            font-size: 0.85rem;
        }

        /* Metadata Section */
        .raffle-metadata {
            padding: 12px;
            gap: 10px;
            margin-bottom: 15px;
        }

        /* Countdown Timer - Escala reducida adicional */
        .raffle-countdown {
            gap: 4px;
        }

        .countdown-unit {
            min-width: 40px;
        }

        .countdown-value {
            font-size: 1rem;
            padding: 6px 8px;
            min-width: 35px;
        }

        .countdown-label {
            font-size: 0.6rem;
        }

        .raffle-countdown-container {
            padding: 12px;
        }

        /* Sorteo Date */
        .raffle-sorteo-date {
            padding: 8px 12px;
            font-size: 0.8rem;
        }

        .raffle-sorteo-date i {
            font-size: 0.9rem;
        }

        /* Prizes Section */
        .prize-card {
            padding: 10px 12px;
            gap: 10px;
        }

        .prize-order {
            width: 26px;
            height: 26px;
            font-size: 0.75rem;
        }

        .prize-title {
            font-size: 0.85rem;
        }

        .prize-description {
            font-size: 0.75rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .prize-value {
            font-size: 0.75rem;
        }

        /* Quantity Pad */
        .qty-btn {
            padding: 10px;
            font-size: 0.85rem;
        }

        .qty-display {
            padding: 10px;
            font-size: 1.2rem;
        }

        /* Form Elements */
        .form-group-modal input,
        .form-group-modal select {
            padding: 9px 10px;
            font-size: 0.85rem;
        }

        .form-divider {
            margin: 8px 0;
        }

        /* Payment Methods */
        .payment-method-card {
            border-radius: 8px;
        }

        .payment-method-header {
            padding: 10px;
            gap: 8px;
        }

        .payment-method-header label {
            font-size: 0.85rem;
        }

        .payment-method-body {
            padding: 10px;
        }

        .payment-field {
            flex-direction: column;
            align-items: flex-start;
        }

        .field-value {
            width: 100%;
            text-align: left;
            margin: 0;
        }

        .btn-copy {
            width: 100%;
            margin-top: 5px;
        }

        /* Super Footer */
        .total-super-footer {
            padding: 10px 15px;
        }

        .amount-label {
            font-size: 0.85rem;
        }

        .amount-value {
            font-size: 1.1rem;
        }

        .btn-process-ticket {
            padding: 12px 20px;
            font-size: 0.9rem;
        }
    }

    /* Fade Up Animation Classes */
    .fade-up-element {
        opacity: 0;
        transform: translateY(40px);
        transition: opacity 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94), transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        will-change: opacity, transform;
    }

    .fade-up-element.in-view {
        opacity: 1;
        transform: translateY(0);
    }

    .fade-up-element.out-view {
        opacity: 0;
        transform: translateY(-40px);
    }

    /* Stagger delays for step cards */
    .steps-grid .step-card:nth-child(1) {
        transition-delay: 0.1s;
    }

    .steps-grid .step-card:nth-child(2) {
        transition-delay: 0.2s;
    }

    .steps-grid .step-card:nth-child(3) {
        transition-delay: 0.3s;
    }

    .steps-grid .step-card:nth-child(4) {
        transition-delay: 0.4s;
    }

    @media (max-width: 767px) {
        .steps-grid .step-card {
            transition-delay: 0s;
        }

        /* Remove stagger on mobile for better UX on scroll */
    }

    /* ==================== PAYMENT METHODS STYLES ==================== */
    .payment-methods {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .payment-method-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        background: #ffffff;
    }

    .payment-method-card:hover {
        border-color: #0066cc;
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.1);
    }

    .payment-method-card:has(input[type="radio"]:checked) {
        border-color: #0066cc;
        background: rgba(0, 102, 204, 0.02);
        box-shadow: 0 4px 16px rgba(0, 102, 204, 0.15);
    }

    .payment-method-header {
        padding: 15px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .payment-method-card:has(input[type="radio"]:checked) .payment-method-header {
        background: rgba(0, 102, 204, 0.08);
    }

    .payment-method-header input[type="radio"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #0066cc;
    }

    .payment-method-header label {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        margin: 0;
        font-weight: 600;
        color: #333;
    }

    .payment-method-header i {
        font-size: 1.2rem;
        color: #0066cc;
    }

    .payment-method-body {
        padding: 15px;
        display: none;
    }

    .payment-method-card:has(input[type="radio"]:checked) .payment-method-body {
        display: block;
    }

    .payment-field {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .payment-field:last-child {
        border-bottom: none;
    }

    .field-label {
        font-weight: 600;
        color: #666;
        font-size: 0.9rem;
    }

    .field-value {
        font-family: 'Courier New', monospace;
        color: #333;
        font-size: 0.95rem;
        margin: 0 10px;
        flex: 1;
        text-align: right;
    }

    .btn-copy {
        background: #0066cc;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.85rem;
    }

    .btn-copy:hover {
        background: #0052a3;
        transform: scale(1.05);
    }

    .btn-copy.copied {
        background: #28a745;
    }

    .loading-spinner {
        text-align: center;
        padding: 30px;
        color: #666;
    }

    .loading-spinner i {
        font-size: 2rem;
        margin-bottom: 10px;
        display: block;
    }

    .no-methods,
    .error-message {
        text-align: center;
        padding: 30px;
        color: #999;
        font-style: italic;
    }

    .error-message {
        color: #dc3545;
    }
</style>

<div class="site-index">

    <?php if (!empty($mejoresRifas)): ?>

        <!-- Rifas Activas Section -->
        <section id="mejores-rifas" class="rifas-section">
            <div class="container-custom">
                <div class="section-header fade-up-element">
                    <h2 class="section-title">Rifas Activas</h2>
                    <p class="section-description">Explora nuestras rifas disponibles y selecciona la que más te guste</p>
                </div>

                <div class="rifas-grid">
                    <?php
                    $delay = 0;
                    foreach ($mejoresRifas as $rifa):
                        // Determinar ranking
                        $rankIndex = array_search($rifa->id, $rankingIds);
                        $isTop1 = ($rankIndex === 0);
                        $rankNumber = $rankIndex + 1;
                        $isTop3 = ($rankIndex !== false && $rankIndex < 3);

                        // Clase para animación extra si es el #1
                        $extraClass = $isTop1 ? 'top-seller-glow' : '';
                        ?>
                        <div class="rifa-card fade-up-element <?= $extraClass ?>" data-fecha-fin="
                <?= $rifa->fecha_fin ?>" style="animation-delay: <?= $delay ?>s">
                            <?php if ($isTop3): ?>
                                <div class="ranking-badge text-muted-blue">
                                    <i class="fas fa-crown"></i> #
                                    <?= $rankNumber ?> MÁS VENDIDA
                                </div>
                            <?php endif; ?>

                            <?php
                            $delay += 0.3; // Incremento de 0.3s por tarjeta
                            ?>
                            <!-- Imagen -->
                            <div class="rifa-card-image">
                                <?php if ($rifa->img): ?>
                                    <?= Html::img($rifa->img, [
                                        'alt' => Html::encode($rifa->titulo),
                                    ]) ?>
                                <?php else: ?>
                                    <div class="rifa-placeholder">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                <?php endif; ?>

                                <?php
                                $simbolo = ($rifa->moneda === 'USD') ? '$' : 'Bs.';
                                $textoPrecio = "PRECIO POR BOLETO " . $simbolo . " " . number_format($rifa->precio_boleto, 2);
                                ?>
                                <div class="rifa-precio-badge">
                                    <div class="marquee-track">
                                        <!-- Texto duplicado para efecto infinito -->
                                        <span><?= $textoPrecio ?></span>
                                        <span><?= $textoPrecio ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenido -->
                            <div class="rifa-card-body">
                                <h3 class="rifa-titulo">
                                    <?= Html::encode($rifa->titulo) ?>
                                </h3>
                                <p class="rifa-descripcion"><?= Html::encode($rifa->descripcion) ?></p>

                                <!-- Progreso -->
                                <?php $porcentaje = $rifa->getPorcentajeVendido(); ?>
                                <div class="rifa-progreso">
                                    <div class=" progreso-info">
                                        <span>
                                            <?= number_format($porcentaje, 0) ?>% vendido
                                        </span>
                                        <span><?= $rifa->getNumerosDisponibles() ?> disponibles</span>
                                    </div>
                                    <div class="progreso-barra">
                                        <div class="progreso-fill" style="width: <?= $porcentaje ?>%"></div>
                                    </div>
                                </div>

                                <?php
                                // Preparar datos adicionales para el modal
                                $premiosData = [];
                                foreach ($rifa->getPremios()->orderBy(['orden' => SORT_ASC])->all() as $premio) {
                                    $premiosData[] = [
                                        'orden' => $premio->orden,
                                        'titulo' => $premio->titulo,
                                        'descripcion' => $premio->descripcion,
                                        'valor_estimado' => $premio->valor_estimado,
                                    ];
                                }
                                $segundosHastaFin = $rifa->getSegundosHastaFinRecaudacion();
                                $fechaSorteo = $rifa->getFechaSorteo();
                                ?>
                                <button type="button" class="btn-participar" onclick="openParticipationModal(
                                        <?= $rifa->id ?>, 
                                        <?= Html::encode(json_encode($rifa->titulo)) ?>, 
                                        <?= $rifa->precio_boleto ?>, 
                                        '<?= $rifa->moneda ?>', 
                                        <?= $rifa->max_numeros ?>, 
                                        <?= $rifa->getNumerosDisponibles() ?>, 
                                        <?= Html::encode(json_encode($rifa->img)) ?>, 
                                        <?= Html::encode(json_encode($rifa->descripcion)) ?>,
                                        <?= Html::encode(json_encode($rifa->fecha_inicio)) ?>,
                                        <?= $segundosHastaFin !== null ? $segundosHastaFin : 'null' ?>,
                                        <?= Html::encode(json_encode($fechaSorteo)) ?>,
                                        <?= Html::encode(json_encode($premiosData)) ?>
                                    )">
                                    ¡PARTICIPA YA!
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Steps Section -->
        <section class="steps-section">
            <div class="container-custom">
                <div class="section-header fade-up-element">
                    <h2 class="section-title">¿Cómo Participar?</h2>
                    <p class="section-description">Sigue estos sencillos pasos para adquirir tus boletos</p>
                </div>

                <div class="steps-grid">
                    <!-- Paso 1 -->
                    <div class="step-card fade-up-element">
                        <div class="step-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <h4 class="step-title">Selecciona la cantidad</h4>
                        <p class="step-description">Elige cuántos boletos deseas comprar</p>
                    </div>

                    <!-- Paso 2 -->
                    <div class="step-card fade-up-element">
                        <div class="step-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <h4 class="step-title">Completa tus datos</h4>
                        <p class="step-description">Ingresa tu información personal de contacto</p>
                    </div>

                    <!-- Paso 3 -->
                    <div class="step-card fade-up-element">
                        <div class="step-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h4 class="step-title">Realiza el pago</h4>
                        <p class="step-description">Paga mediante nuestros métodos disponibles</p>
                    </div>

                    <!-- Paso 4 -->
                    <div class="step-card fade-up-element">
                        <div class="step-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4 class="step-title">Verifica tu boleto</h4>
                        <p class="step-description">Consulta el estado de tus boletos en el verificador</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Verificador Section (Rediseñado) -->
        <div id="verificador-custom-section">
            <section class="verificador-section">
                <div class="container-custom">

                    <div class="verificador-card fade-up-element">
                        <div class="verificador-content">
                            <h3>Consulta tus Boletos</h3>
                            <p>Ingresa tu número de cédula para ver el estado de tus compras</p>
                        </div>

                        <form id="verificador-form">
                            <div class="input-wrapper">
                                <i class="fas fa-search input-icon"></i>
                                <input type="text" id="cedula-input" class="custom-input"
                                    placeholder="Número de Cédula (Ej: 12345678)" required>
                            </div>
                            <button type="submit" class="btn-buscar-moderno">
                                Consultar Ahora
                            </button>
                        </form>

                        <div id="verificador-resultados" class="verificador-resultados">
                            <!-- Resultados AJAX -->
                        </div>
                    </div>

                </div>
            </section>
        </div>

    <?php endif; ?>

</div>

<!-- Modal de Participación -->
<div id="participationModal" class="participation-modal">
    <div class="participation-modal-content">
        <div class="participation-modal-header">
            <h3 class="participation-modal-title" id="participationTitle">Participar en Rifa</h3>
            <span class="participation-modal-close" onclick="closeParticipationModal()">&times;</span>
        </div>
        <div class="participation-modal-body">
            <!-- Nuevo Contenedor de Información de la Rifa -->
            <div class="raffle-info-container">
                <div class="raffle-info-left">
                    <img id="modalRaffleImg" src="" alt="Imagen Rifa" class="raffle-info-img" style="display: none;">
                    <div id="modalRafflePlaceholder" class="raffle-placeholder-modal" style="display: none;">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
                <div class="raffle-info-right">
                    <h3 id="modalRaffleTitle" class="raffle-info-title"></h3>
                    <div id="modalRaffleDesc" class="raffle-info-desc"></div>
                </div>
            </div>

            <!-- Metadatos de la Rifa -->
            <div class="raffle-metadata" id="raffleMetadata">
                <div class="raffle-meta-row">
                    <div class="raffle-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="raffle-meta-label">Inicia:</span>
                        <span class="raffle-meta-value" id="modalFechaInicio">--</span>
                    </div>
                </div>

                <!-- Contador Regresivo -->
                <div class="raffle-countdown-container" id="countdownContainer">
                    <div class="raffle-countdown-label">Tiempo restante para participar</div>
                    <div class="raffle-countdown" id="countdownTimer">
                        <div class="countdown-unit">
                            <span class="countdown-value" id="countdownDays">00</span>
                            <span class="countdown-label">Días</span>
                        </div>
                        <div class="countdown-unit">
                            <span class="countdown-value" id="countdownHours">00</span>
                            <span class="countdown-label">Horas</span>
                        </div>
                        <div class="countdown-unit">
                            <span class="countdown-value" id="countdownMinutes">00</span>
                            <span class="countdown-label">Min</span>
                        </div>
                        <div class="countdown-unit">
                            <span class="countdown-value" id="countdownSeconds">00</span>
                            <span class="countdown-label">Seg</span>
                        </div>
                    </div>
                    <p class="raffle-countdown-cta">¡Participa YA!</p>
                </div>

                <!-- Fecha del Sorteo -->
                <div class="raffle-sorteo-date" id="sorteoDateContainer">
                    <i class="fas fa-trophy"></i>
                    <span>El sorteo se realizará el</span>
                    <span class="raffle-sorteo-value" id="modalFechaSorteo">--</span>
                </div>
            </div>

            <!-- Sección de Premios -->
            <div class="raffle-prizes-section" id="rafflePrizesSection">
                <h4 class="raffle-prizes-title">
                    <i class="fas fa-gift"></i>
                    Premios de esta Rifa
                </h4>
                <div class="prizes-stack" id="prizesStack">
                    <!-- Los premios se cargarán dinámicamente -->
                </div>
            </div>

            <div class="participation-grid">
                <!-- Columna Izquierda: Paso 1 y Paso 3 -->
                <div class="participation-col-left">
                    <!-- Tarjeta Paso 1: CANTIDAD -->
                    <div class="step-card-modal">
                        <div class="step-card-header">
                            <span class="step-number">1</span>
                            <h4>CANTIDAD</h4>
                        </div>
                        <div class="step-card-body">
                            <div class="quantity-pad">
                                <!-- Fila 1: +2, +5, +10 -->
                                <button type="button" class="qty-btn qty-add" onclick="addQuantity(2)">+2</button>
                                <button type="button" class="qty-btn qty-add" onclick="addQuantity(5)">+5</button>
                                <button type="button" class="qty-btn qty-add" onclick="addQuantity(10)">+10</button>
                                <!-- Fila 2: +20, +50, +100 -->
                                <button type="button" class="qty-btn qty-add" onclick="addQuantity(20)">+20</button>
                                <button type="button" class="qty-btn qty-add" onclick="addQuantity(50)">+50</button>
                                <button type="button" class="qty-btn qty-add" onclick="addQuantity(100)">+100</button>
                                <!-- Fila 3: -, Total, + -->
                                <button type="button" class="qty-btn qty-minus" onclick="addQuantity(-1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <div class="qty-display" id="quantityDisplay">0</div>
                                <button type="button" class="qty-btn qty-plus" onclick="addQuantity(1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="step-card-footer">
                            <span id="quantityStatus" class="quantity-status">Seleccione cantidad...</span>
                        </div>
                    </div>

                    <!-- Tarjeta Paso 3: PAGO -->
                    <div class="step-card-modal">
                        <div class="step-card-header">
                            <span class="step-number">3</span>
                            <h4>PAGO</h4>
                        </div>
                        <div class="step-card-body">
                            <div id="paymentMethodsContainer" class="payment-methods">
                                <div class="loading-spinner">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <span>Cargando métodos de pago...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Paso 2 -->
                <div class="participation-col-right">
                    <!-- Tarjeta Paso 2: REGISTRO -->
                    <div class="step-card-modal">
                        <div class="step-card-header">
                            <span class="step-number">2</span>
                            <h4>REGISTRO</h4>
                        </div>
                        <div class="step-card-body">
                            <form id="registrationForm" class="registration-form">
                                <div class="form-group-modal">
                                    <label for="playerCedula">
                                        <i class="fas fa-id-card"></i> Cédula *
                                    </label>
                                    <input type="text" id="playerCedula" name="cedula" placeholder="Ej: 12345678"
                                        pattern="[0-9]+" required>
                                </div>
                                <div class="form-group-modal">
                                    <label for="playerNombre">
                                        <i class="fas fa-user"></i> Nombre Completo *
                                    </label>
                                    <input type="text" id="playerNombre" name="nombre" placeholder="Juan Pérez"
                                        required>
                                </div>
                                <div class="form-group-modal">
                                    <label for="playerPais">
                                        <i class="fas fa-globe-americas"></i> País
                                    </label>
                                    <select id="playerPais" name="pais">
                                        <option value="">Seleccione su país</option>
                                        <option value="VE">🇻🇪 Venezuela</option>
                                        <option value="CO">🇨🇴 Colombia</option>
                                        <option value="PE">🇵🇪 Perú</option>
                                        <option value="EC">🇪🇨 Ecuador</option>
                                        <option value="CL">🇨🇱 Chile</option>
                                        <option value="AR">🇦🇷 Argentina</option>
                                        <option value="MX">🇲🇽 México</option>
                                        <option value="PA">🇵🇦 Panamá</option>
                                        <option value="CR">🇨🇷 Costa Rica</option>
                                        <option value="DO">🇩🇴 Rep. Dominicana</option>
                                        <option value="GT">🇬🇹 Guatemala</option>
                                        <option value="HN">🇭🇳 Honduras</option>
                                        <option value="SV">🇸🇻 El Salvador</option>
                                        <option value="NI">🇳🇮 Nicaragua</option>
                                        <option value="BO">🇧🇴 Bolivia</option>
                                        <option value="PY">🇵🇾 Paraguay</option>
                                        <option value="UY">🇺🇾 Uruguay</option>
                                        <option value="CU">🇨🇺 Cuba</option>
                                        <option value="PR">🇵🇷 Puerto Rico</option>
                                    </select>
                                </div>
                                <div class="form-group-modal">
                                    <label for="playerTelefono">
                                        <i class="fas fa-phone"></i> Teléfono *
                                    </label>
                                    <input type="tel" id="playerTelefono" name="telefono" placeholder="04121234567"
                                        required>
                                </div>
                                <div class="form-group-modal">
                                    <label for="playerCorreo">
                                        <i class="fas fa-envelope"></i> Correo Electrónico
                                    </label>
                                    <input type="email" id="playerCorreo" name="correo"
                                        placeholder="correo@ejemplo.com">
                                </div>
                                <hr class="form-divider">
                                <div class="form-group-modal">
                                    <label for="paymentRef">
                                        <i class="fas fa-receipt"></i> Referencia de Pago *
                                    </label>
                                    <input type="text" id="paymentRef" name="transaction_id" placeholder="Ej: 123456789"
                                        required>
                                </div>
                                <div class="form-group-modal">
                                    <label for="paymentProof">
                                        <i class="fas fa-camera"></i> Comprobante de Pago *
                                    </label>
                                    <input type="file" id="paymentProof" name="comprobante" accept="image/*" required>
                                    <div id="proofPreview" class="proof-preview"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Superfooter de Monto Total -->
<div id="totalSuperFooter" class="total-super-footer">
    <div class="super-footer-content">
        <div class="super-footer-amount">
            <span class="amount-label">Monto Total:</span>
            <span class="amount-value" id="totalAmount">Bs. 0.00</span>
        </div>
        <button type="button" class="btn-process-ticket" id="processTicketBtn" onclick="processTicket()">
            <i class="fas fa-check-circle"></i> Procesar Boleto
        </button>
    </div>
</div>

<script>
    // Verificador de Tickets
    document.addEventListener('DOMContentLoaded', function () {
        const verificadorForm = document.getElementById('verificador-form');
        if (verificadorForm) {
            verificadorForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const cedula = document.getElementById('cedula-input').value;
                const resultadosDiv = document.getElementById('verificador-resultados');

                if (!cedula) return;

                resultadosDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p>Buscando tickets...</p></div>';

                fetch('<?= Url::to(['site/buscar-tickets']) ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
                    },
                    body: 'cedula=' + encodeURIComponent(cedula)
                })
                    .then(response => response.text())
                    .then(html => {
                        resultadosDiv.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        resultadosDiv.innerHTML = '<div class="alert alert-danger">Ocurrió un error al buscar los tickets.</div>';
                    });
            });
        }
    });

    // ==================== MODAL DE PARTICIPACIÓN ====================

    // Estado del modal de participación
    const ParticipationState = {
        rifaId: null,
        rifaTitle: '',
        precioUnitario: 0,
        moneda: 'VES',
        maxNumeros: 0,
        numerosDisponibles: 0, cantidad: 0,
        numerosAsignados: [],
        isProcessing: false,
        countdownInterval: null,
        segundosRestantes: null
    };

    // Formatear fecha para mostrar
    function formatDateForDisplay(dateString) {
        if (!dateString) return '--';
        const date = new Date(dateString);
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return date.toLocaleDateString('es-ES', options);
    }

    // Actualizar el contador regresivo
    function updateCountdown() {
        if (ParticipationState.segundosRestantes === null || ParticipationState.segundosRestantes <= 0) {
            document.getElementById('countdownDays').textContent = '00';
            document.getElementById('countdownHours').textContent = '00';
            document.getElementById('countdownMinutes').textContent = '00';
            document.getElementById('countdownSeconds').textContent = '00';

            if (ParticipationState.countdownInterval) {
                clearInterval(ParticipationState.countdownInterval);
                ParticipationState.countdownInterval = null;
            }

            // Mostrar mensaje de tiempo agotado
            const ctaEl = document.querySelector('.raffle-countdown-cta');
            if (ctaEl && ParticipationState.segundosRestantes !== null) {
                ctaEl.textContent = '¡Tiempo agotado!';
                ctaEl.style.color = '#dc3545';
            }
            return;
        }

        const totalSeconds = ParticipationState.segundosRestantes;
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        document.getElementById('countdownDays').textContent = String(days).padStart(2, '0');
        document.getElementById('countdownHours').textContent = String(hours).padStart(2, '0');
        document.getElementById('countdownMinutes').textContent = String(minutes).padStart(2, '0');
        document.getElementById('countdownSeconds').textContent = String(seconds).padStart(2, '0');

        ParticipationState.segundosRestantes--;
    }

    // Iniciar contador regresivo
    function startCountdown(segundos) {
        // Limpiar intervalo anterior si existe
        if (ParticipationState.countdownInterval) {
            clearInterval(ParticipationState.countdownInterval);
        }

        ParticipationState.segundosRestantes = segundos;

        // Restaurar estilo del CTA
        const ctaEl = document.querySelector('.raffle-countdown-cta');
        if (ctaEl) {
            ctaEl.textContent = '¡Participa YA!';
            ctaEl.style.color = '#ffc107';
        }

        // Mostrar/ocultar el contenedor del countdown según si hay tiempo restante
        const countdownContainer = document.getElementById('countdownContainer');
        if (segundos === null || segundos === undefined) {
            countdownContainer.style.display = 'none';
            return;
        }
        countdownContainer.style.display = 'block';

        // Actualizar inmediatamente
        updateCountdown();

        // Iniciar intervalo de 1 segundo
        ParticipationState.countdownInterval = setInterval(updateCountdown, 1000);
    }

    // Renderizar premios
    function renderPrizes(premios) {
        const prizesStack = document.getElementById('prizesStack');
        const prizesSection = document.getElementById('rafflePrizesSection');

        if (!premios || premios.length === 0) {
            prizesSection.style.display = 'none';
            return;
        }

        prizesSection.style.display = 'block';

        prizesStack.innerHTML = premios.map((premio, index) => {
            const valorFormateado = premio.valor_estimado
                ? `$${parseFloat(premio.valor_estimado).toLocaleString()}`
                : '';

            return `
                <div class="prize-card">
                    <div class="prize-order">${premio.orden || (index + 1)}</div>
                    <div class="prize-info">
                        <h5 class="prize-title">${escapeHtml(premio.titulo)}</h5>
                        ${premio.descripcion ? `<p class="prize-description">${escapeHtml(premio.descripcion)}</p>` : ''}
                    </div>
                    ${valorFormateado ? `<span class="prize-value">${valorFormateado}</span>` : ''}
                </div>
            `;
        }).join('');
    }

    // Escapar HTML para seguridad
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Abrir modal de participación
    function openParticipationModal(rifaId, titulo, precio, moneda, maxNumeros, disponibles, imgUrl, descripcion, fechaInicio, segundosHastaFin, fechaSorteo, premios) {
        // Verificar si aceptó términos
        if (typeof TermsManager !== 'undefined' && !TermsManager.isAccepted()) {
            const termsModal = document.getElementById('termsModal');
            if (termsModal) {
                termsModal.classList.add('show');
            }
            alert('Debe aceptar los términos y condiciones antes de participar.');
            return;
        }

        // Inicializar estado
        ParticipationState.rifaId = rifaId;
        ParticipationState.rifaTitle = titulo;
        ParticipationState.precioUnitario = precio;
        ParticipationState.moneda = moneda;
        ParticipationState.maxNumeros = maxNumeros;
        ParticipationState.numerosDisponibles = disponibles;
        ParticipationState.cantidad = 0;
        ParticipationState.numerosAsignados = [];

        // Actualizar UI - Header
        document.getElementById('participationTitle').textContent = 'Participar en Rifa';

        // Actualizar Info Rifa (Imagen)
        const imgEl = document.getElementById('modalRaffleImg');
        const placeholderEl = document.getElementById('modalRafflePlaceholder');

        if (imgUrl) {
            imgEl.src = imgUrl;
            imgEl.style.display = 'block';
            placeholderEl.style.display = 'none';
        } else {
            imgEl.style.display = 'none';
            placeholderEl.style.display = 'flex';
        }

        // Actualizar título y descripción
        document.getElementById('modalRaffleTitle').textContent = titulo;
        document.getElementById('modalRaffleDesc').textContent = descripcion || '';

        // Actualizar metadatos
        document.getElementById('modalFechaInicio').textContent = formatDateForDisplay(fechaInicio);

        // Actualizar fecha de sorteo
        const sorteoContainer = document.getElementById('sorteoDateContainer');
        if (fechaSorteo) {
            document.getElementById('modalFechaSorteo').textContent = formatDateForDisplay(fechaSorteo);
            sorteoContainer.style.display = 'flex';
        } else {
            sorteoContainer.style.display = 'none';
        }

        // Iniciar contador regresivo
        startCountdown(segundosHastaFin);

        // Renderizar premios
        renderPrizes(premios);

        // Reset cantidad
        document.getElementById('quantityDisplay').textContent = '0';
        document.getElementById('quantityStatus').textContent = 'Seleccione cantidad...';
        document.getElementById('quantityStatus').className = 'quantity-status';
        updateTotalAmount();

        // Limpiar formulario
        document.getElementById('registrationForm').reset();
        document.getElementById('proofPreview').innerHTML = '';

        // Mostrar modal
        document.getElementById('participationModal').classList.add('show');

        // Cargar métodos de pago
        loadPaymentMethods();
    }

    // Cerrar modal de participación
    function closeParticipationModal() {
        document.getElementById('participationModal').classList.remove('show');
        hideSuperFooter();

        // Limpiar intervalo del contador
        if (ParticipationState.countdownInterval) {
            clearInterval(ParticipationState.countdownInterval);
            ParticipationState.countdownInterval = null;
        }
    }

    // Añadir cantidad
    function addQuantity(amount) {
        let newQty = ParticipationState.cantidad + amount;

        // Límites
        if (newQty < 0) newQty = 0;

        // Límite: Mínimo entre maxNumeros (total rifa) y numerosDisponibles (reales)
        // Aunque maxNumeros es fijo, el límite real es lo que queda.
        const limiteReal = ParticipationState.numerosDisponibles;

        if (newQty > limiteReal) {
            newQty = limiteReal;
        }

        ParticipationState.cantidad = newQty;
        document.getElementById('quantityDisplay').textContent = newQty;

        // Actualizar estado visual
        const statusEl = document.getElementById('quantityStatus');
        if (newQty === 0) {
            statusEl.textContent = 'Seleccione cantidad...';
            statusEl.className = 'quantity-status';
            hideSuperFooter();
        } else {
            statusEl.textContent = 'Boletos seleccionados ✓';
            statusEl.className = 'quantity-status status-assigned';
            showSuperFooter();
        }

        updateTotalAmount();
    }

    // Actualizar monto total
    function updateTotalAmount() {
        const total = ParticipationState.cantidad * ParticipationState.precioUnitario;
        const simbolo = ParticipationState.moneda === 'USD' ? '$' : 'Bs.';
        document.getElementById('totalAmount').textContent = `${simbolo} ${total.toFixed(2)}`;
    }

    // Mostrar superfooter
    function showSuperFooter() {
        document.getElementById('totalSuperFooter').classList.add('show');
    }

    // Ocultar superfooter
    function hideSuperFooter() {
        document.getElementById('totalSuperFooter').classList.remove('show');
    }

    // Cargar métodos de pago
    async function loadPaymentMethods() {
        const container = document.getElementById('paymentMethodsContainer');
        container.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i><span>Cargando métodos de pago...</span></div>';

        try {
            const response = await fetch('<?= \yii\helpers\Url::to(['boletos/obtener-metodos-pago']) ?>');
            const data = await response.json();

            if (data.success && data.metodos.length > 0) {
                container.innerHTML = data.metodos.map(metodo => renderPaymentMethod(metodo)).join('');
            } else {
                container.innerHTML = '<div class="no-methods">No hay métodos de pago disponibles</div>';
            }
        } catch (error) {
            console.error('Error loading payment methods:', error);
            container.innerHTML = '<div class="error-message">Error al cargar métodos de pago</div>';
        }
    }

    // Renderizar método de pago
    function renderPaymentMethod(metodo) {
        const fieldsHtml = Object.entries(metodo.fields).map(([key, value]) => {
            const labels = {
                banco: 'Banco',
                titular: 'Titular',
                cedula: 'Cédula',
                telefono: 'Teléfono',
                nro_cuenta: 'Nro. Cuenta'
            };
            return `
                <div class="payment-field">
                    <span class="field-label">${labels[key] || key}:</span>
                    <span class="field-value">${value}</span>
                    <button type="button" class="btn-copy" onclick="copyToClipboard('${value}', this)" title="Copiar">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            `;
        }).join('');

        return `
            <div class="payment-method-card">
                <div class="payment-method-header">
                    <input type="radio" name="payment_method" value="${metodo.id}" id="payment_method_${metodo.id}" required>
                    <label for="payment_method_${metodo.id}">
                        <i class="fas fa-credit-card"></i>
                        <span>${metodo.tipo}</span>
                    </label>
                </div>
                <div class="payment-method-body">
                    ${fieldsHtml}
                </div>
            </div>
        `;
    }

    // Copiar al portapapeles
    async function copyToClipboard(text, button) {
        try {
            await navigator.clipboard.writeText(text);
            const originalIcon = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.add('copied');
            setTimeout(() => {
                button.innerHTML = originalIcon;
                button.classList.remove('copied');
            }, 2000);
        } catch (err) {
            console.error('Error al copiar:', err);
        }
    }

    // Preview del comprobante
    document.addEventListener('DOMContentLoaded', function () {
        const proofInput = document.getElementById('paymentProof');
        if (proofInput) {
            proofInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                const preview = document.getElementById('proofPreview');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.innerHTML = `<img src="${e.target.result}" alt="Comprobante">`;
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = '';
                }
            });
        }
    });

    // Procesar boleto
    async function processTicket() {
        if (ParticipationState.isProcessing) return;

        // Validaciones
        if (ParticipationState.cantidad < 1) {
            alert('Debe seleccionar al menos 1 boleto');
            return;
        }

        // Validar que se haya seleccionado un método de pago
        const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!selectedPaymentMethod) {
            alert('Debe seleccionar un método de pago');
            return;
        }

        const form = document.getElementById('registrationForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        ParticipationState.isProcessing = true;
        const btn = document.getElementById('processTicketBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        btn.disabled = true;

        try {
            // Obtener datos del formulario
            const formData = new FormData(form);

            // Convertir comprobante a base64
            let comprobanteBase64 = null;
            const proofFile = document.getElementById('paymentProof').files[0];
            if (proofFile) {
                comprobanteBase64 = await fileToBase64(proofFile);
            }

            // Preparar datos
            const requestData = {
                id_rifa: ParticipationState.rifaId,
                cantidad: ParticipationState.cantidad,
                acepta_condiciones: true,
                jugador: {
                    cedula: formData.get('cedula'),
                    nombre: formData.get('nombre'),
                    pais: formData.get('pais'),
                    telefono: formData.get('telefono'),
                    correo: formData.get('correo')
                },
                pago: {
                    id_metodo_pago: parseInt(selectedPaymentMethod.value),
                    transaction_id: formData.get('transaction_id'),
                    comprobante: comprobanteBase64
                }
            };

            // Enviar al servidor
            const response = await fetch('<?= \yii\helpers\Url::to(['boletos/procesar-boleto']) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData)
            });

            const result = await response.json();

            if (result.success) {
                // Éxito
                alert(`${result.mensaje}\n\nCódigo de boleto: ${result.boleto.codigo}\nNúmeros: ${result.boleto.numeros.join(', ')}\nTotal: ${result.boleto.total} ${result.boleto.moneda}`);
                closeParticipationModal();
                // Recargar página para actualizar disponibilidad
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar el boleto. Por favor intente de nuevo.');
        } finally {
            ParticipationState.isProcessing = false;
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    // Convertir archivo a base64
    function fileToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
        });
    }

    // Cerrar modal al hacer clic fuera
    document.addEventListener('click', function (event) {
        const modal = document.getElementById('participationModal');
        if (event.target === modal) {
            closeParticipationModal();
        }
    });

    // Cerrar con tecla Escape
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeParticipationModal();
        }
    });

</script>

<script>
    // Intersection Observer for Fade Up / Fade Out Up Animation
    document.addEventListener('DOMContentLoaded', function () {
        const observerOptions = {
            root: null, // viewport
            rootMargin: '0px',
            threshold: 0.1 // Trigger when 10% of element is visible
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                const element = entry.target;
                if (entry.isIntersecting) {
                    // Element entering viewport
                    element.classList.add('in-view');
                    element.classList.remove('out-view');
                } else {
                    // Element leaving viewport

                    // We check if it is leaving to the top or bottom
                    // If boundingClientRect.y < 0, it means it scrolled UP out of view (Top)
                    // If boundingClientRect.y > 0, it means it scrolled DOWN out of view (Bottom)

                    if (entry.boundingClientRect.y < 0) {
                        // Leaving to the top -> Fade Out Up
                        element.classList.add('out-view');
                        element.classList.remove('in-view');
                    } else {
                        // Leaving to the bottom -> Reset to initial state (ready to fade up again)
                        element.classList.remove('in-view');
                        element.classList.remove('out-view');
                    }
                }
            });
        }, observerOptions);

        const fadeElements = document.querySelectorAll('.fade-up-element');
        fadeElements.forEach(el => observer.observe(el));
    });
</script>