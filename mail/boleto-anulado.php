<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\models\Boletos $boleto */
/** @var app\models\Rifas $rifa */
/** @var array $numeros */
/** @var string $resubmitUrl */
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problema con tu Pago</title>
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
                            style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 40px 30px; text-align: center;">
                            <div style="font-size: 48px; margin-bottom: 15px;">⚠️</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                Problema con tu Pago
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
                                Estuvimos revisando tu pago para la rifa
                                <strong><?= Html::encode($rifa->titulo) ?></strong>
                                y notamos que el pago fue realizado <strong
                                    style="color: #ef4444;">incorrectamente</strong>
                                o el comprobante suministrado no corresponde.
                            </p>

                            <!-- Status Badge -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <div
                                            style="display: inline-block; background: linear-gradient(135deg, #fee 0%, #fecaca 100%); border: 2px solid #ef4444; border-radius: 15px; padding: 20px 30px;">
                                            <div style="font-size: 32px; margin-bottom: 10px;">❌</div>
                                            <div
                                                style="color: #991b1b; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                                Estado: Anulado
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Reason -->
                            <div
                                style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
                                <h4 style="margin: 0 0 10px 0; color: #991b1b; font-size: 16px; font-weight: 700;">
                                    🔍 Razones Posibles
                                </h4>
                                <ul style="margin: 0; padding-left: 20px; color: #dc2626; line-height: 1.8;">
                                    <li>El comprobante no es válido o no corresponde a tu boleto.</li>
                                    <li>El monto del pago no coincide con el precio del boleto.</li>
                                    <li>La transferencia fue realizada a una cuenta incorrecta.</li>
                                    <li>El comprobante presenta inconsistencias o está alterado.</li>
                                </ul>
                            </div>

                            <!-- Numbers Info -->
                            <h3
                                style="margin: 0 0 15px 0; color: #ef4444; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                                🎲 Números del Boleto Anulado
                            </h3>

                            <table width="100%" cellpadding="4" cellspacing="0" style="margin-bottom: 30px;">
                                <tr>
                                    <?php
                                    $counter = 0;
                                    foreach ($numeros as $numero):
                                        if ($counter > 0 && $counter % 5 == 0): ?>
                                        </tr>
                                        <tr>
                                        <?php endif; ?>
                                        <td align="center" style="padding: 5px;">
                                            <div
                                                style="background: #d1d5db; color: #4b5563; padding: 12px; border-radius: 10px; font-size: 16px; font-weight: 700;">
                                                <?= Html::encode($numero) ?>
                                            </div>
                                        </td>
                                        <?php
                                        $counter++;
                                    endforeach;

                                    $remaining = 5 - ($counter % 5);
                                    if ($remaining < 5 && $remaining > 0):
                                        for ($i = 0; $i < $remaining; $i++): ?>
                                            <td></td>
                                        <?php endfor;
                                    endif;
                                    ?>
                                </tr>
                            </table>

                            <!-- CTA Options -->
                            <div
                                style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
                                <h4 style="margin: 0 0 15px 0; color: #92400e; font-size: 16px; font-weight: 700;">
                                    💡 ¿Qué Puedes Hacer?
                                </h4>
                                <p style="margin: 0 0 15px 0; color: #78350f; line-height: 1.6; font-size: 14px;">
                                    Si no estás de acuerdo con esta decisión o crees que es un error, puedes:
                                </p>
                                <ol style="margin: 0; padding-left: 20px; color: #78350f; line-height: 1.8;">
                                    <li><strong>Subir un nuevo comprobante</strong> con la información correcta usando
                                        el
                                        botón de abajo.</li>
                                    <li><strong>Contactarnos</strong> al <strong>+58 XXX XXX-XXXX</strong> para aclarar
                                        la
                                        situación.</li>
                                </ol>
                            </div>

                            <!-- CTA Buttons -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td align="center" style="padding-bottom: 15px;">
                                        <a href="<?= $resubmitUrl ?>"
                                            style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; text-decoration: none; padding: 15px 40px; border-radius: 50px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
                                            📤 Subir Nuevo Comprobante
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0; font-size: 14px; color: #718096;">
                                            o llama al <strong style="color: #2d3748;">+58 XXX XXX-XXXX</strong>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Support -->
                            <p
                                style="margin: 20px 0 0 0; font-size: 14px; color: #718096; line-height: 1.6; text-align: center;">
                                Lamentamos los inconvenientes. Estamos aquí para ayudarte.<br>
                                Queremos que puedas participar en nuestra rifa.
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
                                Este correo fue enviado porque tu pago no pudo ser verificado.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>