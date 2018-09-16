<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => 'bindings'
], function($api) {

    $api->group([
        'middleware' => 'api.throttle'
    ], function($api) {
        $api->group([
            'limit' => config('api.rate_limits.sign.limit'),
            'expires' => config('api.rate_limits.sign.expires'),
        ], function ($api) {
            //发送短信
            $api->post('verificationCodes', 'VerificationCodesController@store')->name('api.verificationCodes.store');
        });

        $api->group([
            'limit' => config('api.rate_limits.access.limit'),
            'expires' => config('api.rate_limits.access.expires'),
        ], function ($api) {
            //首页
            //推荐品牌
            $api->get('brands/recommend', 'BrandsController@recommend')->name('api.brands.recommend');
            //车型列表
            $api->get('stypes', 'StypesController@index')->name('api.stypes.index');
            //Banner列表
            $api->get('banners', 'BannersController@index')->name('api.banners.index');

            //购车方案
            $api->get('cmodels/program', 'CmodelsController@program')->name('api.cmodels.program');
            //详情
            $api->get('cmodels/{cmodel}', 'CmodelsController@show')->name('api.cmodels.show');

            //购车
            //品牌列表
            $api->get('brands', 'BrandsController@index')->name('api.brands.index');
            //车系列表
            $api->get('series', 'SeriesController@index')->name('api.series.index');
            //车型列表
            $api->get('cmodels', 'CmodelsController@index')->name('api.cmodels.index');

            //普通登录
            $api->post('authorizations', 'AuthorizationsController@store')->name('api.authorizations.store');
            //小程序登录
            $api->post('weapp/authorizations', 'AuthorizationsController@weappStore')
                ->name('api.weapp.authorizations.store');
            //刷新token
            $api->put('authorizations/current', 'AuthorizationsController@update')->name('api.authorizations.update');

            // 图片验证码
            $api->post('captchas', 'CaptchasController@store')->name('api.captchas.store');

            //业务员登录
            $api->post('worker/authorizations', 'AuthorizationsController@workerStore')->name('api.worker.authorizations.store');
            //业务员刷新token
            $api->put('worker/authorizations/current', 'AuthorizationsController@workerUpdate')
                ->name('api.worker.authorizations.update');
            //业务员删除token
            $api->delete('worker/authorizations/current', 'AuthorizationsController@destroy')
                ->name('api.worker.authorizations.destroy');

            /**
             * 客户端 需要 token 验证的接口
             */
            $api->group(['middleware' => 'api.auth'], function ($api) {
                //获取微信手机号
                $api->put('weapp/users', 'UsersController@weappUpdate')->name('api.weapp.users.update');
                //获取个人用户信息
                $api->get('users/me', 'UsersController@me')->name('api.users.me');
                //个代申请
                $api->post('applies', 'AppliesController@store')->name('api.applies.store');
				//上传图片
				$api->post('images', 'ImagesController@store')->name('api.images.store');
            });

            /**
             * 业务员端 需要 token 验证的接口
             */
            $api->group(['middleware' => ['worker', 'api.auth']], function ($api) {
                //获取工作人员信息
                $api->get('worker', 'WorkersController@me')->name('api.worker.me');
                //获取客户
                $api->get('users', 'UsersController@index')->name('api.users.index');
                //我的团队
                $api->get('workers', 'WorkersController@index')->name('api.workers.index');
            });
        });
    });
});
