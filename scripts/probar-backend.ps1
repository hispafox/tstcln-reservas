# Pasa las pruebas del backend con PHPUnit.
# Prepara antes el esquema de la base de datos de pruebas (SQLite).
# Sin argumentos deja fuera el grupo "pendiente". Cualquier argumento pasa tal cual a PHPUnit:
#   .\scripts\probar-backend.ps1 --testsuite unitarias
#   .\scripts\probar-backend.ps1 --testdox
#   .\scripts\probar-backend.ps1 --group pendiente

$ErrorActionPreference = 'Stop'
$argumentos = @($args)

Push-Location (Join-Path $PSScriptRoot '..\backend')
try {
    php bin/console doctrine:schema:update --force --complete --env=test --quiet
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

    if ($argumentos -notcontains '--group') {
        $argumentos = @('--exclude-group', 'pendiente') + $argumentos
    }

    php vendor/bin/phpunit @argumentos
    exit $LASTEXITCODE
}
finally {
    Pop-Location
}
