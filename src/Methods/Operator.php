<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

use Tamedevelopers\Support\Str;

/**
 * Operator
 *
 * Handles operator-based validation rules by comparing an input value
 * against an expected value using various comparison operators.
 *
 * Returns `true` when the rule condition is satisfied (i.e. the validator
 * treats a matched operator condition as an error flag), and `false`
 * when the condition is not met or the operator is unrecognized.
 *
 * Supported operators
 * -------------------
 *  Equality:
 *      ==          loose equality
 *      ===         strict equality
 *      !=          loose inequality
 *      !==         strict inequality
 *
 *  Single comparison (numeric):
 *      >           greater than
 *      >=          greater than or equal
 *      <           less than
 *      <=          less than or equal
 *
 *  Exclusive range (bounds not included):
 *      <and>       lower < value < upper        (inside range)
 *      <or>        value < lower OR value > upper (outside range)
 *
 *  Inclusive range (bounds included):
 *      <=and>=     lower <= value <= upper      (inside range)
 *      <=or>=      value <= lower OR value >= upper (outside range)
 *
 *  Mixed bounds:
 *      <and>=      lower <  value <= upper
 *      <=and>      lower <= value <  upper
 *
 *  Step / multiple:
 *      step        value must be a multiple of the given step
 *      multiple    alias of `step`
 *
 *  Range + step (min,max,step):
 *      <and>step
 *      <and>:step
 *      steprange
 *
 *  String-length awareness:
 *      When the rule type (data_type) is one of `str_len`, `strlen`, `sl`,
 *      the comparison is performed against `strlen($input)` instead of the
 *      raw input value. This applies to every operator above.
 *
 *  Whitespace tolerance:
 *      The operator token is trimmed of surrounding whitespace. Internal
 *      spaces are NOT stripped here; variants like `< and >` must be
 *      normalized upstream or matched explicitly.
 */
class Operator
{
    /**
     * Rule types whose comparison is based on string length.
     *
     * @var array<int, string>
     */
    private const RULE_STRLEN = ['str_len', 'strlen', 'sl'];

    /**
     * Checking for flag type error.
     * Returns true on error found and false if no error is found.
     *
     * @param  mixed $validator  Validator instance exposing a `param` property.
     * @param  array $dataType   Rule descriptor:
     *                           - operator   (string)  comparison operator
     *                           - value      (mixed)   value or range to compare against
     *                           - input_name (string)  input key being validated
     *                           - data_type  (string)  rule type (e.g. "strlen", "int")
     * @return bool
     */
    public static function validate($validator, $dataType = []): bool
    {
        $operator   = Str::trim($dataType['operator'] ?? '');
        $value      = $dataType['value'] ?? null;
        $input_name = $dataType['input_name'] ?? null;
        $data_type  = $dataType['data_type'] ?? null;
        $param      = $validator->param;

        if (!$input_name || !ValidatorMethod::isParamSet($input_name)) {
            return false;
        }

        $dataString = $param[$input_name];
        $isStrLen   = in_array($data_type, self::RULE_STRLEN, true);
        $comparable = $isStrLen ? strlen((string) $dataString) : $dataString;

        switch ($operator) {
            // -- Equality ------------------------------------------------
            case '==':  return $dataString == $value;
            case '===': return $dataString === $value;
            case '!=':  return $dataString != $value;
            case '!==': return $dataString !== $value;

            // -- Single comparison ---------------------------------------
            case '>':  return self::compare($comparable, $value, '>');
            case '>=': return self::compare($comparable, $value, '>=');
            case '<':  return self::compare($comparable, $value, '<');
            case '<=': return self::compare($comparable, $value, '<=');

            // -- Exclusive range (a < v < b  |  v < a OR v > b) ----------
            case '<and>':
                return self::insideRange($comparable, $value, false);

            case '<or>':
                return self::outsideRange($comparable, $value, false);

            // -- Inclusive range (a <= v <= b  |  v <= a OR v >= b) ------
            case '<=and>=':
                return self::insideRange($comparable, $value, true);

            case '<=or>=':
                return self::outsideRange($comparable, $value, true);

            // -- Mixed bounds --------------------------------------------
            case '<and>=':    return self::mixedRange($comparable, $value, false, true);
            case '<=and>':    return self::mixedRange($comparable, $value, true, false);

            // -- Step validation (value must be a multiple of step) ------
            case 'step':
            case 'multiple':
                return self::step($comparable, $value);

            // -- Range + step (min,max,step) -----------------------------
            case '<and>step':
            case '<and>:step':
            case 'steprange':
                return self::rangeStep($comparable, $value);
        }

        return false;
    }

    /**
     * Single operator comparison.
     *
     * Casts both sides to float and applies the requested comparison operator.
     *
     * @param  mixed  $left
     * @param  mixed  $right
     * @param  string $operator  One of: ">", ">=", "<", "<="
     * @return bool
     */
    private static function compare($left, $right, string $operator): bool
    {
        $left  = (float) $left;
        $right = (float) $right;

        return match ($operator) {
            '>'     => $left >  $right,
            '>='    => $left >= $right,
            '<'     => $left <  $right,
            '<='    => $left <= $right,
            default => false,
        };
    }

    /**
     * Value must be inside the range.
     *
     * Inclusive flag controls whether bounds are `<=` / `>=` or `<` / `>`.
     *
     * Rule examples:
     *   "age:<and>:18,20"       -> 18 <  age <  20
     *   "age:<=and>=:18,20"     -> 18 <= age <= 20
     *
     * @param  mixed $comparable
     * @param  mixed $value      Range string, e.g. "18,20" or "18-20"
     * @param  bool  $inclusive  True for `<=`/`>=`, false for `<`/`>`
     * @return bool
     */
    private static function insideRange($comparable, $value, bool $inclusive): bool
    {
        [$lower, $upper] = self::parseRange($value);
        $comparable = (float) $comparable;

        return $inclusive
            ? ($comparable >= $lower && $comparable <= $upper)
            : ($comparable >  $lower && $comparable <  $upper);
    }

