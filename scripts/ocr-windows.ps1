# OCR con APIs nativas de Windows 10/11 (sin Tesseract).
# Uso: powershell -NoProfile -ExecutionPolicy Bypass -File ocr-windows.ps1 -InputPath "C:\ruta\archivo.pdf"
param(
    [Parameter(Mandatory = $true)]
    [string]$InputPath
)

$ErrorActionPreference = 'Stop'

if (-not (Test-Path -LiteralPath $InputPath)) {
    Write-Error "Archivo no encontrado: $InputPath"
    exit 2
}

Add-Type -AssemblyName System.Runtime.WindowsRuntime

function Await([object]$WinRtAsyncOperation, [Type]$ResultType) {
    $asTaskGeneric = ([System.WindowsRuntimeSystemExtensions].GetMethods() | Where-Object {
        $_.Name -eq 'AsTask' -and $_.GetParameters().Count -eq 1 -and $_.IsGenericMethod
    })[0]

    $asTask = $asTaskGeneric.MakeGenericMethod($ResultType)
    $netTask = $asTask.Invoke($null, @($WinRtAsyncOperation))
    $netTask.Wait(-1) | Out-Null

    return $netTask.GetType().GetProperty('Result').GetValue($netTask)
}

function AwaitAction([object]$WinRtAsyncAction) {
    $asTaskMethod = ([System.WindowsRuntimeSystemExtensions].GetMethods() | Where-Object {
        $_.Name -eq 'AsTask' -and $_.GetParameters().Count -eq 1 -and -not $_.IsGenericMethod
    })[0]

    $netTask = $asTaskMethod.Invoke($null, @($WinRtAsyncAction))
    $netTask.Wait(-1) | Out-Null
}

[Windows.Storage.StorageFile, Windows.Storage, ContentType = WindowsRuntime] | Out-Null
[Windows.Graphics.Imaging.BitmapDecoder, Windows.Graphics.Imaging, ContentType = WindowsRuntime] | Out-Null
[Windows.Media.Ocr.OcrEngine, Windows.Media.Ocr, ContentType = WindowsRuntime] | Out-Null
[Windows.Data.Pdf.PdfDocument, Windows.Data.Pdf, ContentType = WindowsRuntime] | Out-Null
[Windows.Storage.Streams.InMemoryRandomAccessStream, Windows.Storage.Streams, ContentType = WindowsRuntime] | Out-Null
[Windows.Data.Pdf.PdfPageRenderOptions, Windows.Data.Pdf, ContentType = WindowsRuntime] | Out-Null

$engine = [Windows.Media.Ocr.OcrEngine]::TryCreateFromUserProfileLanguages()
if ($null -eq $engine) {
    $engine = [Windows.Media.Ocr.OcrEngine]::TryCreateFromLanguage([Windows.Globalization.Language]::new('es'))
}
if ($null -eq $engine) {
    Write-Error 'No se pudo inicializar el motor OCR de Windows.'
    exit 3
}

function Get-SoftwareBitmapFromImage([string]$Path) {
    $file = Await ([Windows.Storage.StorageFile]::GetFileFromPathAsync($Path)) ([Windows.Storage.StorageFile])
    $stream = Await ($file.OpenAsync([Windows.Storage.FileAccessMode]::Read)) ([Windows.Storage.Streams.IRandomAccessStreamWithContentType])
    $decoder = Await ([Windows.Graphics.Imaging.BitmapDecoder]::CreateAsync($stream)) ([Windows.Graphics.Imaging.BitmapDecoder])
    return Await ($decoder.GetSoftwareBitmapAsync()) ([Windows.Graphics.Imaging.SoftwareBitmap])
}

function Get-SoftwareBitmapFromPdf([string]$Path) {
    $file = Await ([Windows.Storage.StorageFile]::GetFileFromPathAsync($Path)) ([Windows.Storage.StorageFile])
    $stream = Await ($file.OpenAsync([Windows.Storage.FileAccessMode]::Read)) ([Windows.Storage.Streams.IRandomAccessStreamWithContentType])
    $pdf = Await ([Windows.Data.Pdf.PdfDocument]::LoadFromStreamAsync($stream)) ([Windows.Data.Pdf.PdfDocument])
    if ($pdf.PageCount -lt 1) {
        throw 'PDF sin paginas.'
    }
    $page = $pdf.GetPage(0)
    $mem = New-Object Windows.Storage.Streams.InMemoryRandomAccessStream
    $options = New-Object Windows.Data.Pdf.PdfPageRenderOptions
    AwaitAction ($page.RenderToStreamAsync($mem, $options))
    $mem.Seek(0) | Out-Null
    $decoder = Await ([Windows.Graphics.Imaging.BitmapDecoder]::CreateAsync($mem)) ([Windows.Graphics.Imaging.BitmapDecoder])
    return Await ($decoder.GetSoftwareBitmapAsync()) ([Windows.Graphics.Imaging.SoftwareBitmap])
}

$ext = [System.IO.Path]::GetExtension($InputPath).ToLowerInvariant()

try {
    if ($ext -eq '.pdf') {
        $bitmap = Get-SoftwareBitmapFromPdf -Path $InputPath
    }
    elseif ($ext -in @('.png', '.jpg', '.jpeg', '.bmp', '.tif', '.tiff', '.gif')) {
        $bitmap = Get-SoftwareBitmapFromImage -Path $InputPath
    }
    else {
        Write-Error "Tipo no soportado: $ext"
        exit 4
    }

    $result = Await ($engine.RecognizeAsync($bitmap)) ([Windows.Media.Ocr.OcrResult])
    $text = $result.Text
    if ($null -eq $text) { $text = '' }
    $text = $text.Trim()

    if ($text -eq '') {
        exit 5
    }

    [Console]::OutputEncoding = [System.Text.Encoding]::UTF8
    Write-Output $text
    exit 0
}
catch {
    Write-Error $_.Exception.Message
    exit 1
}
