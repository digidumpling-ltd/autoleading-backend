# Homepage Images — Upload Guide (S3 / Cloudflare R2)

The site stores uploads on the **configured filesystem disk** (`FILESYSTEM_DISK=s3`,
backed by Cloudflare R2, bucket `autoleading-website`, public host
`https://pub-f1b39cd5b7024f4f95ab93171fbd94ea.r2.dev`). Homepage section images
must live on R2 — **not** local disk — so they survive deploys/rebuilds.

## There is no Media Manager. Two ways to upload an image:

### A. Admin workaround (recommended, no code)
1. Admin → **CMS → Pages** → create a throwaway page (e.g. "scratch").
2. In the TinyMCE editor, use **Insert image → upload**. The editor uploads to
   the configured disk (R2) and inserts an `<img src="https://pub-...r2.dev/...">`.
3. **Copy that R2 URL** from the inserted image's source.
4. Paste the URL wherever you need it (a theme block's image field / HTML).
5. Delete the scratch page (the uploaded file stays on R2).

### B. Via tinker (technical)
```php
use Illuminate\Support\Facades\Storage;
Storage::put('theme/1/my-image.webp', file_get_contents('/path/to/local.webp'), 'public');
echo Storage::url('theme/1/my-image.webp');   // -> the R2 public URL to use
```

## Rules
- Always reference images by their **R2 URL** (`https://pub-...r2.dev/...`), never
  `/storage/...` (local) — local files are not on R2 and break on rebuild.
- Pre-optimise to WebP before upload (R2 has no on-the-fly resize; the homepage
  carousel serves the single image, so size it appropriately, ~1920w for hero).
- The hero carousel image is set in admin: **Settings → Themes → Image Carousel**.
