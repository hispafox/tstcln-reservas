# Analiza el frontend con ESLint (frontend/eslint.config.js): los conjuntos
# recomendados, los límites entre funcionalidades y las reglas del dominio.
# Solo comprueba: no cambia nada. Cualquier argumento pasa tal cual a "ng lint":
#   .\scripts\analizar-frontend.ps1 --max-warnings 0

$ErrorActionPreference = 'Stop'

Push-Location (Join-Path $PSScriptRoot '..\frontend')
try {
    # npx.cmd y no npx: el lanzador npx.ps1 vuelve a leer el texto de la orden
    # y pierde lo que llega en @args.
    npx.cmd ng lint @args
    exit $LASTEXITCODE
}
finally {
    Pop-Location
}
