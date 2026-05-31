using Windows.Data.Pdf;
using Windows.Graphics.Imaging;
using Windows.Media.Ocr;
using Windows.Storage;
using Windows.Storage.Streams;

if (args.Length < 1 || !File.Exists(args[0]))
{
    Console.Error.WriteLine("Uso: WinOcr.exe <ruta-archivo.pdf|png|jpg>");
    return 2;
}

var path = Path.GetFullPath(args[0]);
var ext = Path.GetExtension(path).ToLowerInvariant();

var engine = OcrEngine.TryCreateFromUserProfileLanguages()
             ?? OcrEngine.TryCreateFromLanguage(new Windows.Globalization.Language("es"));

if (engine is null)
{
    Console.Error.WriteLine("No se pudo inicializar OCR de Windows.");
    return 3;
}

try
{
    SoftwareBitmap bitmap = ext switch
    {
        ".pdf" => await RenderPdfFirstPageAsync(path),
        ".png" or ".jpg" or ".jpeg" or ".bmp" or ".tif" or ".tiff" or ".gif" => await LoadImageAsync(path),
        _ => throw new InvalidOperationException($"Tipo no soportado: {ext}"),
    };

    var result = await engine.RecognizeAsync(bitmap);
    var text = (result.Text ?? string.Empty).Trim();

    if (text.Length == 0)
    {
        return 5;
    }

    Console.OutputEncoding = System.Text.Encoding.UTF8;
    Console.Write(text);
    return 0;
}
catch (Exception ex)
{
    Console.Error.WriteLine(ex.Message);
    return 1;
}

static async Task<SoftwareBitmap> LoadImageAsync(string path)
{
    var file = await StorageFile.GetFileFromPathAsync(path);
    var stream = await file.OpenAsync(FileAccessMode.Read);
    var decoder = await BitmapDecoder.CreateAsync(stream);
    return await decoder.GetSoftwareBitmapAsync();
}

static async Task<SoftwareBitmap> RenderPdfFirstPageAsync(string path)
{
    var file = await StorageFile.GetFileFromPathAsync(path);
    var stream = await file.OpenAsync(FileAccessMode.Read);
    var pdf = await PdfDocument.LoadFromStreamAsync(stream);

    if (pdf.PageCount < 1)
    {
        throw new InvalidOperationException("PDF sin paginas.");
    }

    using var page = pdf.GetPage(0);
    var mem = new InMemoryRandomAccessStream();
    var options = new PdfPageRenderOptions();
    await page.RenderToStreamAsync(mem, options);
    mem.Seek(0);

    var decoder = await BitmapDecoder.CreateAsync(mem);
    return await decoder.GetSoftwareBitmapAsync();
}
