<!-- Pending Payments Modal -->
<div class="modal fade" id="pendingPaymentsModal" tabindex="-1" aria-labelledby="pendingPaymentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="pendingPaymentsModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Pagos Pendientes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>Atención:</strong> Hay pagos pendientes por un total de Bs. {{ number_format($stats['pending_payments'], 2) }}
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Tratamiento</th>
                                <th>Fecha</th>
                                <th>Monto Total</th>
                                <th>Pagado</th>
                                <th>Pendiente</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Juan Pérez</td>
                                <td>Ortodoncia</td>
                                <td>15/05/2025</td>
                                <td>Bs. 3,500.00</td>
                                <td>Bs. 1,500.00</td>
                                <td>Bs. 2,000.00</td>
                                <td>
                                    <button class="btn btn-sm btn-success register-payment-btn" data-patient-id="1" data-treatment-id="1">
                                        <i class="fas fa-money-bill-wave"></i> Registrar Pago
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>María González</td>
                                <td>Implante dental</td>
                                <td>22/06/2025</td>
                                <td>Bs. 5,000.00</td>
                                <td>Bs. 2,000.00</td>
                                <td>Bs. 3,000.00</td>
                                <td>
                                    <button class="btn btn-sm btn-success register-payment-btn" data-patient-id="2" data-treatment-id="2">
                                        <i class="fas fa-money-bill-wave"></i> Registrar Pago
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Carlos Rodríguez</td>
                                <td>Puente dental</td>
                                <td>10/06/2025</td>
                                <td>Bs. 4,200.00</td>
                                <td>Bs. 2,000.00</td>
                                <td>Bs. 2,200.00</td>
                                <td>
                                    <button class="btn btn-sm btn-success register-payment-btn" data-patient-id="3" data-treatment-id="3">
                                        <i class="fas fa-money-bill-wave"></i> Registrar Pago
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Ana Martínez</td>
                                <td>Tratamiento de conducto</td>
                                <td>05/07/2025</td>
                                <td>Bs. 2,500.00</td>
                                <td>Bs. 1,000.00</td>
                                <td>Bs. 1,500.00</td>
                                <td>
                                    <button class="btn btn-sm btn-success register-payment-btn" data-patient-id="4" data-treatment-id="4">
                                        <i class="fas fa-money-bill-wave"></i> Registrar Pago
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Roberto López</td>
                                <td>Blanqueamiento dental</td>
                                <td>25/06/2025</td>
                                <td>Bs. 1,800.00</td>
                                <td>Bs. 800.00</td>
                                <td>Bs. 1,000.00</td>
                                <td>
                                    <button class="btn btn-sm btn-success register-payment-btn" data-patient-id="5" data-treatment-id="5">
                                        <i class="fas fa-money-bill-wave"></i> Registrar Pago
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Sofía García</td>
                                <td>Extracción de muelas del juicio</td>
                                <td>18/06/2025</td>
                                <td>Bs. 3,000.00</td>
                                <td>Bs. 1,900.00</td>
                                <td>Bs. 1,100.00</td>
                                <td>
                                    <button class="btn btn-sm btn-success register-payment-btn" data-patient-id="6" data-treatment-id="6">
                                        <i class="fas fa-money-bill-wave"></i> Registrar Pago
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Diego Flores</td>
                                <td>Prótesis dental</td>
                                <td>30/06/2025</td>
                                <td>Bs. 6,000.00</td>
                                <td>Bs. 1,000.00</td>
                                <td>Bs. 5,000.00</td>
                                <td>
                                    <button class="btn btn-sm btn-success register-payment-btn" data-patient-id="7" data-treatment-id="7">
                                        <i class="fas fa-money-bill-wave"></i> Registrar Pago
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="{{ route('payments.index') }}" class="btn btn-primary">Ir a Gestión de Pagos</a>
            </div>
        </div>
    </div>
</div>

<!-- Register Payment Modal -->
<div class="modal fade" id="registerPaymentModal" tabindex="-1" aria-labelledby="registerPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerPaymentModalLabel">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" id="payment-patient-id">
                    <input type="hidden" id="payment-treatment-id">
                    
                    <div class="mb-3">
                        <label for="payment-date" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="payment-date" value="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment-amount" class="form-label">Monto (Bs.)</label>
                        <input type="number" step="0.01" class="form-control" id="payment-amount" placeholder="0.00">
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment-method" class="form-label">Método de Pago</label>
                        <select class="form-select" id="payment-method">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                            <option value="transferencia">Transferencia Bancaria</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment-reference" class="form-label">Referencia/Comprobante</label>
                        <input type="text" class="form-control" id="payment-reference" placeholder="Número de comprobante o referencia">
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment-notes" class="form-label">Notas</label>
                        <textarea class="form-control" id="payment-notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="savePaymentBtn">Guardar Pago</button>
            </div>
        </div>
    </div>
</div>