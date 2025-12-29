<!-- Modal de Aprobación Manual -->
<div class="modal fade" id="aprobacionModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-user-shield me-2"></i>Aprobar Boleto Manualmente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <!-- Paso 1: Verificación de Contraseña -->
                <div id="aprobacion-paso-1" style="display: block;">
                    <div
                        style="background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin-bottom: 20px; border-radius: 6px;">
                        <h6 style="margin: 0 0 10px 0; color: #92400e; font-weight: 600;">
                            <i class="fas fa-info-circle me-1"></i>Información Importante
                        </h6>
                        <p style="margin: 0; font-size: 0.9rem; color: #78350f; line-height: 1.6;">
                            Si el jugador se contactó con usted para aclarar la situación, puede aprobar el boleto. Al
                            hacerlo:
                        </p>
                        <ul style="margin: 10px 0 0 0; padding-left: 20px; font-size: 0.85rem; color: #78350f;">
                            <li>Se le asignarán números disponibles (si los suyos fueron tomados)</li>
                            <li>El boleto pasará a estado "Pagado" y confirmado</li>
                            <li>Se le notificará al jugador por correo electrónico</li>
                            <li style="font-weight: 600; color: #92400e;">
                                Números disponibles en la rifa: <span
                                    id="numeros-disponibles-count"><?= $rifa->getNumerosDisponibles() ?></span>
                            </li>
                        </ul>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password-confirmation"
                            style="font-weight: 600; margin-bottom: 8px; display: block;">
                            <i class="fas fa-lock me-1"></i>Ingrese su contraseña para confirmar su identidad
                        </label>
                        <input type="password" class="form-control" id="password-confirmation" placeholder="••••••••"
                            style="padding: 12px; font-size: 1rem;">
                        <small class="text-muted">Por seguridad, debe confirmar su identidad antes de continuar.</small>
                    </div>

                    <button type="button" class="btn w-100" id="btn-verificar-password"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 12px; font-weight: 600; font-size: 1rem;">
                        <i class="fas fa-arrow-right me-2"></i>Verificar y Continuar
                    </button>
                </div>

                <!-- Paso 2: Confirmación Final -->
                <div id="aprobacion-paso-2" style="display: none;">
                    <div
                        style="background: #ecfdf5; border-left: 4px solid #10b981; padding: 20px; margin-bottom: 25px; border-radius: 6px; text-align: center;">
                        <i class="fas fa-check-circle"
                            style="font-size: 3rem; color: #10b981; margin-bottom: 15px;"></i>
                        <h4 style="margin: 0 0 10px 0; color: #065f46; font-weight: 700;">
                            Identidad Verificada
                        </h4>
                        <p style="margin: 0; color: #047857; font-size: 0.95rem;">
                            Ahora puede aprobar el boleto del jugador
                        </p>
                    </div>

                    <div style="text-align: center; margin-bottom: 25px;">
                        <button type="button" id="btn-aprobar-boleto" class="btn btn-lg"
                            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 18px 50px; font-weight: 700; font-size: 1.1rem; border-radius: 50px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
                            <i class="fas fa-check-double me-2"></i>Aprobar Pago y Boleto
                        </button>
                    </div>

                    <div class="alert alert-warning" style="margin: 0; font-size: 0.85rem;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Atención:</strong> Esta acción confirmará el pago y activará la participación del
                        jugador en la rifa.
                    </div>
                </div>

                <!-- Loading State -->
                <div id="aprobacion-loading" style="display: none; text-align: center; padding: 30px;">
                    <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Procesando...</span>
                    </div>
                    <p style="margin-top: 15px; color: #6b7280;">Procesando aprobación...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Esperar a que Bootstrap esté disponible
    function initAprobacionModal() {
        if (typeof bootstrap === 'undefined') {
            // Bootstrap aún no está cargado, intentar de nuevo en 100ms
            setTimeout(initAprobacionModal, 100);
            return;
        }

        var aprobacionModalEl = document.getElementById('aprobacionModal');
        var aprobacionModal = new bootstrap.Modal(aprobacionModalEl);
        var boletoId = <?= $boleto->id ?>;
        var rifaId = <?= $rifa->id ?>;

        // Vincular evento al botón de abrir modal
        var btnAbrir = document.getElementById('btn-abrir-modal-aprobacion');
        if (btnAbrir) {
            btnAbrir.addEventListener('click', function () {
                // Reset modal al estado inicial
                document.getElementById('aprobacion-paso-1').style.display = 'block';
                document.getElementById('aprobacion-paso-2').style.display = 'none';
                document.getElementById('aprobacion-loading').style.display = 'none';
                document.getElementById('password-confirmation').value = '';

                aprobacionModal.show();
            });
        }

        // Botón verificar contraseña
        var btnVerificar = document.getElementById('btn-verificar-password');
        if (btnVerificar) {
            btnVerificar.addEventListener('click', function () {
                var password = document.getElementById('password-confirmation').value;

                if (!password) {
                    alert('Por favor ingrese su contraseña');
                    return;
                }

                var btn = this;
                var originalHTML = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verificando...';

                fetch('<?= \yii\helpers\Url::to(['/panel/verificar-password']) ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
                    },
                    body: JSON.stringify({ password: password })
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;

                        if (data.success) {
                            document.getElementById('aprobacion-paso-1').style.display = 'none';
                            document.getElementById('aprobacion-paso-2').style.display = 'block';
                        } else {
                            alert('Contraseña incorrecta. Por favor intente nuevamente.');
                            document.getElementById('password-confirmation').value = '';
                            document.getElementById('password-confirmation').focus();
                        }
                    })
                    .catch(function (error) {
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                        alert('Error al verificar la contraseña');
                        console.error(error);
                    });
            });
        }

        // Botón aprobar boleto
        var btnAprobar = document.getElementById('btn-aprobar-boleto');
        if (btnAprobar) {
            btnAprobar.addEventListener('click', function () {
                document.getElementById('aprobacion-paso-2').style.display = 'none';
                document.getElementById('aprobacion-loading').style.display = 'block';

                fetch('<?= \yii\helpers\Url::to(['/panel/aprobar-boleto-anulado']) ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
                    },
                    body: JSON.stringify({
                        boletoId: boletoId,
                        rifaId: rifaId
                    })
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.success) {
                            aprobacionModal.hide();

                            var mensaje = '¡Boleto aprobado exitosamente!\n\n';
                            if (data.numerosReasignados) {
                                mensaje += 'Se reasignaron los siguientes números:\n' + data.numerosNuevos.join(', ');
                            } else {
                                mensaje += 'Se mantuvieron los números originales.';
                            }
                            mensaje += '\n\nSe ha enviado un correo al jugador confirmando su participación.';

                            alert(mensaje);
                            location.reload();
                        } else {
                            aprobacionModal.hide();
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(function (error) {
                        aprobacionModal.hide();
                        alert('Error al aprobar el boleto');
                        console.error(error);
                    });
            });
        }
    }

    // Iniciar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAprobacionModal);
    } else {
        initAprobacionModal();
    }
</script>