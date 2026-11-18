# Analiza el backend con PHPStan, sin ejecutar nada (backend/phpstan.dist.neon).
# Sale en rojo con cualquier error que no esté en la línea base.
# Cualquier argumento pasa tal cual a PHPStan:
#   .\scripts\analizar-backend.ps1 -v
#   .\scripts\analizar-backend.ps1 --generate-baseline

$ErrorActionPreference = 'Stop'
$argumentos = @($args)

Push-Location (Join-Path $PSScriptRoot '..\backend')
try {
    php vendor/bin/phpstan analyse @argumentos
    exit $LASTEXITCODE
}
finally {
    Pop-Location
}
