<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
class ValidCategory implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string = null): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $targets = DB::table('categories')->pluck('board_name')->toArray();
        if(!in_array($value,$targets)){
            $fail('category must be in categories table\'s board_name');
        }
    }
}