    /**
     * Value must be outside the range.
     *
     * Inclusive flag controls whether the bounds themselves count as "outside".
     *
     * Rule examples:
     *   "age:<or>:18,20"        -> age <  18 OR age >  20
     *   "age:<=or>=:18,20"      -> age <= 18 OR age >= 20
     *
     * @param  mixed $comparable
     * @param  mixed $value      Range string, e.g. "18,20" or "18-20"
     * @param  bool  $inclusive  True for `<=`/`>=`, false for `<`/`>`
     * @return bool
     */
    private static function outsideRange($comparable, $value, bool $inclusive): bool
    {
        [$lower, $upper] = self::parseRange($value);
        $comparable = (float) $comparable;

        return $inclusive
            ? ($comparable <= $lower || $comparable >= $upper)
            : ($comparable <  $lower || $comparable >  $upper);
    }

    /**
     * Mixed bounds — e.g. lower exclusive, upper inclusive.
     *
     * Rule examples:
     *   "age:<and>=:18,20"      -> 18 <  age <= 20
     *   "age:<=and>:18,20"      -> 18 <= age <  20
     *
     * @param  mixed $comparable
     * @param  mixed $value            Range string, e.g. "18,20"
     * @param  bool  $lowerInclusive   Use `>=` for lower bound?
     * @param  bool  $upperInclusive   Use `<=` for upper bound?
     * @return bool
     */
    private static function mixedRange($comparable, $value, bool $lowerInclusive, bool $upperInclusive): bool
    {
        [$lower, $upper] = self::parseRange($value);
        $comparable = (float) $comparable;

        $lowPass  = $lowerInclusive ? $comparable >= $lower : $comparable >  $lower;
        $highPass = $upperInclusive ? $comparable <= $upper : $comparable <  $upper;

        return $lowPass && $highPass;
    }

    /**
     * Value must be a multiple of step.
     *
     * Rule example:
     *   "quantity:step:5"       -> quantity must be a multiple of 5
     *
     * Uses an epsilon comparison to avoid floating-point precision issues.
     *
     * @param  mixed $comparable
     * @param  mixed $value      The step value (must be > 0)
     * @return bool
     */
    private static function step($comparable, $value): bool
    {
        $step = (float) $value;

        if ($step <= 0.0) {
            return false;
        }

        $comparable = (float) $comparable;

        // Use epsilon comparison to avoid float precision issues
        $remainder = fmod($comparable, $step);

        return abs($remainder) < 1e-9 || abs($remainder - $step) < 1e-9;
    }

    /**
     * Value must be inside range AND aligned to step.
     *
     * Rule examples:
     *   "quantity:<and> step:10,50,5"    -> 10 < quantity < 50 AND multiple of 5
     *   "quantity:<and>:step:10,50,5"    -> same as above
     *   "quantity:step range:10,50,5"    -> same as above
     *
     * Parsing priority:
     *   - 3 values  -> [lower, upper, step]
     *   - 2 values  -> [lower, upper], step defaults to 1
     *   - 1 value   -> treated as step only, range unbounded
     *
     * @param  mixed $comparable
     * @param  mixed $value      Comma/dash-separated "min,max,step" (or partial)
     * @return bool
     */
    private static function rangeStep($comparable, $value): bool
    {
        $parts = self::parseParts($value);
        $count = count($parts);

        if ($count >= 3) {
            [$lower, $upper, $step] = $parts;
        } elseif ($count === 2) {
            [$lower, $upper] = $parts;
            $step = 1.0;
        } else {
            // Only a step given — no range constraint
            $lower = null;
            $upper = null;
            $step  = $parts[0] ?? 1.0;
        }

        $comparable = (float) $comparable;

        // Range check (exclusive bounds, matching <and> default)
        if ($lower !== null && $comparable <= $lower) {
            return false;
        }
        if ($upper !== null && $comparable >= $upper) {
            return false;
        }

        // Step check
        if ($step <= 0.0) {
            return false;
        }

        $remainder = fmod($comparable, $step);

        return abs($remainder) < 1e-9 || abs($remainder - $step) < 1e-9;
    }

    /**
     * Parse a range string like "18,20" or "18-20" into [lower, upper].
     *
     * Bounds are normalized so the returned pair is always [min, max].
     *
     * @param  mixed $value
     * @return array{0: float, 1: float}
     */
    private static function parseRange($value): array
    {
        $parts = self::parseParts($value);

        $lower = $parts[0] ?? 0.0;
        $upper = $parts[1] ?? $lower;

        if ($lower > $upper) {
            [$lower, $upper] = [$upper, $lower];
        }

        return [$lower, $upper];
    }

    /**
     * Parse a comma- or dash-separated string into an array of floats.
     *
     * Returns all parts (not just two) so callers can consume 3-value ranges.
     * Empty segments are ignored.
     *
     * @param  mixed $value
     * @return array<int, float>
     */
    private static function parseParts($value): array
    {
        $value = (string) $value;

        // Split on comma or dash (with optional surrounding whitespace)
        $raw = preg_split('/\s*[,\-]\s*/', $value);

        if ($raw === false) {
            return [];
        }

        $parts = [];
        foreach ($raw as $item) {
            $item = trim($item);
            if ($item === '') {
                continue;
            }
            $parts[] = (float) $item;
        }

        return $parts;
    }
}