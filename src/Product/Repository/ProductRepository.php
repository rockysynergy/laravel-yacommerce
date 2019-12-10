<?php

namespace Orq\Laravel\YaCommerce\Product\Repository;

use App\Service\BeListTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Orq\Laravel\YaCommerce\Product\Model\Product;
use Orq\DddBase\Repository\AbstractRepository;

class ProductRepository extends AbstractRepository
{
    protected static $table = 'yac_products';
    protected static $class = Product::class;
    use BeListTrait;

    public static function getAllByCategoryIds(array $catIds): Collection
    {
        return DB::table(self::$table)->whereIn('category_id', $catIds)->get();
    }

    public function getAllByShopId(int $shopId, bool $showAll = true, array $filter = []): array
    {
        $query = DB::table('yac_products as A');
        if (isset($filter['filterTitle'])) $query = $query->where('A.title', 'like', "%{$filter['filterTitle']}%");
        if (isset($filter['filterStatus'])) $query = $query->where('status', '=', $filter['filterStatus']);
        $query = $query->join('yac_categories', function ($join) use ($shopId) {
            $join->on('A.category_id', '=', 'yac_categories.id')
                ->where('yac_categories.shop_id', '=', $shopId);
        })
            ->select('A.*', 'yac_categories.title as category');


        if ($showAll) {
            return $this->paginate($query, $filter);
        } else {
            $query = $query->where('A.status', 1)
                ->where('inventory', '>', 0);
            return $this->paginate($query, $filter);
        }
    }

    public static function incInventoryForProducts(array $data): void
    {
        foreach ($data as $d) {
            DB::table(self::$table)->where('id', $d[0])->increment('inventory', $d[1]);
        }
    }
}
