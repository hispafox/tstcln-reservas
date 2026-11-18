# Lanza en tu equipo los pasos del pipeline (.github/workflows/calidad.yml),
# con las mismas órdenes y en el mismo orden: primero el trabajo del backend
# y después el del frontend. Para en el primer paso que falla y dice cuánto
# ha tardado cada uno. Solo comprueba: no cambia nada.
# Los pasos que preparan la máquina (composer install, npm ci) no se lanzan:
# en tu equipo las dependencias ya están instaladas.
# La mutación compara con 8.4-inicio, el punto de partida del pipeline: muta
# solo las líneas que han cambiado desde ahí. El workflow compara con
# origin/main; aquí main es la aplicación del principio del curso.
#   .\scripts\pipeline.ps1
#   .\scripts\pipeline.ps1 -Base <rama o etiqueta>   (con qué compara la mutación)

param(
    [string] $Base = '8.4-inicio'
)

$ErrorActionPreference = 'Stop'
$raiz = Join-Path $PSScriptRoot '..'

function Invoke-Paso([string] $trabajo, [string] $nombre, [scriptblock] $orden) {
    Write-Host ''
    Write-Host "== $trabajo · $nombre" -ForegroundColor Cyan
    $reloj = [Diagnostics.Stopwatch]::StartNew()
    Push-Location (Join-Path $raiz $trabajo)
    try {
        & $orden
        $codigo = $LASTEXITCODE
    }
    finally {
        Pop-Location
    }
    $segundos = [math]::Round($reloj.Elapsed.TotalSeconds, 1)
    if ($codigo -ne 0) {
        Write-Host "== $trabajo · ${nombre}: FALLA ($segundos s)" -ForegroundColor Red
        exit $codigo
    }
    Write-Host "== $trabajo · ${nombre}: bien ($segundos s)" -ForegroundColor Green
}

Invoke-Paso 'backend' 'Formato' {
    php vendor/bin/php-cs-fixer check
}
Invoke-Paso 'backend' 'Análisis estático' {
    php vendor/bin/phpstan analyse --no-progress
}
Invoke-Paso 'backend' 'Capas' {
    php vendor/bin/deptrac analyse --config-file=deptrac.yaml
}
Invoke-Paso 'backend' 'Pruebas' {
    php bin/console doctrine:schema:update --force --complete --env=test
    if ($LASTEXITCODE -ne 0) { return }
    php vendor/bin/phpunit --exclude-group pendiente --coverage-text
}
Invoke-Paso 'backend' 'Pruebas de mutación del código que cambia' {
    php vendor/bin/infection --git-diff-lines "--git-diff-base=$Base" `
        --min-msi=80 --ignore-msi-with-no-mutations `
        '--test-framework-options=--exclude-group=pendiente'
}

# npx.cmd y no npx: el lanzador npx.ps1 vuelve a leer el texto de la orden
# y pierde los argumentos.
Invoke-Paso 'frontend' 'Formato' {
    npx.cmd prettier --check .
}
Invoke-Paso 'frontend' 'Análisis estático y límites' {
    npx.cmd ng lint
}
Invoke-Paso 'frontend' 'Pruebas' {
    npx.cmd ng test --watch=false --coverage
}

Write-Host ''
Write-Host '== Pipeline en verde' -ForegroundColor Green
exit 0
