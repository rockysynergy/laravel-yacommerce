<?php

namespace Orq\Laravel\YaCommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class OrmModel extends Model
{
    use SoftDeletes;

    /**
     * validate the data which which will store into the database
     *
     * @exception(DomainException)
     */
    public function validate(array $data): void
    {
        $rules = $this->getRules();
        $messages = $this->makeMessages($rules);
        $validator = Validator::make($data, $rules, $messages);

        $errorMsgs = $validator->errors()->all();
        if (count($errorMsgs) > 0) {
            throw new IllegalArgumentException($errorMsgs[0], 1573629695);
        }
    }

    protected function makeMessages(array $rules):array
    {
        $msg = [];
        foreach ($rules as $k => $v) {
            $attr = $k;
            $aRules = explode('|', $v);

            foreach ($aRules as $aRule) {
                $ruleBits = explode(':', $aRule);
                $rKey = $ruleBits[0];
                $ruleAttr = explode(',', $ruleBits[1]);

                $repMsg = [];
                if (count($ruleAttr) == 1) $repMsg['first'] = $ruleBits[0];
                if (count($ruleAttr) == 2) $repMsg['second'] = $ruleBits[1];
                if (count($ruleAttr) == 3) $repMsg['third'] = $ruleBits[2];

                $msg["{$attr}.${rKey}"] = trans("YaCommerce:validation.{$rKey}", $repMsg);
            }
        }

        return $msg;
    }
}
