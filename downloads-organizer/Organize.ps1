# Professional Downloads Folder Organizer
# Version 2.1

param(
    # Folder to organize. Defaults to the current user's Downloads folder.
    [string]$SourcePath = "$env:USERPROFILE\Downloads",

    # Where to write the log file. Defaults to the user's Desktop.
    [string]$LogPath = "$env:USERPROFILE\Desktop\DownloadOrganizer.log"
)

# Configuration
$downloadsPath = $SourcePath
$logFile = $LogPath
$date = Get-Date -Format "yyyy-MM-dd HH:mm:ss"

# Category definitions with file extensions
$categories = @{
    "Documents" = @(".pdf", ".doc", ".docx", ".txt", ".rtf", ".odt", ".xls", ".xlsx", ".ppt", ".pptx", ".csv", ".log")
    "Archives" = @(".zip", ".rar", ".7z", ".tar", ".gz", ".bz2", ".iso", ".img")
    "Images" = @(".jpg", ".jpeg", ".png", ".gif", ".bmp", ".tiff", ".svg", ".ico", ".webp")
    "Videos" = @(".mp4", ".avi", ".mkv", ".mov", ".wmv", ".flv", ".webm", ".m4v", ".mpg", ".mpeg")
    "Audio" = @(".mp3", ".wav", ".flac", ".aac", ".wma", ".m4a", ".ogg")
    "Executables" = @(".exe", ".msi", ".bat", ".cmd", ".ps1", ".vbs", ".js", ".jar")
    "Development" = @(".py", ".java", ".cpp", ".c", ".h", ".js", ".ts", ".html", ".css", ".xml", ".json", ".yml", ".yaml", ".ini", ".conf")
    "Design" = @(".psd", ".ai", ".eps", ".indd", ".sketch", ".fig", ".xd")
    "Fonts" = @(".ttf", ".otf", ".woff", ".woff2", ".eot")
    "Temporary" = @(".tmp", ".temp", ".cache", ".bak", ".old")
    "Installers" = @(".dmg", ".pkg", ".deb", ".rpm", ".apk", ".appimage")
    "Other" = @()
}

# Function to write log
function Write-Log {
    param([string]$message)
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logEntry = "[$timestamp] $message"
    Write-Host $logEntry -ForegroundColor Cyan
    Add-Content -Path $logFile -Value $logEntry
}

# Function to get category based on extension
function Get-Category {
    param([string]$extension)
    
    foreach ($category in $categories.Keys) {
        if ($categories[$category] -contains $extension) {
            return $category
        }
    }
    return "Other"
}

# Function to sanitize folder name
function Get-SanitizedFolderName {
    param([string]$name)
    # Remove invalid characters for folder names
    return $name -replace '[<>:"/\\|?*]', '_'
}

# Start logging
Write-Log "========================================="
Write-Log "Starting Downloads Folder Organization"
Write-Log "Target: $downloadsPath"
Write-Log "========================================="

# Check if Downloads folder exists
if (-not (Test-Path $downloadsPath)) {
    Write-Log "ERROR: Downloads folder not found at $downloadsPath"
    exit 1
}

# Create organized folder structure
$organizedFolder = Join-Path $downloadsPath "Organized_$((Get-Date).ToString('yyyyMMdd_HHmmss'))"
New-Item -ItemType Directory -Path $organizedFolder -Force | Out-Null
Write-Log "Created master organization folder: $organizedFolder"

# Create category folders
foreach ($category in $categories.Keys) {
    $categoryPath = Join-Path $organizedFolder $category
    New-Item -ItemType Directory -Path $categoryPath -Force | Out-Null
}
Write-Log "Created category folders"

# Get all files in Downloads (excluding folders)
$files = Get-ChildItem -Path $downloadsPath -File | Where-Object { $_.Name -notlike "Organized_*" }

$fileCount = $files.Count
$processedCount = 0
$movedCount = 0
$errorCount = 0

Write-Log "Found $fileCount files to organize"

foreach ($file in $files) {
    $processedCount++
    
    try {
        $extension = [System.IO.Path]::GetExtension($file.Name).ToLower()
        $category = Get-Category -extension $extension
        
        # Get base name and create safe version
        $baseName = [System.IO.Path]::GetFileNameWithoutExtension($file.Name)
        $safeBaseName = Get-SanitizedFolderName -name $baseName
        
        # Destination folder
        $destinationFolder = Join-Path $organizedFolder $category
        
        # Check for duplicate files
        $newFileName = $file.Name
        $counter = 1
        
        while (Test-Path (Join-Path $destinationFolder $newFileName)) {
            $newFileName = "$safeBaseName-$counter$extension"
            $counter++
        }
        
        # Move the file
        $destination = Join-Path $destinationFolder $newFileName
        Move-Item -Path $file.FullName -Destination $destination -Force
        
        Write-Log "MOVED: $($file.Name) -> $category\$newFileName"
        $movedCount++
        
    } catch {
        Write-Log "ERROR: Failed to process $($file.Name) - $_"
        $errorCount++
    }
    
    # Progress indicator
    if ($processedCount % 10 -eq 0) {
        Write-Progress -Activity "Organizing Downloads" -Status "Processing files..." -PercentComplete (($processedCount / $fileCount) * 100)
    }
}

# Create summary files
$summary = @"
Downloads Organization Summary
===============================
Date: $date
Total files processed: $fileCount
Files moved: $movedCount
Errors encountered: $errorCount

Category Breakdown:
"@

foreach ($category in $categories.Keys) {
    $categoryPath = Join-Path $organizedFolder $category
    if (Test-Path $categoryPath) {
        $count = (Get-ChildItem -Path $categoryPath -File).Count
        $summary += "`n$category : $count files"
    }
}

$summaryPath = Join-Path $organizedFolder "Organization_Summary.txt"
$summary | Out-File -FilePath $summaryPath -Encoding UTF8
Write-Log "Created summary file: $summaryPath"

# Final report
Write-Log "========================================="
Write-Log "Organization Complete!"
Write-Log "Total files: $fileCount"
Write-Log "Successfully moved: $movedCount"
Write-Log "Errors: $errorCount"
Write-Log "Organized files located at: $organizedFolder"
Write-Log "Summary saved to: $summaryPath"
Write-Log "Log saved to: $logFile"
Write-Log "========================================="

# Display summary in console
Write-Host "`n`n" -NoNewline
Write-Host "========================================" -ForegroundColor Green
Write-Host "     DOWNLOADS ORGANIZED SUCCESSFULLY    " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host "Total Files: $fileCount" -ForegroundColor Yellow
Write-Host "Moved Successfully: $movedCount" -ForegroundColor Green
Write-Host "Errors: $errorCount" -ForegroundColor Red
Write-Host "========================================" -ForegroundColor Green
Write-Host "Location: $organizedFolder" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Green

# Optional: Open the organized folder
$openFolder = Read-Host "`nOpen organized folder? (Y/N)"
if ($openFolder -eq 'Y' -or $openFolder -eq 'y') {
    Explorer $organizedFolder
}