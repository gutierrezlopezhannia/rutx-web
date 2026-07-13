# Documentación de Estructura Mínima para Reportes Financieros — RUTX Web

Este documento define la estructura de datos mínima requerida en la base de datos (y emulada mediante mocks en la fase M1) para dar soporte a los reportes financieros del módulo de **Ventas** de la plataforma RUTX.

---

## 1. Módulo: Cobranza (Día 1)
Registra los cobros realizados por los vendedores/repartidores en campo a las facturas pendientes de clientes.

### Estructura de Datos (Esquema Mínimo)
| Campo | Tipo | Descripción | Ejemplo |
|---|---|---|---|
| `ruta` | `string` | Nombre o identificador de la ruta operativa. | `"Ruta 1"` |
| `vendedor` | `string` | Nombre completo del vendedor que realiza el cobro. | `"Ana María"` |
| `cliente` | `string` | Razón social o nombre comercial del cliente. | `"Supermercado San José"` |
| `recibo` | `string` | Folio único del recibo de cobranza. | `"REC-0045"` |
| `tipo_pago` | `string` | Método utilizado para liquidar (Efectivo, Transferencia, Cheque, Crédito). | `"Efectivo"` |
| `monto` | `decimal(10,2)` | Cantidad monetaria cobrada. | `1200.00` |
| `fecha_hora` | `datetime` | Fecha y hora en que se registró el cobro. | `"2026-06-20 09:15 AM"` |

---

## 2. Módulo: Reporte de Utilidad (Día 2)
Permite visualizar la rentabilidad bruta y neta de las ventas realizadas dentro de un período de tiempo filtrado.

### Estructura de Datos (Esquema Mínimo)
| Campo | Tipo | Descripción | Ejemplo |
|---|---|---|---|
| `fecha` | `date` | Fecha de la operación. | `"2026-07-13"` |
| `ventas_totales` | `decimal(12,2)` | Ingresos totales por ventas brutas. | `450000.00` |
| `costo_ventas` | `decimal(12,2)` | Costo de adquisición/fabricación de los productos vendidos. | `280000.00` |
| `gastos_operativos`| `decimal(12,2)` | Gastos asociados a la distribución (gasolina, viáticos, etc.). | `35000.00` |
| `impuestos` | `decimal(12,2)` | Monto acumulado de impuestos cobrados (IVA, IEPS). | `72000.00` |

---

## 3. Módulo: Ventas por Cliente (Día 3)
Consolida el historial comercial y financiero de compras de cada cliente.

### Estructura de Datos (Esquema Mínimo)
| Campo | Tipo | Descripción | Ejemplo |
|---|---|---|---|
| `cliente_id` | `integer` | ID único de cliente en Microsip. | `102` |
| `cliente_nombre` | `string` | Nombre comercial del cliente. | `"Abarrotes La Esperanza"` |
| `total_comprado` | `decimal(12,2)` | Acumulado histórico de compras facturadas. | `85400.50` |
| `saldo_pendiente`| `decimal(10,2)` | Deuda activa que el cliente tiene con la empresa. | `12500.00` |
| `limite_credito` | `decimal(10,2)` | Límite máximo autorizado para ventas a crédito. | `20000.00` |
| `estado_cuenta` | `string` | Estado financiero del cliente (Al corriente, Vencido, Suspendido). | `"Al corriente"` |

---

## 4. Módulo: Rentabilidad por Ruta (Día 4)
Mide la eficiencia económica de cada camión o ruta de reparto.

### Estructura de Datos (Esquema Mínimo)
| Campo | Tipo | Descripción | Ejemplo |
|---|---|---|---|
| `ruta_id` | `string` | Identificador de la ruta. | `"Ruta 1"` |
| `ventas_ruta` | `decimal(12,2)` | Ingreso total facturado en la ruta. | `15000.00` |
| `gastos_ruta` | `decimal(10,2)` | Suma de gastos operativos reportados por la ruta. | `1200.00` |
| `visitas_efectivas`| `integer` | Número de visitas que concluyeron en venta. | `18` |
| `visitas_totales` | `integer` | Total de clientes agendados en la ruta. | `20` |

---

## 5. Módulo: Depósito Venta / Liquidación (Día 5)
Cuadre de caja para la oficina de administración. Compara el dinero en efectivo entregado por el chofer contra las fichas de depósito bancario.

### Estructura de Datos (Esquema Mínimo)
| Campo | Tipo | Descripción | Ejemplo |
|---|---|---|---|
| `cierre_id` | `string` | Folio de liquidación/cierre del día. | `"LIQ-9872"` |
| `ruta` | `string` | Ruta liquidada. | `"Ruta 2"` |
| `efectivo_sistema`| `decimal(10,2)` | Dinero en efectivo reportado por la app móvil. | `8450.00` |
| `efectivo_fisico` | `decimal(10,2)` | Efectivo real entregado a caja general. | `8450.00` |
| `depositos_banco` | `decimal(10,2)` | Monto reportado en fichas de depósito o transferencias. | `12000.00` |
| `diferencia` | `decimal(10,2)` | Diferencia final (sobrante o faltante). | `0.00` |
