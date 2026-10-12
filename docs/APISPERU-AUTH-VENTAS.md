# Configuración de autenticación, comprobantes y ApisPeru

## Variables de entorno

Copia `.env.example` a `.env` y configura la conexión de base de datos y `APP_KEY`. Añade los valores entregados por ApisPeru en tu `.env` local:

- `APISPERU_URL`: endpoint de consulta de DNI de tu cuenta (por defecto, `https://api.apisperu.com/api/v1/dni`).
- `APISPERU_TOKEN`: token privado de ApisPeru. No lo subas al repositorio ni lo incluyas en el frontend.
- `BUSINESS_RUC`: RUC real del negocio que aparecerá en el PDF.

Después ejecuta:

```bash
php artisan config:clear
php artisan migrate
php artisan storage:link
php artisan test
```

La consulta envía el DNI y el token desde el servidor, aplica timeout y valida que el documento tenga ocho dígitos. Si la API no está configurada, la pantalla muestra un mensaje sin registrar datos inventados.

## Inicio de sesión

Las rutas del POS, los reportes y la impresión requieren sesión autenticada. Crea el primer usuario desde la raíz del proyecto:

```bash
php artisan tinker
```

En Tinker:

```php
\App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@tu-negocio.pe',
    'password' => 'CAMBIA-ESTA-CONTRASENA',
]);
```

Usa una contraseña única y fuerte. El acceso aplica limitación de intentos; cierra sesión desde el menú superior.

## Descuentos y comprobantes

El descuento se guarda en la venta como porcentaje y monto, sin actualizar los precios permanentes del catálogo. El servidor vuelve a consultar los precios y bloquea las filas de productos durante la confirmación para validar stock y evitar confiar en importes enviados por el navegador.

La factura requiere RUC de 11 dígitos y dirección fiscal; la boleta puede usar DNI consultado con ApisPeru. El PDF generado es un comprobante interno del POS: para emitir comprobantes electrónicos con validez tributaria SUNAT se necesita una integración autorizada de facturación electrónica, serie/correlativo y envío/aceptación de SUNAT u OSE/PSE. Este cambio no simula esa autorización.
