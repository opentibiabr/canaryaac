<?php
/**
 * ClientEditor Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use ZipArchive;

class ClientEditor{

    private $battlEyeDisableBytes;

    private $rsa;

    private $services;

    public function __construct()
    {
        $this->battlEyeDisableBytes = [
            "\xC6\x45\xD7\x00\xC6\x45\xCF\x00" => "\xC6\x45\xD7\x00\xC6\x45\xCF\x00", // 12.64
            "\x8D\x8D\x70\xFF\xFF\xFF\x75\x0E" => "\x8D\x8D\x70\xFF\xFF\xFF\xEB\x0E", // 12.72 <> 12.75
            "\x8D\x4D\x84\x75\x0E\xE8\x1B\x41" => "\x8D\x4D\x84\xEB\x0E\xE8\x1B\x41", // 12.86
            "\x8D\x4D\x84\x75\x0E\xE8\xDB\x3F" => "\x8D\x4D\x84\xEB\x0E\xE8\xDB\x3F", // 12.87.12038
            "\x8D\x4D\x84\x75\x0E\xE8\xF8\x24" => "\x8D\x4D\x84\xEB\x0E\xE8\xF8\x24", // 12.87.12047
            "\x8D\x4D\x8C\x75\x0E\xE8\xB6\xD3" => "\x8D\x4D\x8C\xEB\x0E\xE8\xB6\xD3", // 12.90
            "\x8D\x4D\x80\x75\x0E\xE8\x2E\xC8" => "\x8D\x4D\x80\xEB\x0E\xE8\x2E\xC8", // 12.91.12329
            "\x8D\x8D\x6C\xFF\xFF\xFF\x75\x0E" => "\x8D\x8D\x6C\xFF\xFF\xFF\xEB\x0E", // 12.91.12383 <> 12.91.12424
            "\x8D\x4D\xB8\x75\x0E\xE8\x34\x79" => "\x8D\x4D\xB8\xEB\x0E\xE8\x34\x79" // 13.16.13209
        ];
        $this->rsa = '9B646903B45B07AC956568D87353BD7165139DD7940703B03E6DD079399661B4' .
            'A837AA60561D7CCB9452FA0080594909882AB5BCA58A1A1B35F8B1059B72B12' .
            '12611C6152AD3DBB3CFBEE7ADC142A75D3D75971509C321C5C24A5BD51FD460' .
            'F01B4E15BEB0DE1930528A5D3F15C1E3CBF5C401D6777E10ACAAB33DBE8D5B7FF5';
        $this->services = [
            'loginWebService' => 'http://127.0.0.1/api/v1/login',
            'clientWebService' => 'http://127.0.0.1/api/v1/login',
            'tibiaPageUrl' => '',
            'tibiaStoreGetCoinsUrl' => '',
            'getPremiumUrl' => '',
            'createAccountUrl' => '',
            'createTournamentCharacterUrl' => '',
            'accessAccountUrl' => '',
            'lostAccountUrl' => '',
            'manualUrl' => '',
            'faqUrl' => '',
            'premiumFeaturesUrl' => '',
            'limesurveyUrl' => '',
            'hintsUrl' => '',
            'cipSoftUrl' => '',
            'twitchTibiaUrl' => '',
            'youTubeTibiaUrl' => '',
            'crashReportUrl' => '',
            'fpsHistoryRecipient' => '',
            'tutorialProgressWebService' => '',
            'tournamentDetailsUrl' => '',
        ];
    }

    public function progress($inputFilePath, $outfitFilePath)
    {
        if (strlen($this->getRsa()) !== 256) {
            return 'RSA key length must be 256 characters';
        }
        foreach ($this->services as $key => $value) {
            if ($value !== '' && substr($value, 0, 7) !== 'http://' && substr($value, 0, 8) !== 'https://') {
                return '"' . $key . '" has invalid value. Every URL must start with "http://" or "https://".';
            }
        }
        $file = file_get_contents($inputFilePath);
        $newClientExe = '';
        $matches = [];
        $lines = explode("\r\n", $file);
        foreach ($lines as $line) {
            foreach ($this->battlEyeDisableBytes as $fromBytes => $toBytes) {
                if (strpos($line, $fromBytes) !== false) {
                    $line = str_replace($fromBytes, $toBytes, $line);
                }
            }
            foreach ($this->services as $key => $value) {
                if ($value !== '') {
                    if (strpos($line, $key) === 0) {
                        $oldValue = substr($line, strlen($key) + 1);
                        $fillBytes = strlen($oldValue) - strlen($value);
                        if ($fillBytes < 0) {
                            return 'Defined "' . $key . '" value "' . $value . '" is longer than original value "' . $oldValue . '". Cannot replace it.';
                        }
                        $line = $key . '=' . $value . str_repeat("\x20", $fillBytes);
                    }
                }
            }
            if (preg_match('/[0-9A-F]{256}/', $line, $matches)) {
                foreach ($matches as $possibleRSA) {
                    $possibleRsaWithNulls = "\x00" . $possibleRSA . "\x00";
                    if (strpos($line, $possibleRsaWithNulls) !== false) {
                        $newRsaWithNulls = "\x00" . $this->rsa . "\x00";
                        $line = str_replace($possibleRsaWithNulls, $newRsaWithNulls, $line);
                    }
                }
            }
            $newClientExe .= $line . "\r\n";
        }
        $zip = new ZipArchive;
        $res = $zip->open($outfitFilePath, ZipArchive::CREATE);
        if ($res === true) {
            $zip->addFromString('client.exe', $newClientExe);
            $zip->close();
        } else {
            return 'Failed to save in ZIP.';
        }
        return true;
    }

    public function getRsa()
    {
        return $this->rsa;
    }

    public function setRsa($rsa)
    {
        $this->rsa = $rsa;
    }

    public function getServices()
    {
        return $this->services;
    }

    public function getService($key)
    {
        return $this->services[$key];
    }

    public function setService($key, $value)
    {
        if (isset($this->services[$key])) {
            $this->services[$key] = $value;
        }
    }
}