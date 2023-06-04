<?php
/**
 * Outfit Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Model\Functions\Outfit as FunctionsOutfit;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;

class Outfit
{
    public static function generateCache($request)
    {
        $outfitImagesPath = OUTFITS_FOLDER;
        $dirIterator = new RecursiveDirectoryIterator($outfitImagesPath, FilesystemIterator::UNIX_PATHS);
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

        $outfits = [];
        $i = 0;
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filePath = trim($file->getPath(), '.');
                $filePath = trim($filePath, '/');
                $outfitIdData = explode('/', $filePath);
                $outfitId = $outfitIdData[4];
                $outfits[$outfitId]['files'][] = $filePath . '/' . $file->getFilename();
                if(isset($outfits[$outfitId]['framesNumber'])) {
                    $outfits[$outfitId]['framesNumber'] = max($outfits[$outfitId]['framesNumber'], (int) substr($file->getFilename(), 0, 1));
                } else {
                    $outfits[$outfitId]['framesNumber'] = (int) substr($file->getFilename(), 0, 1);
                }
            }
        }

        // CODE TO CHECK WHAT VALUES OF 'framesNumber' ARE POSSIBLE FOR YOUR OUTFITS
        $frameNumbers = [0,0,0,0,0,0,0,0,0,0];
        foreach($outfits as $outfitId => $outfit) {
            if (!file_put_contents($outfitImagesPath . '/' . $outfitId . '/outfit.data.txt', serialize($outfit))) {
                exit('PHP cannot write to: "' . $outfitImagesPath . '/' . $outfitId . '/outfit.data.txt", check directory access rights');
            }
            $frameNumbers[$outfit['framesNumber']]++;
        }
        echo 'FILE SYSTEM CACHE GENERATED<br />Animation frames count in loaded outfits:';
    }

    public static function viewAnimatedOutfit($request)
    {
        $queryParams = $request->getQueryParams();
        $walkSpeeds = [
            1 => 50,
            2 => 35,
            3 => 30,
            4 => 15,
            5 => 15,
            6 => 15,
            7 => 15,
            8 => 10,
            9 => 8
        ];

        $id = 0;
        if (isset($queryParams['id'])) {
            $id = (int) $queryParams['id'];
        }
        if (!FunctionsOutfit\Animated::loadData($id, false)) {
            exit('Outfit does not exist or file cache is not generated.');
        }

        $mount = 0;
        if (isset($queryParams['mount'])) {
            $mount = (int) $queryParams['mount'];
        }
        if ($mount > 0 && !FunctionsOutfit\Animated::loadData($mount, true)) {
            exit('Mount outfit does not exist or file cache is not generated.');
        }

        $head = 0;
        if (isset($queryParams['head'])) {
            $head = (int) $queryParams['head'];
        }
        $body = 0;
        if (isset($queryParams['body'])) {
            $body = (int) $queryParams['body'];
        }
        $legs = 0;
        if (isset($queryParams['legs'])) {
            $legs = (int) $queryParams['legs'];
        }
        $feet = 0;
        if (isset($queryParams['feet'])) {
            $feet = (int) $queryParams['feet'];
        }
        $addons = 0;
        if (isset($queryParams['addons'])) {
            $addons = (int) $queryParams['addons'];
        }
        $direction = 3;
        if (isset($queryParams['direction'])) {
            $direction = (int) $queryParams['direction'];
        }

        $frames = [];
        $durations = [];

        $moveAnimFrames = FunctionsOutfit\Animated::getOutfitFramesNumber();

        // rotate player, BIG IMAGES, 20-80 KB per outfit!
        //for($direction = 1; $direction <= 4; $direction++)
        for ($moveAnimFrame = 1; $moveAnimFrame <= $moveAnimFrames; ++$moveAnimFrame) {
            $frames[] = FunctionsOutfit\Animated::instance()->outfit(
                $id,
                $addons,
                $head,
                $body,
                $legs,
                $feet,
                $mount,
                $direction,
                $moveAnimFrame
            );
            $durations[] = $walkSpeeds[$moveAnimFrames];
        }
        $gc = new FunctionsOutfit\GifCreator();
        $gc->create($frames, $durations, 0);
        $gifBinary = $gc->getGif();
        header('Content-type: image/gif');
        echo $gifBinary;
    }
}
