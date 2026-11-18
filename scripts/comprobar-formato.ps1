# Comprueba el formato de los dos lados, sin cambiar nada:
# PHP-CS-Fixer en el backend (backend/.php-cs-fixer.dist.php)
# y Prettier en el frontend (frontend/.prettierrc).
# Sale en rojo si cualquiera de los dos encuentra un fichero sin formatear.
# Para arreglarlo: php vendor/bin/php-cs-fixer fix en el backend
# y npx prettier --write . en el frontend.

$ErrorActionPreference = 'Stop'
$fallos = 0

Push-Location (Join-Path $PSScriptRoot '..\backend')
try {
    php vendor/bin/php-cs-fixer check
    if ($LASTEXITCODE -ne 0) { $fallos++ }
}
finally {
    Pop-Location
}

Push-Location (Join-Path $PSScriptRoot '..\frontend')
try {
    # npx.cmd y no npx: el lanzador npx.ps1 vuelve a leer el texto de la orden
    # y pierde lo que llega en @args.
    npx.cmd prettier --check .
    if ($LASTEXITCODE -ne 0) { $fallos++ }
}
finally {
    Pop-Location
}

if ($fallos -gt 0) { exit 1 }
exit 0
