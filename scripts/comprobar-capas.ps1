# Comprueba la regla de capas del backend con Deptrac (backend/deptrac.yaml).
# Sale en rojo si una clase usa otra de una capa que no le está permitida.
# Cualquier argumento pasa tal cual a Deptrac:
#   .\scripts\comprobar-capas.ps1 --report-uncovered

$ErrorActionPreference = 'Stop'
$argumentos = @($args)

Push-Location (Join-Path $PSScriptRoot '..\backend')
try {
    php vendor/bin/deptrac analyse --config-file=deptrac.yaml @argumentos
    exit $LASTEXITCODE
}
finally {
    Pop-Location
}