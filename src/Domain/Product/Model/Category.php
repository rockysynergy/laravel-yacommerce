<?php

namespace Orq\Laravel\YaCommerce\Domain\Product\Model;

use Kalnoy\Nestedset\NodeTrait;
use Kalnoy\Nestedset\Collection;
use Orq\Laravel\YaCommerce\Domain\OrmModel;
use Orq\Laravel\YaCommerce\IllegalArgumentException;

/**
 * The Category model.
 * Uses Eloquent model to work with Database
 * The category is attached to Shop
 *
 * It uses nestesset for tree related functions
 * @see https://github.com/lazychaser/laravel-nestedset#retrieving-nodes
 *
 * @author rockysynergy@qq.com
 */
class Category extends OrmModel
{

    use NodeTrait;

    protected $table = 'yac_categories';
    protected $model = 'category';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Make validation rules for the model
     */
    protected function makeRules():array
    {
        return [
            'title' => 'max:100',
            'pic' => 'max:120',
            'parent_id' => 'gte:0',
            'shop_id' => 'gte:1',
        ];
    }

    /**
     * Add new category node
     * If `parent_id` is provided, it will append to the parent
     */
    public function createNew(array $data):void
    {
        try {
            $this->validate($data);
            $node = $this->makeInstance($data);
            $this->insert($node, $data);
        } catch (IllegalArgumentException $e) {
            throw $e;
        }
    }

    /**
     * Update category
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function updateInstance(array $data):void
    {
        if (!isset($data['id'])) throw new IllegalArgumentException(trans("YaCommerce::message.update_no-id"), 1576220777);
        try {
            $this->validate($data);
            $node = $this->makeInstance($data, self::find($data['id']));
            $this->insert($node, $data);
        } catch (IllegalArgumentException $e) {
            throw $e;
        }
    }

    /**
     * Insert into DB
     */
    protected function insert(Category $node, array $data):void
    {
        if (isset($data['parent_id'])) {
            $p = self::find($data['parent_id']);
            $p->appendNode($node);
        } else {
            $node->save();
        }
    }


    /**
     * get all categories for shop.
     * It will return a tree
     *
     * @param int $shopId The shop Id
     * @param bool $includeTrashed
     * @return Kalnoy\Nestedset\Collection
     */
    public function getAllForShop(int $shopId, array $filter = [], bool $includeTrashed = false): Collection
    {
        $query = self::where('shop_id', '=', $shopId);
        if (isset($filter['title'])) $query = $query->where('title', 'like', "%{$filter['title']}%");
        if ($includeTrashed) $query = $query->withTrashed();
        return $query->get()->toTree();
    }

}
