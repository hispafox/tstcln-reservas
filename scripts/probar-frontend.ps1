# Pasa las pruebas del frontend con Vitest, una sola vez y sin vigilar cambios.
# Cualquier argumento pasa tal cual a "ng test":
#   .\scripts\probar-frontend.ps1 --coverage
#   .\scripts\probar-frontend.ps1 --include src/app/logica/filtro-salas.spec.ts

$ErrorActionPreference = 'Stop'

Push-Location (Join-Path $PSScriptRoot '..\frontend')
try {
    npx ng test --watch=false @args
    exit $LASTEXITCODE
}
finally {
    Pop-Location
}
