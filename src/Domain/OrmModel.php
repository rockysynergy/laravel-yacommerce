<?php

namespace Orq\Laravel\YaCommerce\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orq\Laravel\YaCommerce\IllegalArgumentException;

abstract class OrmModel extends Model
{
    use SoftDeletes;

    /**
     *
     * @param callable $preHook The function to be called before the instance is made
     * @param callable $preHook The function to be called after the instance is made
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     * @return Orq\Laravel\YaCommerce\Domain\OrmModel
     */
    public function createNew(array $data, callable $preHook = null, callable $postHook = null)
    {
        try {
            $this->validate($data);
            if (!is_null($preHook)) $data = $preHook($data);
            $model = $this->makeInstance($data);
            if (!is_null($postHook)) $postHook($model, $data);
            $model->save();
            return $model;
        } catch (IllegalArgumentException $e) {
            throw $e;
        }
    }

    /**
     * @param callable $preHook The function to be called before the instance is made
     * @param callable $preHook The function to be called after the instance is made
     *
     * @return void
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function updateInstance(array $data, callable $preHook = null, callable $postHook = null):void
    {
        if (!isset($data['id'])) throw new IllegalArgumentException(trans("YaCommerce::message.update_no-id"), 1576220777);
        try {
            $this->validate($data);
            if (!is_null($preHook)) $data = $preHook($data);
            $model = $this->makeInstance($data, static::find($data['id']));
            if (!is_null($postHook)) $postHook($model, $data);
            $model->save();
        } catch (IllegalArgumentException $e) {
            throw $e;
        }
    }

    /**
     * validate the data which which will store into the database
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function validate(array $data): void
    {
        $rules = $this->makeRules();
        $messages = $this->makeMessages($rules);
        $validator = Validator::make($data, $rules, $messages);

        $errorMsgs = $validator->errors()->all();
        if (count($errorMsgs) > 0) {
            throw new IllegalArgumentException($errorMsgs[0], 1573629695);
        }
    }

    abstract protected function makeRules():array;

    protected function makeMessages(array $rules):array
    {
        $msg = [];
        foreach ($rules as $k => $v) {
            $attr = $k;
            $aRules = explode('|', $v);

            foreach ($aRules as $aRule) {
                $ruleBits = explode(':', $aRule);
                $rKey = $ruleBits[0];
                $repMsg = [];
                $repMsg['field'] = trans("YaCommerce::fields.product.{$attr}");

                if (count($ruleBits) > 1) {
                    $ruleAttr = explode(',', $ruleBits[1]);

                    if (count($ruleAttr) == 1) $repMsg['first'] = $ruleAttr[0];
                    if (count($ruleAttr) == 2) $repMsg['second'] = $ruleAttr[1];
                    if (count($ruleAttr) == 3) $repMsg['third'] = $ruleAttr[2];
                }

                $msg["{$attr}.${rKey}"] = trans("YaCommerce::validation.{$rKey}", $repMsg);
            }
        }

        return $msg;
    }


    /**
     * make new instance
     *
     * @return OrmModel
     */
    public function makeInstance(array $data, OrmModel $instance = null)
    {
        $instance = is_null($instance) ? new static() : $instance;
        foreach ($data as $k => $v) {
            $instance->$k = $v;
        }
        return $instance;
    }

    /**
     * Delete instance by id
     *
     * @param int $id
     * @throws  Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function deleteById(int $id, callable $hook = null):void
    {
        $m = new static();
        $model = $m->find($id);
        if (!$model) throw new IllegalArgumentException(trans("YaCommerce::message.no-record"), 1576480164);
        if (!is_null($hook)) {
            $hook($model);
        }
        $model->delete();
    }

    /**
     * find instance by id
     *
     * @param int $id
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     * @return Orq\Laravel\YaCommerce\Domain\Product\Model\ormModel
     */
    public function findById(int $id)
    {
        $m = new static();
        $model = $m->find($id);
        if (!$model) throw new IllegalArgumentException(trans("YaCommerce::message.no-record"), 1576480181);
        return $model;
    }
}
