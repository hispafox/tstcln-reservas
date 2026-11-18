$ErrorActionPreference = 'Stop'
Push-Location (Join-Path $PSScriptRoot '..\backend')
try {
    php bin/console app:cargar-datos-iniciales
    exit $LASTEXITCODE
}
finally {
    Pop-Location
}
