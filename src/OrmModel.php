<?php

namespace Orq\Laravel\YaCommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class OrmModel extends Model
{
    use SoftDeletes;

    /**
     * The validation rules
     */
    protected static $rules = [ ];

    /**
     * The validation messages
     */
    protected static $messages = [ ];

    /**
     * validate the data which which will store into the database
     *
     * @exception(DomainException)
     */
    public static function validate(array $data): void
    {
        $validator = Validator::make($data, static::$rules, static::$messages);

        $errorMsgs = $validator->errors()->all();
        if (count($errorMsgs) > 0) {
            throw new IllegalArgumentException($errorMsgs[0], 1573629695);
        }
    }
}
