<?php
namespace App;

class Helpers
{
    /**
     * Чистим номер телефона от всего, кроме цифр
     * TODO: Перенести в App\Helpers
     *
     * @param string $phone Номер телефона в любом формате
     * @param bool $savePlus Сохранять ли плюс в номере
     *
     * @return string
     */
    public static function cleanPhoneString(string $phone, bool $savePlus = false) : string
    {
        $plus = false;
        if($savePlus) {
            $plus = '+';
        }

        $regex = '/[^0-9'.$plus.'.]+/';

        return preg_replace($regex, '', $phone);
    }

    /**
     * Сокращение строки до указанного количества символов
     *
     * @param string $value Строка
     * @param int $length Максимальная длина строки
     *
     * @return string
     */
    public static function truncate(string $value, int $length = 255, $end = '') : string
    {
        return \Illuminate\Support\Str::limit($value, $length, $end);
//        $value = trim($value);
//        if(mb_strlen($value) <= $length) {
//            return $value;
//        }

//        return mb_substr($value, 0, $length) . $end;
    }
}
