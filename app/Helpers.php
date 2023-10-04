<?php
namespace App;

class Helpers
{
    /**
     * Чистим номер телефона от всего, кроме цифр
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
    }

    /**
     * Изменяет окончание слова в зависимости от количества позиций
     *
     * @param int $number количество
     * @param string $nominativeMessage название в именительном падеже (есть кто? что?) (1)
     * @param string $genitiveMessage название в родительном падеже (нет кого? чего?) (2-4)
     * @param string $accusativeMessage название в винительном падеже (вижу кого? что?) (5-9)
     * @return string отформатированное название
     */
    public static function declinateWord(int $number, string $nominativeMessage, string $genitiveMessage, string $accusativeMessage) : string
    {
        $exceptions = range(11, 20);
        if($number % 10 === 1 && !in_array($number % 100, $exceptions, true)) {
            $word = $nominativeMessage;
        } elseif($number % 10 > 1 && $number % 10 < 5 && !in_array($number % 100, $exceptions, true)) {
            $word = $genitiveMessage;
        } else {
            $word = $accusativeMessage;
        }

        return $word;
    }

}
