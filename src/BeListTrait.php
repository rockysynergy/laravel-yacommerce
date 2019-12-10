<?php

namespace Orq\Laravel\YaCommerce;

use Illuminate\Support\Facades\Log;

trait BeListTrait
{
    /**
     * @return query instance
     */
    public function filterByCreatedAt($query, $info)
    {
        if (isset($info['filterCreatedAt']) && strtolower($info['filterCreatedAt']) != 'null') {
            $query = $query->whereDate('created_at', $info['filterCreatedAt']);
        }
        return $query;
    }

    /**
     * @return ['count', 'data']
     */
    public function paginate($query, $info): array
    {
        $query = $this->filterByCreatedAt($query, $info);
        $count = $query->count();
        if (isset($info['page']) && isset($info['limit'])) {
            $offset = ($info['page'] - 1) * $info['limit'];
            $limit = $info['limit'];
            $query = $query->offset($offset)->limit($limit);
        }
        return ['count' => $count, 'data' => $query->get()->toArray()];
    }
}
