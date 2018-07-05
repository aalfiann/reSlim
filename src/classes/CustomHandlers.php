<?php
/**
 * This class is a part of reSlim project
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
    namespace classes;
    /**
     * CustomHandlers is to show Your custom own message handler
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2016 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
     include_once( "../config.php" ) ;
    class CustomHandlers {

        /**
         * @param $responseMessages is data array to handler the status message of response
         *
         */
        public static $responseMessages = [
            //Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            //Successful 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
            //Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            //Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            421 => 'Misdirected Request',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            444 => 'Connection Closed Without Response',
            451 => 'Unavailable For Legal Reasons',
            499 => 'Client Closed Request',
            //Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
            599 => 'Network Connect Timeout Error'
        ];

        /**
         * @param $reSlimMessages is data array to handler the status message in reSlim (english)
         *
         */
        public static $reSlimMessagesEN = [
            // User process success 1xx    
            'RS101' => 'Request process is successfully created.',
            'RS102' => 'Request process is successfully read.',
            'RS103' => 'Request process is successfully updated.',
            'RS104' => 'Request process is successfully deleted.',
            'RS105' => 'Mail hasbeen sent.',
            'RS106' => 'Processing log is successfully completed.',
            // User process error 2xx
            'RS201' => 'Request process is failed to create.',
            'RS202' => 'Request process is failed to read.',
            'RS203' => 'Request process is failed to update.',
            'RS204' => 'Request process is failed to delete.',
            // User authority success 3xx
            'RS301' => 'Your token is actived.',
            'RS302' => 'Your token is match.',
            'RS303' => 'Your token is passed.',
            'RS304' => 'Your token is authorized.',
            'RS305' => 'Your token hasbeen revoked.',
            'RS306' => 'Your api key hasbeen revoked.',
            // User authority error 4xx
            'RS401' => 'Your token is expired or Unauthorized, so You have to generate the new authorized token!',
            'RS402' => 'Your token is not match.',
            'RS403' => 'Your token is wrong.',
            'RS404' => 'Your token is Unauthorized.',
            'RS405' => 'You don\'t have any token.',
            'RS406' => 'Your api key is expired or Unauthorized.',
            'RS407' => 'Your api key is not valid.',
            // User data success 5xx
            'RS501' => 'Data records is found.',
            // User data error 6xx
            'RS601' => 'There is no any data records found.',
            'RS602' => 'Data records is exceed the limit.',
            'RS603' => 'Data can\'t be duplicated.',
            'RS604' => 'Data records is exceed the limit to render.',
            'RS605' => 'Max items per page : ',
            // Parameter success 7xx
            'RS701' => 'The parameter is valid.',
            'RS702' => 'The parameter is authorized.',
            // Parameter error 8xx
            'RS801' => 'The parameter is not valid.',
            'RS802' => 'The parameter is not authorized.',
            'RS803' => 'The parameter is deprecated.',
            'RS804' => 'The parameter is contains not allowed character.',
            'RS805' => 'Some parameter is required!',
            // Any error messages 9xx
            'RS901' => 'Failed to register user.',
            'RS902' => 'Username is not available.',
            'RS903' => 'Username or Password is not match! {case sensitive}',
            'RS904' => 'Failed to update user.',
            'RS905' => 'Failed to delete user.',
            'RS906' => 'Sorry, Your account is suspended.',
            'RS907' => 'Failed to change password.',
            'RS908' => 'Failed to upload, file type is not allowed.',
            'RS909' => 'Upload success, but failed to insert data into database.',
            'RS910' => 'Upload failed. Connection lost, please try again.',
            'RS911' => 'Upload failed, File is too large.',
            'RS912' => 'Upload canceled, The filename is already on server. Please use another filename.',
            'RS913' => 'Request process is successfully deleted only in database server but failed to delete the data file on server.',
            'RS914' => 'Email is not available.',
            'RS915' => 'Failed to verify pass key maybe it was expired.',
            'RS916' => 'Domain hasbeen registered.',
            'RS917' => 'Users can not make a review more than one.',
            // Any error messages for midleware
            'MD001' => 'This field is required. Blank, empty or whitespace value is not allowed!',
            'MD002' => 'The value is not valid domain or subdomain format.',
            'MD003' => 'The value is not valid url format.',
            'MD004' => 'The value is not valid date with yyyy-mm-dd format.',
            'MD005' => 'The value is not valid timestamp with yyyy-mm-dd hh:mm:ss format.',
            'MD006' => 'The value is not alphanumeric!',
            'MD007' => 'The value is not alphabet!',
            'MD008' => 'The value is not decimal!',
            'MD009' => 'The value should be numeric and contains leading zero value is not allowed!',
            'MD010' => 'The value is not numeric!',
            'MD011' => 'The value is not numeric (double)!',
            'MD012' => 'The value is not username allowed format!',
            'MD013' => 'The value is not email address allowed format!',
            'MD014' => 'The value is not valid json format!',
            'MD015' => 'The value is not using valid format!',
            'MD101' => 'Chars length is should be between',
            'MD102' => 'Failed to determine between chars length!',
            'MD103' => 'Wrong format between occured! The format is "min-max".',
            'MD104' => 'The body of request json is empty! Maybe you got the wrong way to send your json request into our server.',
            'MD105' => 'Corrupted, malformed or empty request json!',
            // Any Custom Notice
            'CN001' => 'Keep secret and better to send this pass key via email. This pass key will expired 3 days from now.'
        ];

        /**
         * @param $reSlimMessages is data array to handler the status message in reSlim (Indonesian)
         *
         */
        public static $reSlimMessagesID = [
            // User process success 1xx    
            'RS101' => 'Permintaan proses create berhasil.',
            'RS102' => 'Permintaan proses read berhasil.',
            'RS103' => 'Permintaan proses update berhasil.',
            'RS104' => 'Permintaan proses delete berhasil.',
            'RS105' => 'Email telah dikirim.',
            'RS106' => 'Memproses log berhasil.',
            // User process error 2xx
            'RS201' => 'Permintaan proses create gagal.',
            'RS202' => 'Permintaan proses read gagal.',
            'RS203' => 'Permintaan proses update gagal.',
            'RS204' => 'Permintaan proses delete gagal.',
            // User authority success 3xx
            'RS301' => 'Token Anda telah aktif.',
            'RS302' => 'Token Anda cocok.',
            'RS303' => 'Token Anda sesuai.',
            'RS304' => 'Token Anda memiliki wewenang.',
            'RS305' => 'Token Anda telah dicabut.',
            'RS306' => 'API Key Anda telah dicabut.',
            // User authority error 4xx
            'RS401' => 'Token Anda kadaluarsa atau tidak memiliki wewenang, Jadi Anda harus membuat token baru yang memiliki wewenang!',
            'RS402' => 'Token Anda tidak cocok.',
            'RS403' => 'Token Anda salah.',
            'RS404' => 'Token Anda tidak memiliki wewenang.',
            'RS405' => 'Anda tidak memiliki token apapun.',
            'RS406' => 'API Key Anda telah kadaluarsa atau tidak memiliki wewenang.',
            'RS407' => 'API Key Anda tidak valid.',
            // User data success 5xx
            'RS501' => 'Data ditemukan.',
            // User data error 6xx
            'RS601' => 'Tidak ada satupun data ditemukan.',
            'RS602' => 'Data telah melebihi batas penyimpanan.',
            'RS603' => 'Data tidak dapat di duplikasi.',
            'RS604' => 'Data telah melibihi batas untuk ditampilkan.',
            'RS605' => 'Maksimum item data per halaman : ',
            // Parameter success 7xx
            'RS701' => 'Parameter valid.',
            'RS702' => 'Parameter memiliki wewenang.',
            // Parameter error 8xx
            'RS801' => 'Parameter tidak valid.',
            'RS802' => 'Parameter tidak memiliki wewenang.',
            'RS803' => 'Parameter telah tidak berlaku lagi.',
            'RS804' => 'Parameter terdapat karakter yang dilarang.',
            'RS805' => 'Ada beberapa parameter yang dibutuhkan!',
            // Any error messages 9xx
            'RS901' => 'Gagal registrasi pengguna.',
            'RS902' => 'Nama pengguna tidak tersedia.',
            'RS903' => 'Nama pengguna atau password tidak cocok! {case sensitive}',
            'RS904' => 'Gagal memperbaharui pengguna.',
            'RS905' => 'Gagal menghapus pengguna.',
            'RS906' => 'Maaf, Akun Anda telah dibekukan.',
            'RS907' => 'Gagal merubah kata sandi.',
            'RS908' => 'Gagal mengunggah, jenis berkas tidak di ijinkan.',
            'RS909' => 'Unggah berhasil, tapi gagal menyimpan data ke dalam database.',
            'RS910' => 'Unggah gagal. Kehilangan koneksi, silahkan coba lagi.',
            'RS911' => 'Unggah gagal, Ukuran berkas terlalu besar.',
            'RS912' => 'Unggah dibatalkan, Nama berkas sudah ada di dalam server. Silahkan gunakan nama berkas yang lain.',
            'RS913' => 'Permintaan proses delete telah berhasil hanya di database server tapi gagal menghapus berkas yang ada di server.',
            'RS914' => 'Email tidak tersedia.',
            'RS915' => 'Gagal verifikasi kata kunci, mungkin sudah kadaluarsa.',
            'RS916' => 'Domain telah terdaftar.',
            'RS917' => 'Pengguna tidak dapat membuat ulasan lebih dari satu.',
            // Any error messages for midleware
            'MD001' => 'Kolom ini dibutuhkan. Kosong atau hanya spasi karakter, tidak diizinkan!',
            'MD002' => 'Isi tidak sesuai format domain atau subdomain.',
            'MD003' => 'Isi tidak sesuai format url.',
            'MD004' => 'Isi tidak sesuai date dengan format yyyy-mm-dd.',
            'MD005' => 'Isi tidak sesuai timestamp dengan format yyyy-mm-dd hh:mm:ss.',
            'MD006' => 'Isi bukan format alphanumeric!',
            'MD007' => 'Isi bukan format alphabet!',
            'MD008' => 'Isi bukan format decimal!',
            'MD009' => 'Isi seharusnya angka dan jika ada angka nol didepan itu tidak diizinkan!',
            'MD010' => 'Isi bukan format numeric!',
            'MD011' => 'Isi bukan format numeric (double)!',
            'MD012' => 'Isi bukan format username!',
            'MD013' => 'Isi bukan format alamat email!',
            'MD014' => 'Isi tidak sesuai format json!',
            'MD015' => 'Isi tidak menggunakan valid format!',
            'MD101' => 'Panjang karakter seharusnya',
            'MD102' => 'Gagal mengetahui jarak panjang karakter!',
            'MD103' => 'Format jarak panjang karakter salah! Formatnya adalah "min-max".',
            'MD104' => 'The body of request json is empty! Maybe you got the wrong way to send your json request into our server.',
            'MD105' => 'Korup, format salah atau request json kosong!',
            // Any Custom Notice
            'CN001' => 'Simpan dan lebih baik kirim pass key ini via email. Pass key ini akan kadaluarsa dalam 3 hari.'
        ];

        /**
         * @param code : input the code of status message in reSlim
         * @param lang : is to override the default language. Default is null, means will read from main configuration reSlim
         * @return string status message
         */
        public static function getreSlimMessage($code,$lang=''){
            global $config;
            $datalang = (empty($lang)?strtolower($config['language']):$lang);
            switch($datalang){
                case 'id':
                    return self::$reSlimMessagesID[$code];
                default:
                    return self::$reSlimMessagesEN[$code];
            }
        }

        /**
         * @param $code : input the code of status message in response
         * @return string status message
         */
        public static function getResponseMessage($code){
            return self::$responseMessages[$code];
        }
    }