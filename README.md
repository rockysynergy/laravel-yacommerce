Yet Another Commerce Pakcage for Laravel.

# Get Started
1. use composesr install the package
2. publish the config, route and views
3. customize them according to your specific needs
4. Look at the config files to update settings(Mostly for WxPay) in the .env file.
5. Add the Routes as below

```PHP
Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {
     \Orq\Laravel\YaCommerce\Yac::webRoutes();
});

```

6. For bpshop add below to register method of `App\Providers\AppServiceProvider`
```PHP
    $this->app->bind(
        'Orq\Laravel\YaCommerce\Order\PrepaidUserInterface',
        'App\Service\BpshopUserService'
    );
```

7. Example front end code
```JavaScript

  //响应“购买”按钮点击事件
  buy: function(e) {
    if (this.data.shopType == 'shop') {
      // 添加到购物车然后跳转到购物车页面
      EXT.wxRequest({
        url: APP.globalData.baseUrl + 'shop_addcartitem',
        data: {
          product_id: e.currentTarget.dataset.productid,
          amount: 1,
          shop_id: this.data.shopId
        },
      }).then(data => {
        wx.navigateTo({
          url: '/pages/shop_cart/shop_cart',
        })
      })
    } else {
      // 没有购物车，直接显示地址表单
      if (this.data.shopType == 'seckill' && this.data.prodInfo.inventory < 1) {
        wx.showModal({
          title: '已抢光',
        })
      } else {
        this.setData({
          showAddressForm: true
        });
      }
    }
  },

  // 提交订单
  saveOrder: function(e) {
    let items = [];
    let that = this;
    items.push({
      'thumbnail': that.data.prodInfo.cover_pic,
      'title': that.data.prodInfo.title,
      'amount': 1,
      'pay_amount': (that.data.shopType == 'seckill') ? that.data.prodInfo.sk_price : that.data.prodInfo.price,
      'unit_price': that.data.prodInfo.price,
      'product_id': that.data.prodInfo.id
    });

    EXT.wxRequest({
      url: APP.globalData.baseUrl + 'shop_makeorder',
      data: {
        items: items,
        shipaddress: e.detail.address,
        shop_type: that.data.shopType,
        pid: that.data.shopId,
        user_id: that.data.user_id,
      }
    }).then(data => {
      if (that.data.shopType == 'bp_shop') {
        wx.showModal({
          title: '兑换成功',
        });
        wx.navigateTo({
          url: '/pages/my_bonus/my_bonus?pid='+that.data.shopId,
        })
      }
      if (that.data.shopType == 'seckill') {
        that.pay(data);
      }
    }).catch(err => {
      wx.showModal({
        title: err.message,
      })
    });
  },


  // 调取支付界面
  pay: function(data) {
    let p = data;
    let that = this;

    wx.requestPayment({
      timeStamp: p.timeStamp.toString(),
      nonceStr: p.nonceStr,
      package: p.package,
      signType: p.signType,
      paySign: p.paySign,
      success: function(res) {
        wx.navigateTo({
          url: '/pages/shop/shop?shopType=' + that.shopType,
        })
      }
    })
  },
```
