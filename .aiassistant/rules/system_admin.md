---
apply: always
---

# Project AI Rules

## Estilo
- PSR-12 para PHP.
- Clases en PascalCase, métodos en camelCase, tablas en snake_case plural.
- Usar Form Requests para validaciones.
- Evitar helpers globales de Laravel, preferir inyección de dependencias.

## Arquitectura
- Controladores solo orquestan.
- Reglas de negocio en Services (`App\Services`).
- Consultas complejas en Repositories (`App\Repositories`).
- Respuestas de API con Resources (`App\Http\Resources`).
- Observers para logs de creación, edición y eliminación.

## Dominios
- Empresas ⇄ Productos: relación muchos a muchos vía `company_product`.
- Clientes compran productos: modelo `Purchase` (cabecera) y `PurchaseItem` (detalle).
- Logs de toda acción relevante.

## Multiempresa
- Todas las queries deben filtrar por `company_id`.
- Cache con prefijo `company:{id}`.

## API
- Prefijo `/api/v1`.
- Respuesta estándar:
  ```json
  { "status":"success|error", "data":{...}, "message":"...", "errors":{...}, "meta":{...} }
