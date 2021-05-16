<?php

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
|
| Useful simple helpers.
|
| + Verify variables
|   - function  IsSafe
|
| + Time
|   - function  MsTime
|   - function  MsTimestamp
|
| + Format of number and text
|   - const     EmailFormat
|   - function  CheckYmdHis
|   - function  SecondsToEnglishString
|   - function  ChineseWeekDate
|
| + Conversions between Excel column name and number (Base 26 in another way)
|   - function  ExcelColumnToNumber
|   - function  NumberToExcelColumn
|
| + Handling of text, character and string
|   - function  Blank
|   - function  TextCompress
|   - function  RemoveTrailingZeros
|
| + Regular Expression
|   - function  CombineRegex
|
| + Base 62
|   - const     Base62Dict
|   - function  StrBase62
|   - function  Base10To62
|   - function  Base62To10
|
| + GUID and UUID
|   - function  Guid
|   - function  Uuid
|
| + TGUID
|   - function  Tguid16
|   - function  Base62Guid
|   - function  Base62Tguid
|   - function  Tguid
|   - function  TguidToTime
|   - function  TimeToBase62Guid
|
| + Variable export
|   - function  VarExportFormat
|
| + JSON
|   - function  JsonUnescaped
|   - function  JsonPrettyPrinted
|   - function  JsonEmptyObject
|
| + HTML
|   - function  TitleOnlyPage
|
| + Cache Buster
|   - function  AssetCachebuster
|   - const     CachebusterLength
|
*/

if (!function_exists('IsSafe'))
{
    /**
     * Check is the input value exist, not `null`, `false` or empty string.
     * 
     * 0、character 0（`'0'`）, empty array or object will be `true`.
     *
     * Warning while `$value` is undefined (not able to replace `isset()` fully), can be suppressed using character `@`.
     *
     * @param  integer|string|array|object  $value  Input value
     * @return boolean
     */
    function IsSafe($value = null)
    {
        switch (true)
        {
            case ($value !== null && $value != null):
            case ($value !== null && $value === 0):
            case ($value !== null && $value === '0'):
            case ($value !== null && $value == null && is_array($value)):
                return true;

            default:
                return false;
        }
    }
}

if (!function_exists('MsTime'))
{
    /**
     * Get a microsecond-level time string. If a timestamp is given, the time string will correspond to it.
     *
     * @param  string|integer|double|null  $Timestamp  Timestamp
     * @return string
     */
    function MsTime($Timestamp = null)
    {
        if ($Timestamp !== null)
        {
            $Timestamp = (string) $Timestamp;
            $date = explode('.', $Timestamp);
            $s = (int) $date[0];
            $ms = isset($date[1]) ? rtrim($date[1], '0') : '0';
            $time = ($ms != 0) ? date('Y-m-d H:i:s', $s) . '.' . $ms : date('Y-m-d H:i:s', $s);
            return $time;
        }
        else
        {
            $datetime = new \DateTime();
            $time = $datetime->format('Y-m-d H:i:s.u');
            return $time;
        }
    }
}

if (!function_exists('MsTimestamp'))
{
    /**
     * Get the microsecond-level timestamp. If a time string is given, the timestamp will correspond to it.
     *
     * @param  string|null  $TimeString  Time string
     * @return double
     */
    function MsTimestamp($TimeString = null)
    {
        if ($TimeString !== null)
        {
            $time = explode('+', $TimeString);
            $time = explode('.', $time[0]);
            $s = strtotime($time[0]);
            $ms = isset($time[1]) ? rtrim($time[1], '0') : '0';
            $mtime = ($ms != 0) ? ($s . '.' . $ms) : $s;
        }
        else
        {
            $time = explode(' ', microtime());
            $s = $time[1];
            $ms = rtrim($time[0], '0');
            $ms = preg_replace('/^0/', '', $ms);
            $mtime = $s . $ms;
        }
        return (float) $mtime;
    }
}

if (!defined('EmailFormat'))
{
    /**
     * Regular expression of a email address.
     *
     * @var string
     */
    define('EmailFormat', '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD');
}

