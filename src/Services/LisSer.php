<?php

namespace Licon\Lis\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Licon\Lis\Traits\CacheKeys;


class LisSer
{
    use CacheKeys;

    public $license;

    private $li;
    private $codeu;
    private $licenseKey;
    private $co = [];
    private $do = [];
    private $do2 = [];
    private $accessToken = true;

    public function __construct($v)
    {
        $this->codeu = $v;
        $req = $_SERVER;
        $r = $this->getAllCount();
        $this->co = $r['ply'];
        $this->do = $this->getRq($req);
        $this->do2 = $this->getRq2($req);
        $this->li = $this->getAccessTokenKey();
        if (!$this->li['code']) {
            // abort(403, base64_decode("TElDRU5TRSBFWFBJUkVE"));
        }

    }

    /**
     *
     * @param string $licenseKey
     * @param array $data
     *
     * @return boolean
     */
    public function validateL()
    {
        if ($this->accessToken) {

            $folderPath = $this->basePth() . base64_decode('L3N0b3JhZ2UvYXBwL2NvbmZpZy50eHQ=');
            $se = self::crl($this->codeu);
            if ($se['chco'] == 200) {
                if (json_decode($se['chre'], 1)['status'] == 'SUCCESS') {
                    Storage::disk('local')->put('LICENSE.txt', (
                        openssl_encrypt(
                            json_encode(["resp" => @json_decode($se['chre'], true), "error" => @json_decode($se['cher'], true), "code" => @json_decode($se['chco'], 1), "param" => $this->do]),
                            'AES-256-CBC',
                            @json_decode($se['chre'], 1)['data']['ecryptionKey'],
                            OPENSSL_RAW_DATA,
                            "0123456789abcdef"
                        ) . "(c{v{b" . base64_decode(@json_decode($se['chre'], 1)['data']['ecryptionKey'])
                    ));
                    $content = json_encode(['domain' => @$this->do['domain'], "name" => @$this->do['project'], "ip" => @$this->do['ip'], "key" => @env("APP_LI")]);
                    if (!file_exists($folderPath)) {
                        file_put_contents($folderPath, $content);
                    }
                    $this->clELg();
                    return true;

                } elseif (in_array(json_decode($se['chre'], 1)['status'], ['PENDING', "FAILURE"])) {
                    if (file_exists(storage_path('/app/LICENSE.txt'))) {
                        unlink(storage_path('/app/LICENSE.txt'));
                    }
                    abort(403, base64_decode("TElDRU5TRSBFWFBJUkVE"));
                }
            }
            if (file_exists(storage_path('/app/LICENSE.txt'))) {
                unlink(storage_path('/app/LICENSE.txt'));
            }
            abort(403, base64_decode("TElDRU5TRSBFWFBJUkVE"));
        }
        abort(403, base64_decode("TElDRU5TRSBFWFBJUkVE"));
    }

    function crl($codeu)
    {
        $gL = $this->gtELg();
        if ($gL['s']) {
            $olEr = $gL['r'];
        } else {
            $olEr = [];
        }

        $dt = (isset($olEr[array_key_last($olEr)]['resp']['data']['reqTime']) && !empty($olEr[array_key_last($olEr)]['resp']['data']['reqTime'])) ? $olEr[array_key_last($olEr)]['resp']['data']['reqTime'] : date('Y-m-d H:i:s');

        $newtimestamp = strtotime(array_key_last($olEr) ?? date('Y-m-d H:i:s') . ' + ' . $dt ?? 03 . ' minute');
        $newtimestamp = date('Y-m-d H:i:s', $newtimestamp);


        if ((date("Y-m-d H:i:s") >= $newtimestamp) || count($olEr) <= 0) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, base64_decode($codeu));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["requestKey" => base64_encode(json_encode($this->do2))]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            $chre = curl_exec($ch);
            $cher = curl_error($ch);
            $chco = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $this->mkELg(['chre' => $chre, "cher" => $cher, 'chco' => $chco]);

            if (@json_decode($chre, true)['code'] == "0x0207") {
                print_r(json_decode($chre, true)['data']['content']);
                exit;
            }
            return ['chre' => $chre, "cher" => $cher, 'chco' => $chco];
        }

        isset($olEr[array_key_last($olEr)]['resp']['data']['content']) ? print_r($olEr[array_key_last($olEr)]['resp']['data']['content']) : abort(403, base64_decode("TElDRU5TRSBFWFBJUkVE"));

        // print_r($olEr[array_key_last($olEr)]['resp']['data']['content']);
        exit;
        // abort(403, base64_decode("TElDRU5TRSBFWFBJUkVE"));
    }




}
