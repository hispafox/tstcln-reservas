# tstcln-reservas

Aplicación de reservas de salas de reuniones: un backend en Symfony con Doctrine y SQLite, y un frontend en Angular.

## Ramas

- `main`: la aplicación tal como está al empezar el curso.
- `submodulo-M.S/<slug>`: una rama por submódulo. Dentro de cada rama, dos etiquetas:
  - `M.S-inicio`: el punto de partida del laboratorio.
  - `M.S-solucion`: la solución, con las pruebas en verde.

Para colocarte en el punto de partida y comparar con la solución:

```powershell
git checkout 1.2-inicio
git diff 1.2-inicio 1.2-solucion
```

## Scripts

Desde la raíz del repositorio:

```powershell
.\scripts\probar-backend.ps1
.\scripts\probar-frontend.ps1
```

## Frontend

Angular 22 con Vitest, Testing Library y los harnesses de Angular CDK, en `frontend/`. Necesita Node.js `^22.22.3`, `^24.15.0` o `^26.0.0`. Las dependencias se instalan con `npm install` dentro de `frontend/`.

En desarrollo escucha en `http://localhost:5173`, y `proxy.conf.json` reenvía las rutas de la API al backend en `https://localhost:8000`.