if (!function_exists('CheckYmdHis'))
{
    /**
     * Check is the time string in the legal `Y-m-d H:i:s` format.
     *
     * @param  string  $TimeString  Time string
     * @return boolean
     */
    function CheckYmdHis($TimeString)
    {
        $TimeFormat = '/^\d{1,}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';

        if (!preg_match($TimeFormat, $TimeString))
        {
            return false;
        }
        else
        {
            $FullArray = explode(' ', $TimeString);
            $Date = $FullArray[0];
            $Time = $FullArray[1];

            $DateArray = explode('-', $Date);
            $Year  = (int) $DateArray[0];
            $Month = (int) $DateArray[1];
            $Day   = (int) $DateArray[2];

            $TimeArray = explode(':', $Time);
            $Hour   = (int) $TimeArray[0];
            $Minute = (int) $TimeArray[1];
            $Second = (int) $TimeArray[2];

            if (($Month < 1 || $Month > 12) ||
                ($Day < 1) ||
                (in_array($Month, [1, 3, 5, 7, 8, 10, 12]) && $Day > 31) ||
                (in_array($Month, [4, 6, 9, 11]) && $Day > 30) ||
                ($Month === 2 && $Day > 29) ||
                ($Hour < 0 || $Hour > 23) ||
                ($Minute < 0 || $Minute > 59) ||
                ($Second < 0 || $Second > 59))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }
}

if (!function_exists('SecondsToEnglishString'))
{
    /**
     * Convert a number of seconds to other time units
     *
     * @param  integer  $Seconds  Number of seconds
     * @return string|null
     */
    function SecondsToEnglishString($Seconds)
    {
        $Second = $Seconds % 60;
        $Minute = ($Seconds - $Second) / 60 % 60;
        $Hour = (($Seconds - $Second) / 60 / 60) % 24;
        $Day = (($Seconds - $Second) / 60 / 60 / 24) % 7;
        $Week = floor(($Seconds - $Second) / 60 / 60 / 24 / 7);

        $Array = [];

        # Week
        if ($Week > 1)
        {
            $Array[] = $Week . ' weeks';
        }
        else if ($Week > 0)
        {
            $Array[] = $Week . ' week';
        }

        # Day
        if ($Day > 1)
        {
            $Array[] = $Day . ' days';
        }
        else if ($Day > 0)
        {
            $Array[] = $Day . ' day';
        }

        # Hour
        if ($Hour > 1)
        {
            $Array[] = $Hour . ' hours';
        }
        else if ($Hour > 0)
        {
            $Array[] = $Hour . ' hour';
        }

        # Minute
        if ($Minute > 1)
        {
            $Array[] = $Minute . ' minutes';
        }
        else if ($Minute > 0)
        {
            $Array[] = $Minute . ' minute';
        }

        # Second
        if ($Second > 1)
        {
            $Array[] = $Second . ' seconds';
        }
        else if ($Second > 0)
        {
            $Array[] = $Second . ' second';
        }

        $String = implode(', ', $Array);
        $String = preg_replace('/, ([^,]+)$/', ' and $1', $String);

        return $String;
    }
}

if (!function_exists('ChineseWeekDate'))
{
    /**
     * Convert given date as Chinese in the "Y 年 n 月 j 日" format with name of day of the week
     *
     * @param  string   $Date    Time string can be parse by `strtotime()`. Default value is `null` and the current date will be substituted in 
     * @param  boolean  $Gap     Whether to place blanks between numbers and Chinese characters. Default value is `true`
     * @param  string   $Prefix  Prefix of name of day of the week. Default value is `x` (星期), other options: `z` (週)
     * @return string[]
     */
    function ChineseWeekDate($Date = null, $Gap = true, $Prefix = 'x')
    {
        if ($Date === null)
        {
            $Time = time();
        }
        else
        {
            $Time = strtotime($Date);
        }

        $Day = $Gap ? date('Y 年 n 月 j 日', $Time) : date('Y年n月j日', $Time);

        $WeekDay = date('w', $Time);
        switch ($WeekDay)
        {
            case 0:
                $ChineseWeekDay = '日';
                break;

            case 1:
                $ChineseWeekDay = '一';
                break;

            case 2:
                $ChineseWeekDay = '二';
                break;

            case 3:
                $ChineseWeekDay = '三';
                break;

            case 4:
                $ChineseWeekDay = '四';
                break;

            case 5:
                $ChineseWeekDay = '五';
                break;

            case 6:
                $ChineseWeekDay = '六';
                break;
        }

        switch (strtolower($Prefix))
        {
            case 'z':
                $ChineseWeekDay = '週' . $ChineseWeekDay;
                break;

            case 'x':
            default:
                $ChineseWeekDay = '星期' . $ChineseWeekDay;
                break;
        }

        return [ $Day, $ChineseWeekDay ];
    }
}

if (!function_exists('ExcelColumnToNumber'))
{
    /**
     * Convert column name in **Excel A1 Reference Style** to number (Maximum in Office 2019 is **XFD** = **16384**).
     *
     * @param  string  $Column  Column name
     * @return integer|boolean
     */
    function ExcelColumnToNumber($Column)
    {
        $ColumnChar = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $Number = 0;

        $Column = strtoupper($Column);

        for ($i = 0; $i < strlen($Column); $i++)
        {
            if (strpos($ColumnChar, $Column[$i]) !== false)
            {
                $Digit = strlen($Column) - $i - 1;
                $Value = (strpos($ColumnChar, $Column[$i]) + 1) * pow(26, $Digit);
                $Number += $Value;
            }
            else
            {
                return false;
            }
        }

        return ($Number > 0) ? $Number : false;
    }
}

if (!function_exists('NumberToExcelColumn'))
{
    /**
     * Convert number to column name in **Excel A1 Reference Style** (Maximum in Office 2019 is **XFD** = **16384**).
     *
     * @param  integer  $Number  Column number
     * @return string
     */
    function NumberToExcelColumn($Number)
    {
        $ColumnChar = 'ZABCDEFGHIJKLMNOPQRSTUVWXY';     // Dict of the alphabet
        $Column = '';
        $LowDigitValue = '';    // Remember alphabet value of the previous digit

        $Digit = 0;             // Order of the digit, start from the right most

        do
        {
            # Handling of the 1st digit
            if ($Digit === 0)
            {
                $Remainder = $Number % 26;                // Get the remainder of dividing by 26
                $Quotient = (int) floor($Number / 26);    // Get the quotient of dividing by 26
                $Value = $ColumnChar[$Remainder];         // Get alphabet value of this digit by Remainder
                $Column = $Value;                         // Put the alphabet value into string Column

                $LowDigitValue = $Value;                  // Save the alphabet value as the alphabet value of previous digit
                $Number = $Quotient;                      // Replace Number with current Quotient

                $Digit++;                                 // Add 1 to the order of the digit
            }
            # Handling of 2nd and more digit
            else
            {
                if ($LowDigitValue === 'Z')
                {
                    $Number--;                            // Subtract 1 from Number while the alphabet value previous digit is Z
                }
                $Remainder = $Number % 26;                // Get the remainder of dividing by 26
                $Quotient = (int) floor($Number / 26);    // Get the quotient of dividing by 26
                $Value = $ColumnChar[$Remainder];         // Get alphabet value of this digit by Remainder

                if ($Quotient === 0 && $LowDigitValue === 'Z' && $Value === 'Z')
                {
                    # Add no alphabet value to Column while Quotient is 0 (highest digit) and the alphabet value
                    # of the previous digit is Z (order of the digit can not be added anymore)
                }
                else
                {
                    $Column = $Value . $Column;           // Add the alphabet value to Column, otherwise
                }

                $LowDigitValue = $Value;                  // Save the alphabet value as the alphabet value of previous digit
                $Number = $Quotient;                      // Replace Number with current Quotient

                $Digit++;                                 // Add 1 to the order of the digit
            }
        }
        while ($Number > 0);

        return $Column;
    }
}

if (!function_exists('Blank'))
{
    /**
     * Generate specified number of blanks
     *
     * @param  integer  $Number  Number of blanks
     * @return string
     */
    function Blank($Number = 1)
    {
        $Blank = '';
        for ($i = 0; $i < $Number; $i++)
        {
            $Blank .= ' ';
        }
        return $Blank;
    }
}

if (!function_exists('TextCompress'))
{
    /**
     * Compress the text: remove breaks and redundant blanks from the string.
     *
     * @param  string  $text  String to be compressed
     * @return string
     */
    function TextCompress($text = '')
    {
        return preg_replace([
            '/\r?\n */',
            '/\( +/', '/ +\)/',
            '/\[ +/', '/ +\]/',
            '/\{ +/', '/ +\}/'
        ], [
            ' ',
            '(', ')',
            '[', ']',
            '{', '}'
        ], $text);
    }
}

if (!function_exists('RemoveTrailingZeros'))
{
    /**
     * Remove trailing zeros behind decimal point from a formatted number-string.
     *
     * The number-string should has been handled by `number_format()` and gotten formatted with digit-grouping commas.
     *
     * @param  string  $strnum  Formatted number-string
     * @return string
     */
    function RemoveTrailingZeros($strnum)
    {
        return preg_replace(
            [
                '/\.0+$/',
                '/(\.\d*[^0])0+$/'
            ],
            [
                '',
                '$1'
            ],
            $strnum
        );
    }
}

if (!function_exists('CombineRegex'))
{
    /**
     * Input an array of regular expression strings, and return a combined single Regex string.
     *
     * @param  string[]  $segments  Array of regular expression strings
     * @return string
     */
    function CombineRegex($segments)
    {
        $combo = [];

        foreach ($segments as $reg)
        {
            $combo[] = preg_replace(['/^\//', '/\/$/'], '', $reg);
        }

        $combo = '/' . implode('|', $combo) . '/';

        return $combo;
    }
}

if (!defined('Base62Dict'))
{
    /**
     * Order of base 62 numbers (same as ASCII).
     *
     * @var string
     */
    define('Base62Dict', '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
}

if (!function_exists('StrBase62'))
{
    /**
     * Get random base 62 string.
     *
     * Original name: `str_base62`
     *
     * @param  integer  $length  Length of the string
     * @return string
     */
    function StrBase62($length = 8)
    {
        $str = '';
        for ($i = 0; $i < $length; $i++)
        {
            $rand = mt_rand(0, 61);
            $str .= Base62Dict[$rand];
        }
        return $str;
    }
}

if (!function_exists('Base10To62'))
{
    /**
     * Convert a decimal (base 10) number to base 62.
     *
     * @param  integer  $num  Decimal (base 10) number
     * @return string
     */
    function Base10To62($num)
    {
        $to = 62;
        $ret = '';
        do
        {
            $ret = Base62Dict[bcmod($num, $to)] . $ret;
            $num = bcdiv($num, $to);
        }
        while ($num > 0);
        return $ret;
    }
}

if (!function_exists('Base62To10'))
{
    /**
     * Convert a base 62 number to decimal (base 10).
     *
     * @param  integer  $num  Base 62 number
     * @return string
     */
    function Base62To10($num)
    {
        $from = 62;
        $num = strval($num);
        $len = strlen($num);
        $dec = 0;
        for ($i = 0; $i < $len; $i++)
        {
            $pos = strpos(Base62Dict, $num[$i]);
            $dec = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $dec);
        }
        return $dec;
    }
}

if (!function_exists('Guid'))
{
    /**
     * Get a GUID
     *
     * Original author: Sujip Thapa (https://github.com/sudiptpa/guid)
     *
     * @param  boolean  $trim  Whether to remove opening and closing braces
     * @return string
     */
    function Guid($trim = true)
    {
        # Windows
        if (function_exists('com_create_guid') === true) {
            if ($trim === true)
            {
                return trim(com_create_guid(), '{}');
            }
            else
            {
                return com_create_guid();
            }
        }

        # OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true)
        {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        # Fallback (PHP 4.2+)
        mt_srand((double) microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                                  // "-"
        $lbrace = $trim ? "" : chr(123);                    // "{"
        $rbrace = $trim ? "" : chr(125);                    // "}"
        $guidv4 = $lbrace .
        substr($charid, 0, 8) . $hyphen .
        substr($charid, 8, 4) . $hyphen .
        substr($charid, 12, 4) . $hyphen .
        substr($charid, 16, 4) . $hyphen .
        substr($charid, 20, 12) . $rbrace;

        return $guidv4;
    }
}

if (!function_exists('Uuid'))
{
    /**
     * Prepend an ID generated by `uniqid()` (without `more_entropy`) to a GUID,
     * remove the shifted GUID characters to remain 32 digits and the dash.
     *
     * @return string
     */
    function Uuid()
    {
        $unid = uniqid() . str_replace('-', '', Guid());
        $uuid = substr($unid, 0, 8) . '-' . substr($unid, 8, 4) . '-' . substr($unid, 12, 4) . '-' . substr($unid, 16, 4) . '-' . substr($unid, 20, 12);
        return $uuid;
    }
}

if (!function_exists('Tguid16'))
{
    /**
     * Generate a hexadecimal (base 16) GUID and prepend a `uniqid()`-generated ID to it, combined by dashes.
     * Whole string will be 14 + 8 + 32 = 54-digit, and 60-digit if include the dashes.
     *
     * @return string
     */
    function Tguid16()
    {
        $entropicGuid = str_replace('.', '-', uniqid('', true)) . '-' . Guid();
        return $entropicGuid;
    }
}

if (!function_exists('Base62Guid'))
{
    /**
     * Convert a hexadecimal (base 16) GUID to base 62; it should be a 24-digit string or 28-digit string with dashes.
     *
     * @param  boolean  $dash  Whether to include dashes
     * @return string
     */
    function Base62Guid($dash = false)
    {
        # Separator, none by default ($dash = false)
        $separator = $dash ? '-' : '';

        # Generate the GUID
        $guid = Guid();

        # Divide the GUID into an array by the dashes
        $guidHex = explode('-', $guid);

        # Separately convert the 5 parts of the GUID to decimal (base 10) and then
        # convenrt them to base 62 and make them up to a manner of 6-3-3-3-9
        foreach ($guidHex as $key => $idhex)
        {
            # Each of the 5 parts of the GUID are 6, 3, 3, 3, 9-digits after being converted to base 62
            switch ($key)
            {
                case 0:
                    $pad = 6;
                    break;

                case 1:
                case 2:
                case 3:
                    $pad = 3;
                    break;

                case 4:
                    $pad = 9;
                    break;

                default:
                    break;
            }

            # Convert to decimal (base 10) from hexadecimal (base 16)
            $guidDec[$key] = hexdec($idhex);

            # Convert to base 62 from decimal (base 10)
            $guidBase62[$key] = Base10To62($guidDec[$key]);

            # Prepend numbers randomly while the digits are not enough
            $len = strlen($guidBase62[$key]);
            $ret = '';
            if (strlen($len) < $pad)
            {
                for ($i = 0; $i < ($pad - $len); $i++)
                {
                    $ret .= Base62Dict[mt_rand(0, 61)];
                }
            }
            $guidBase62[$key] = $ret . $guidBase62[$key];
        }

        # Combine the 5 parts (can be separated by dashes)
        $bguid = implode($separator, $guidBase62);

        # Return base-62 GUID
        return $bguid;
    }
}

if (!function_exists('Base62Tguid'))
{
    /**
     * Convert a hexadecimal (base 16) TGUID to base 62; it should be a 39-digit string or 45-digit string with dashes.
     *
     * @param  boolean  $dash  Whether to include dashes
     * @return string
     */
    function Base62Tguid($dash = false)
    {
        # Separator, none by default ($dash = false)
        $separator = $dash ? '-' : '';

        # Generate the TGUID
        $tguid = Tguid16();

        # Divide the TGUID into an array by the dashes
        $tguidHex = explode('-', $tguid);

        # Separately convert the 7 parts of the TGUID to decimal (base 10) and then
        # convenrt them to base 62 and make them up to a manner of 10-5-6-3-3-3-9
        foreach ($tguidHex as $key => $idhex)
        {
            # Each of the 7 parts of the GUID are 10, 5, 6, 3, 3, 3, 9-digits after being converted to base 62
            switch ($key)
            {
                case 0:
                    $pad = 10;
                    break;

                case 1:
                    $pad = 5;
                    break;

                case 2:
                    $pad = 6;
                    break;

                case 3:
                case 4:
                case 5:
                    $pad = 3;
                    break;

                case 6:
                    $pad = 9;
                    break;

                default:
                    break;
            }

            # Convert to decimal (base 10) from hexadecimal (base 16)
            # The 2nd part, entropy, need no conversion 'cause it have been base 10
            if ($key !== 1)
                $tguidDec[$key] = hexdec($idhex);
            else
                $tguidDec[$key] = $idhex;

            # Convert to base 62 from decimal (base 10)
            $tguidBase62[$key] = Base10To62($tguidDec[$key]);

            # Prepend 0 to the 1st part (time from `uniqid()`) and random
            # numbers to other part while the digits are not enough
            $len = strlen($tguidBase62[$key]);
            $ret = '';
            if (strlen($len) < $pad)
            {
                for ($i = 0; $i < ($pad - $len); $i++)
                {
                    switch ($key)
                    {
                        case 0:
                            $ret .= '0';
                            break;

                        default:
                            $ret .= Base62Dict[mt_rand(0, 61)];
                            break;
                    }
                }
            }
            $tguidBase62[$key] = $ret . $tguidBase62[$key];
        }

        # Combine the 7 parts (can be separated by dashes)
        $bguid = implode($separator, $tguidBase62);

        # Return base-62 TGUID
        return $bguid;
    }
}

if (!function_exists('Tguid'))
{
    /**
     * Append 3 digits randomly to TGUID to make it 42-digit; it should be a 42-digit string or 49-digit string with dashes.
     *
     * Answer to the Ultimate Question of Life, The Universe, and Everything!
     *
     * Original name: `Base62Tguid42` (`base62_tguid42`)
     *
     * @param  boolean  $dash  Whether to include dashes
     * @return string
     */
    function Tguid($dash = false)
    {
        # Separator, none by default ($dash = false)
        $separator = $dash ? '-' : '';

        # Number of total digits with dashes is 48 (the last dash connect to the original TGUID is not included)
        # If no dashes, 42
        $digit = $dash ? 48 : 42;

        # Protecting mechanism, lest the total digits are beyond 39
        $bguid = Base62Tguid($dash);
        $len = strlen($bguid);

        # Append enough base 62 numbers to the TGUID
        $ret = '';
        for ($i = 0; $i < ($digit - $len); $i++)
        {
            $ret .= Base62Dict[mt_rand(0, 61)];
        }
        $bguid .= $separator . $ret;

        # Return 42-digit TGUID
        return $bguid;
    }
}

if (!function_exists('TguidToTime'))
{
    /**
     * Get the time by a base 62 TGUID; latest examinable time is 3555-04-08 14:09:22.133048 (zzzzzzzzzz).
     *
     * Original name: `base62_guid_to_time`
     *
     * @param  integer  $tguid  TGUID
     * @return string
     */
    function TguidToTime($tguid = 0)
    {
        # Pick the left-most 10 digits
        $num = substr($tguid, 0, 10);

        # Convert it to decimal and convert back to hexadecimal
        $dec = Base62To10($num);
        $hex = dechex($dec);

        # Check for overflow to determine which digit of the hexadecimal value as the picking
        # breakpoint between the second-level and the microsecond-level timestamp
        # 5K1WLnfhB1 in base 62 = 72,057,594,037,927,935 in base 10
        #                       = ff ffff ffff ffff (16^14 - 1) in base 16
        if ($dec > Base62To10('5K1WLnfhB1'))
            $sub = -5;
        else
            $sub = -6;

        # Pick the left-most 8 or 9 digits of the hexadecimal value, convert it to base 10
        # second-level timestamp, and convert it back to `Y-m-d H:i:s` date
        $timestampHex = substr($hex, 0, $sub);
        $timestampDec = hexdec($timestampHex);
        $date = date('Y-m-d H:i:s', $timestampDec);

        # Pick the right-most 5 or 6 digits of the hexadecimal value, convert it to base 10
        # microsecond-level timestamp (without enough accuracy)
        $microtimeHex = substr($hex, $sub);
        $microtimeDec = substr(str_pad(round(hexdec($microtimeHex), 6), 6, '0', STR_PAD_LEFT), 0, 6);

        # Return `Y-m-d H:i:s` date and the microsecond
        return $date . '.' . $microtimeDec;
    }
}

if (!function_exists('TimeToBase62Guid'))
{
    /**
     * Convert given time string to a TGUID-10-digits-left-most-like string.
     *
     * Original name: `time_to_base62_guid`
     *
     * @param  string  $time  Time string
     * @return string
     */
    function TimeToBase62Guid($time = '')
    {
        # Define $time as `Y-m-d H:i:s.u` format while the time string is empty (by default)
        if ($time == '')
        {
            $time = date('Y-m-d H:i:s.u');
        }

        # Remove quotation marks in the inputted time string
        $time = str_replace('"', '', $time);
        $time = str_replace("'", '', $time);

        # Append default microsecond value while there is no microsecond part in the inputted time string
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $time))
        {
            $time .= '.000000';
        }
        else
        {
            # Return null while the inputted time string is not match neither `Y-m-d H:i:s` nor `Y-m-d H:i:s.u` format
            if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\.\d{1,6}$/', $time))
            {
                return null;
            }
        }

        # Divide the time string as second-and-longer and microsecond part
        $date = explode('.', $time)[0];
        $microtime = explode('.', $time)[1];

        # Convert the second-and-longer part to hexadecimal timestamp
        $timestampDec = strtotime($date);
        $timestampHex = dechex($timestampDec);

        # Append 0 to the microsecond part to make it 6-digit, converting it to hexadecimal, and prepend 0 to 1 to make it 6-digit again
        $microtimeDec = str_pad($microtime, 6, '0', STR_PAD_RIGHT);
        $microtimeHex = str_pad(dechex($microtimeDec), 6, '0', STR_PAD_LEFT);

        # Combine the two hexadecimal time string
        $prototHex = $timestampHex . $microtimeHex;

        # Convert the hexadecimal time string to base 62
        $base62 = gmp_strval(gmp_init($prototHex, 16), 62);

        # Prepend 0 to the base 62 time string to make it 10-digit
        $base62 = str_pad($base62, 10, '0', STR_PAD_LEFT);

        return $base62;
    }
}

if (!function_exists('VarExportFormat'))
{
    /**
     * Convert the variable to valid, formatted PHP code.
     *
     * @param  mixed  $var  Variable to be converted
     * @return string
     */
    function VarExportFormat($var)
    {
        return preg_replace(
            [
                "/\n((?:  )+) *([^ ])/",
                '/array \(/',
                '/=> ?\n */'
            ],
            [
                "\n$1$1$2",
                'array(',
                '=> '
            ],
            var_export($var, true)
        );
    }
}

if (!function_exists('JsonUnescaped'))
{
    /**
     * Get UTF-8 encoded, Unicode and slashes unescaped JSON.
     *
     * @param  array|object  $data  Data to be converted to JSON
     * @return string
     */
    function JsonUnescaped($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

if (!function_exists('JsonPrettyPrinted'))
{
    /**
     * Get UTF-8 encoded, Unicode and slashes unescaped, and pretty-printed JSON.
     *
     * @param  array|object  $data  Data to be converted to JSON
     * @return string
     */
    function JsonPrettyPrinted($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}

if (!function_exists('JsonEmptyObject'))
{
    /**
     * Remove opening and closing quotient marks from string '"{}"', to make it a standard JSON empty object.
     *
     * @param  string  $json  JSON string, should be '"{}"'
     * @return string
     */
    function JsonEmptyObject($json = '"{}"')
    {
        return preg_replace('/\"\{\}\"/', '{}', $json);
    }
}

if (!function_exists('TitleOnlyPage'))
{
    /**
     * Get HTML of a empty page with title only
     *
     * @param  string  $title  Title of the page
     * @return string
     */
    function TitleOnlyPage($title)
    {
        $html =
            "<!DOCTYPE html>\n" .
            "<html>\n" .
            "<head>\n" .
            "    <meta charset=\"utf-8\">\n" .
            "    <title>{$title}</title>\n" .
            "</head>\n" .
            "<body>\n" .
            "</body>\n" .
            "</html>";
        return $html;
    }
}

if (!function_exists('AssetCachebuster'))
{
    /**
     * Append random base 62 string with specified length to the given resource path
     *
     * @param  string   $path    Path of the resource
     * @param  integer  $length  Length of the base 62 string
     * @return string
     */
    function AssetCachebuster($path, $length = 0)
    {
        # Prepend a slash while the given path is not begin with it to ensure
        # that the path is located from the root of the project
        if (!preg_match('/^\//', $path))
        {
            $path = '/' . $path;
        }

        if ($length > 0)
        {
            return $path . '?' . StrBase62($length);
        }
        else
        {
            return $path;
        }
    }
}

if (!defined('CachebusterLength'))
{
    /**
     * Length of the random base 62 string, should be call with `AssetCachebuster()` together.
     *
     * @var integer
     */
    define('CachebusterLength', 22);
}
