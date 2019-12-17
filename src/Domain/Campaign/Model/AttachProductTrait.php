<?php
namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Illuminate\Database\Eloquent\Collection;

trait AttachProductTrait
 {

    /**
     * add product
     *
     * @param int $productId
     * @return void
     */
    public function addProduct($productId, $campaignPrice = null):void
    {
        if (is_null($campaignPrice)) $this->products()->attach($productId);
        else $this->products()->attach($productId, ['campaign_price' => $campaignPrice]);
    }

 }
