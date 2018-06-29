<?php

namespace modules\packager;
    /**
     * A dictionary class for Packager
     *
     * @package    Dictionary Packager
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2018 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim-modules-packager/blob/master/LICENSE.md  MIT License
     */
	class Dictionary {
        /**
         * @param $id is indonesian dictionary
         *
         */
        public static $id = [
            //Info
            'is_compatible' => 'kompatibel dengan reSlim',
            'is_not_compatible' => 'tidak kompatibel dengan reSlim',
            'dependency_ok' => 'telah diperiksa dan berhasil di verifikasi.',
            'dependency_fail' => 'Dependency yang dibutuhkan, masih ada yang belum terpasang!',
            'tips_readme' => 'Jika file readme tidak muncul, Anda harus mengganti nama filenya menjadi README.md atau periksa untuk memastikan file readme ada di dalam server.',
            //handler
            'PC101' => 'Instalasi modul berhasil!',
            'PC102' => 'Melepas instalasi modul berhasil!',
            'PC103' => 'Modul yang terinstal ditemukan!',
            'PC104' => 'Format struktur modul OK!',
            'PC201' => 'Instalasi modul gagal!',
            'PC202' => 'Melepas instalasi modul gagal!',
            'PC203' => 'Modul yang terinstal tidak ditemukan!',
            'PC204' => 'Format struktur modul salah!'
        ];

        /**
         * @param $en is english dictionary
         *
         */
        public static $en = [
            //Transaction process
            'is_compatible' => 'is compatible with reSlim',
            'is_not_compatible' => 'is not compatible with reSlim',
            'dependency_ok' => 'was checked and successfully verified.',
            'dependency_fail' => 'Required dependency, still not installed yet!',
            'tips_readme' => 'If file readme doesn\'t appear, You have rename to README.md or check to make sure file readme is exist on server.',
            //handler
            'PC101' => 'Install module successful!',
            'PC102' => 'Uninstall module successful!',
            'PC103' => 'Installed modules is found!',
            'PC104' => 'Module structure format is OK!',
            'PC201' => 'Install module failed!',
            'PC202' => 'Uninstall module failed!',
            'PC203' => 'Installed modules is not found!',
            'PC204' => 'Wrong module structure format!'
        ];

        /**
         * @param $key : input the key of dictionary
         * @return string dictionary language
         */
        public static function write($key,$lang=''){
            switch($lang){
                case 'id':
                    return self::$id[$key];
                break;
                default:
                    return self::$en[$key];
            }
        }
    }