# Restart Nuxt Dev Server
Write-Host "🔄 Restarting Nuxt Dev Server..." -ForegroundColor Cyan

# Stop any running node processes on port 3000
Write-Host "`n📋 Checking for processes on port 3000..." -ForegroundColor Yellow
$process = Get-NetTCPConnection -LocalPort 3000 -ErrorAction SilentlyContinue | Select-Object -ExpandProperty OwningProcess -ErrorAction SilentlyContinue
if ($process) {
    Write-Host "⚠️  Found process on port 3000. Stopping..." -ForegroundColor Yellow
    Stop-Process -Id $process -Force -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 2
}

# Clear .nuxt cache
Write-Host "`n🧹 Clearing .nuxt cache..." -ForegroundColor Yellow
if (Test-Path .nuxt) {
    Remove-Item -Recurse -Force .nuxt -ErrorAction SilentlyContinue
    Write-Host "✅ .nuxt folder cleared" -ForegroundColor Green
} else {
    Write-Host "ℹ️  .nuxt folder doesn't exist" -ForegroundColor Gray
}

# Clear node_modules/.cache if it exists
if (Test-Path "node_modules\.cache") {
    Write-Host "🧹 Clearing node_modules cache..." -ForegroundColor Yellow
    Remove-Item -Recurse -Force "node_modules\.cache" -ErrorAction SilentlyContinue
    Write-Host "✅ Cache cleared" -ForegroundColor Green
}

# Start dev server
Write-Host "`n🚀 Starting Nuxt dev server..." -ForegroundColor Green
Write-Host "`n📝 Instructions:" -ForegroundColor Cyan
Write-Host "   1. After the server starts, open: http://localhost:3000/admin/orders/109/edit" -ForegroundColor White
Write-Host "   2. Press Ctrl+Shift+R (or Cmd+Shift+R on Mac) to HARD REFRESH the page" -ForegroundColor White
Write-Host "   3. Look for the 'Update Shipping Status' dropdown in the Order Information section" -ForegroundColor White
Write-Host "`n⏳ Starting server...`n" -ForegroundColor Yellow

npm run dev

