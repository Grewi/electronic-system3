<?php 

// namespace system\core\valid\number;

// use system\core\valid\item;

// class valid_snils extends item
// {
//     protected string $textError = 'ИНН указан не корректно';
// 	public const NAME = 'snils';

//     public const ID_MIN = 1001999;
//     public const ID_MAX = 999999999;
//     public const FORMAT_CANONICAL = 'c';
//     public const FORMAT_SPACE     = 's';
//     public const FORMAT_HYPHEN    = 'h';

//     public const SEPARATOR_SPACE  = ' ';
//     public const SEPARATOR_HYPHEN = '-';	

// 	protected int $id;

//     public function control()
//     {
//         if ($this->original && !$this->isValidSnils($this->original)) {

//             $this->setError($this->textError);
//             $this->setControl(false);
//         }
//     }

//     public function getResult():mixed
//     {
//         return ($this->control ? (int) $this->original : null);
//     }

//     public static function isValidSnils(mixed $snils): bool
//     {
//         if ($snils instanceof self || is_string($snils) || is_int($snils)) {
//             return (bool) self::validate($snils);
//         }

//         return false;
//     }
	
//     public static function validate(self|string|int|null $snils, string|null $format = null): int|false
//     {
//         if ($snils === null) {
//             return false;
//         }

//         if ($snils instanceof self) {
//             return static::isIdValid($snils->getID()) ? $snils->getID() : false;
//         }

//         if ($format === null) {
//             $snils  = preg_replace('/[^0-9]/', '', (string) $snils);
//             $format = self::FORMAT_CANONICAL;
//         }

//         $snils = (string) $snils;

//         [$id, $checksum] = match ($format) {
//             self::FORMAT_CANONICAL => call_user_func(static function (string $snils): array {
//                 $snils = str_pad($snils, 11, '0', STR_PAD_LEFT);

//                 return [substr($snils, 0, 9), substr($snils, -2)];
//             }, $snils),

//             self::FORMAT_SPACE,
//             self::FORMAT_HYPHEN => call_user_func(static function (string $snils, string $format): array {
//                 $separator = $format === self::FORMAT_SPACE ? '\\s' : self::SEPARATOR_HYPHEN;

//                 if (preg_match('/^(\d{3})-(\d{3})-(\d{3})' . $separator . '(\d{2})$/', $snils, $matches) > 0) {
//                     return [$matches[1] . $matches[2] . $matches[3], $matches[4]];
//                 }

//                 return [null, null];
//             }, $snils, $format),

//             default => throw new \Exception('Неизвестный формат СНИЛСа: ' . $format)
//         };

//         if ($id === null || $checksum === null || ! static::isIdValid($id)) {
//             return false;
//         }

//         if ($checksum !== static::checksum($id)) {
//             return false;
//         }

//         return (int) $id;
//     }

// 	public static function isIdValid(string|int|null $id): bool
//     {
//         return is_numeric($id)
//             && (is_int($id) || strpos($id, '.') === false) // Инвалидация строк типа "12345678.9"
//             && self::ID_MIN <= $id && $id <= self::ID_MAX;
//     }	

// 	public function getID(): int
//     {
//         return $this->id;
//     }

//     public static function checksum(string|int|null $id): string|null
//     {
//         if ($id === null || ! static::isIdValid($id)) {
//             return null;
//         }

//         $snils9 = str_pad((string) $id, 9, '0', STR_PAD_LEFT);

//         $sum = 0;
//         for ($pos = 9; $pos > 0; $pos--) {
//             $sum += (int) $snils9[9 - $pos] * $pos;
//         }

//         return substr(str_pad((string) ($sum % 101), 2, '0', STR_PAD_LEFT), -2);
//     }	
// }