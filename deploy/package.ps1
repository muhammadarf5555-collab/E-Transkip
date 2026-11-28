$ProgressPreference = 'SilentlyContinue'
$ErrorActionPreference = 'Stop'

if (Test-Path deploy/staging) {
    Remove-Item -Force -Recurse deploy/staging
}
New-Item -ItemType Directory -Force -Path deploy/staging | Out-Null

Copy-Item -Recurse app deploy/staging/app
Copy-Item -Recurse resources deploy/staging/resources
Copy-Item -Recurse routes deploy/staging/routes
Copy-Item -Recurse config deploy/staging/config
Copy-Item -Recurse public/build deploy/staging/build
Copy-Item public/sync.php deploy/staging/sync.php

if (Test-Path deploy/update-tampilan.zip) {
    Remove-Item -Force deploy/update-tampilan.zip
}

Compress-Archive -Path deploy/staging/* -DestinationPath deploy/update-tampilan.zip

Remove-Item -Force -Recurse deploy/staging

Get-Item deploy/update-tampilan.zip | Select-Object Name, Length, LastWriteTime
