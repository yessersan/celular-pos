# Catálogo de productos, imágenes y menú lateral

## Mostrar imágenes cargadas

Las imágenes se guardan en el disco público de Laravel, dentro de `storage/app/public/products`. Para que el navegador pueda verlas, crea el enlace público una vez por instalación:

```bash
php artisan storage:link
```

Comprueba que el archivo exista en `storage/app/public/products` y que el enlace `public/storage` apunte a `storage/app/public`. Si despliegas en un hosting, asegúrate de que el directorio `storage` y sus subdirectorios tengan permisos de escritura para el usuario de PHP. La URL se genera ahora con `Storage::disk('public')->url(...)` y normaliza rutas antiguas guardadas con los prefijos `public/` o `storage/`.

## Funciones nuevas

- En el inventario puedes buscar por nombre, filtrar productos disponibles, con stock bajo o agotados y limitar el precio de venta por un mínimo y máximo.
- El botón **Vender** de cada producto con stock abre el punto de venta y agrega ese producto al carrito; no confirma la operación automáticamente. Revisa los datos del cliente, cantidad y descuento antes de confirmar.
- La navegación se muestra como barra lateral fija en escritorio y menú desplegable en pantallas pequeñas.

Ejecuta las pruebas con `php artisan test` después de configurar `.env`, instalar dependencias y ejecutar las migraciones.
