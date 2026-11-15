# Pasa las pruebas del frontend con Vitest, una sola vez y sin vigilar cambios.
# Cualquier argumento pasa tal cual a "ng test":
#   .\scripts\probar-frontend.ps1 --coverage
#   .\scripts\probar-frontend.ps1 --include src/app/salas/filtro-salas.spec.ts

$ErrorActionPreference = 'Stop'

Push-Location (Join-Path $PSScriptRoot '..\frontend')
try {
    # npx.cmd y no npx: el lanzador npx.ps1 vuelve a leer el texto de la orden
    # y pierde lo que llega en @args, como --coverage o --include.
    npx.cmd ng test --watch=false @args
    exit $LASTEXITCODE
}
finally {
    Pop-Location
}
