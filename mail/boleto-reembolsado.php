<?php
use yii\helpers\Html;

/** @var app\models\Boletos $boleto */
/** @var app\models\Rifas $rifa */
/** @var array $numeros */
/** @var string $statusUrl */
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reembolso Procesado</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f7;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f7; padding: 40px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 40px 30px; text-align: center;">
                            <div style="font-size: 48px; margin-bottom: 15px;">↩️</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                Reembolso Procesado
                            </h1>
                            <p style="margin: 10px 0 0 0; color: rgba(255,255,255,0.9); font-size: 16px;">
                                Boleto #<?= Html::encode($boleto->codigo) ?>
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">

                            <!-- Greeting -->
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #2d3748; line-height: 1.6;">
                                Hola <strong><?= Html::encode($boleto->jugador->nombre) ?></strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; font-size: 16px; color: #2d3748; line-height: 1.6;">
                                Te informamos que el pago de tu boleto para la rifa
                                <strong><?= Html::encode($rifa->titulo) ?></strong> ha sido
                                <strong style="color: #3b82f6;">reembolsado</strong>.
                            </p>

                            <!-- Status Badge -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <div
                                            style="display: inline-block; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border: 2px solid #3b82f6; border-radius: 15px; padding: 20px 30px;">
                                            <div style="font-size: 32px; margin-bottom: 10px;">💰</div>
                                            <div
                                                style="color: #1e40af; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                                Estado: Reembolsado
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Ticket Info -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="background-color: #f7fafc; border-radius: 15px; padding: 20px; margin-bottom: 30px;">
                                <tr>
                                    <td>
                                        <h3
                                            style="margin: 0 0 15px 0; color: #3b82f6; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                                            📋 Detalles del Boleto Reembolsado
                                        </h3>

                                        <table width="100%" cellpadding="8" cellspacing="0">
                                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                                <td style="color: #718096; font-size: 14px; padding: 10px 0;">
                                                    <strong>Código:</strong>
                                                </td>
                                                <td align="right"
                                                    style="color: #2d3748; font-size: 14px; font-weight: 600; padding: 10px 0;">
                                                    #<?= Html::encode($boleto->codigo) ?>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                                <td style="color: #718096; font-size: 14px; padding: 10px 0;">
                                                    <strong>Rifa:</strong>
                                                </td>
                                                <td align="right"
                                                    style="color: #2d3748; font-size: 14px; font-weight: 600; padding: 10px 0;">
                                                    <?= Html::encode($rifa->titulo) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #718096; font-size: 14px; padding: 10px 0;">
                                                    <strong>Monto Reembolsado:</strong>
                                                </td>
                                                <td align="right"
                                                    style="color: #3b82f6; font-size: 18px; font-weight: 700; padding: 10px 0;">
                                                    Bs. <?= number_format($boleto->total_precio, 2, ',', '.') ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info Message -->
                            <div
                                style="background-color: #dbeafe; border-left: 4px solid #3b82f6; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
                                <h4 style="margin: 0 0 10px 0; color: #1e3a8a; font-size: 16px; font-weight: 700;">
                                    ℹ️ Información del Reembolso
                                </h4>
                                <p style="margin: 0; color: #1e40af; line-height: 1.8; font-size: 14px;">
                                    El reembolso se ha procesado exitosamente. Dependiendo de tu método de pago
                                    original, el dinero puede tardar entre 3 a 10 días hábiles en reflejarse en tu
                                    cuenta.
                                </p>
                            </div>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="<?= $statusUrl ?>"
                                            style="display: inline-block; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; text-decoration: none; padding: 15px 40px; border-radius: 50px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);">
                                            Ver Estado del Boleto
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Closing -->
                            <p
                                style="margin: 0; font-size: 14px; color: #718096; line-height: 1.6; text-align: center;">
                                Lamentamos no haber podido procesar tu participación en esta ocasión.<br>
                                ¡Esperamos verte pronto en nuestras próximas rifas!
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color: #f7fafc; padding: 30px; text-align: center; border-top: 2px solid #e2e8f0;">
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #718096;">
                                © <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?>. Todos los derechos reservados.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #a0aec0;">
                                Este correo fue enviado porque tu pago fue reembolsado.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>