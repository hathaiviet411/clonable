<?php


namespace Helper;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Common
{
    /**
     * @param UploadedFile $file
     * @param $path
     * @param null $shopId
     * @return mixed
     */
//    public static function uploadFile(UploadedFile $file, $path = '', $shopId = null)
//    {
//        $shopId = $shopId ?? data_get(Auth::user(), 'shop.id');
//        $fileName = now()->format('d-m-Y--H-i-s') . "_" . $file->getClientOriginalName();
//        return $file->storeAs("shops/{$shopId}/{$path}", $fileName);
//    }

    public static function uploadFile(UploadedFile $file, $path = '', $userId = null)
    {
        $userId = $userId ?? Auth::id();
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($path, $fileName);
    }

    public static function randNotInArr($min, $max, $inArray)
    {
        do {
            $rand = rand($min, $max);
        } while (in_array($rand, $inArray));
        return $rand;
    }

    /**
     * Change input encoding for by file csv, xlsx
     *
     *
     * @param $path : string path to file check encoding
     */
    public static function changeInputEncodingByFile($pathFile = null, $encChange = null)
    {
        $enc = $encChange ? $encChange : 'UTF-8';
        if ($pathFile) {
            $enc = self::getEncodingByFile($pathFile);
        }
        \Config::set('excel.imports.csv.input_encoding', $enc);
    }

    public static function getEncodingByFile($pathFile)
    {
        return mb_detect_encoding(file_get_contents($pathFile), mb_list_encodings(), true);
    }

    public static function onWindows()
    {
        return PHP_OS === 'WINNT' || Str::contains(php_uname(), 'Microsoft');
    }
}
