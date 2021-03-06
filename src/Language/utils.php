<?php

namespace Digia\GraphQL\Language;

/**
 * @param int $cp
 * @return string
 */
function chrUTF8(int $cp)
{
    return mb_convert_encoding(pack('N', $cp), 'UTF-8', 'UCS-4BE');
}

/**
 * @param string $string
 * @return int
 */
function ordUTF8(string $string)
{
    [, $ord] = unpack('N', mb_convert_encoding($string, 'UCS-4BE', 'UTF-8'));

    return $ord;
}

/**
 * @param string $string
 * @param int    $position
 * @return int
 */
function charCodeAt(string $string, int $position): int
{
    return ordUTF8($string[$position]);
}

/**
 * @param int $code
 * @return string
 */
function printCharCode(int $code): string
{
    if ($code === null) {
        return '<EOF>';
    }
    return $code < 0x007F
        // Trust JSON for ASCII.
        ? json_encode(chrUTF8($code))
        // Otherwise print the escaped form.
        : '"\\u' . dechex($code) . '"';
}

/**
 * @param string   $string
 * @param int      $start
 * @param int|null $end
 * @return string
 */
function sliceString(string $string, int $start, int $end = null): string
{
    $length = $end !== null ? $end - $start : mb_strlen($string) - $start;
    return mb_substr($string, $start, $length);
}

/**
 * @param int $code
 * @return bool
 */
function isLetter(int $code): bool
{
    return ($code >= 65 && $code <= 90) || ($code >= 97 && $code <= 122); // a-z or A-Z
}

/**
 * @param int $code
 * @return bool
 */
function isNumber(int $code): bool
{
    // - or 0-9
    return $code === 45 || ($code >= 48 && $code <= 57);
}

/**
 * @param int $code
 * @return bool
 */
function isUnderscore(int $code): bool
{
    return $code === 95; // _
}

/**
 * @param int $code
 * @return bool
 */
function isAlphaNumeric(int $code): bool
{
    return isLetter($code) || isNumber($code) || isUnderscore($code);
}

/**
 * @param int $code
 * @return bool
 */
function isSourceCharacter(int $code): bool
{
    return $code < 0x0020 && $code !== 0x0009 && $code !== 0x000a && $code !== 0x000d;
}

/**
 * Converts four hexidecimal chars to the integer that the
 * string represents. For example, uniCharCode('0','0','0','f')
 * will return 15, and uniCharCode('0','0','f','f') returns 255.
 *
 * @param string $a
 * @param string $b
 * @param string $c
 * @param string $d
 * @return string
 */
function uniCharCode(string $a, string $b, string $c, string $d): string
{
    return (dechex(ordUTF8($a)) << 12) | (dechex(ordUTF8($b)) << 8) | (dechex(ordUTF8($c)) << 4) | dechex(ordUTF8($d));
}

/**
 * @param string $value
 * @return bool
 */
function isOperation(string $value): bool
{
    // TODO: Benchmark
    return \in_array($value, ['query', 'mutation', 'subscription'], true);
}

/**
 * @param array $location
 * @return array|null
 */
function locationShorthandToArray(array $location): ?array
{
    return isset($location[0], $location[1]) ? ['line' => $location[0], 'column' => $location[1]] : null;
}

/**
 * @param array $locations
 * @return array
 */
function locationsShorthandToArray(array $locations): array
{
    return array_map(function ($shorthand) {
        return locationShorthandToArray($shorthand);
    }, $locations);
}

/**
 * @param array $array
 * @return string
 */
function block(array $array): string
{
    return !empty($array) ? "{\n" . indent(implode("\n", $array)) . "\n}" : '';
}

/**
 * @param string      $start
 * @param null|string $maybeString
 * @param null|string $end
 * @return string
 */
function wrap(string $start, ?string $maybeString = null, ?string $end = null): string
{
    return null !== $maybeString ? ($start . $maybeString . ($end ?? '')) : '';
}

/**
 * @param null|string $maybeString
 * @return string
 */
function indent(?string $maybeString): string
{
    return null !== $maybeString ? '  ' . preg_replace("/\n/", "\n  ", $maybeString) : '';
}

/**
 * @param string $str
 * @return string
 */
function dedent(string $str): string
{
    $trimmed = \preg_replace("/^\n*|[ \t]*$/", '', $str); // Remove leading newline and trailing whitespace
    $matches = [];
    \preg_match("/^[ \t]*/", $trimmed, $matches); // Figure out indent
    $indent = $matches[0];
    return \str_replace($indent, '', $trimmed); // Remove indent
}
