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
