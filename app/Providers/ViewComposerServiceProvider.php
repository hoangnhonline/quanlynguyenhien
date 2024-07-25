<?php
namespace App\Providers;

use App\Http\View\Composers\TaskComposer;
use Illuminate\Support\ServiceProvider;
use Hash;
use App\Models\UserNotification;
use App\Models\TourSystem;
use App\Models\City;
use App\Models\Collecter;
use App\Models\Settings;

use Helper;
use Auth;
class ViewComposerServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//Call function composerSidebar
		$this->composerMenu();
        view()->composer(['task.*'], TaskComposer::class);
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Composer the sidebar
	 */
	private function composerMenu()
	{

		view()->composer( '*' , function( $view ){

	        //set city_id default
	        $city_id_default = session('city_id_default', 1);

	        $routeName = \Request::route()->getName();

	        //$isEdit = Auth::check();
	        $userRole = $notiList = $userLogged = null;
	        if(Auth::check()){
	        	$notiList = UserNotification::where('user_id', Auth::user()->id)->where('is_read', 0)->orderBy('id', 'desc')->get();
	        	$userRole = Auth::user()->role;
	        	$userLogged = Auth::user();
	        	$city_id_default = session('city_id_default', $userLogged->city_id);
	        }
	       	$tourSystemList = TourSystem::where('status', 1)->get();
	       	$tourSystemName = [];
	       	foreach($tourSystemList as $t){
	       		$tourSystemName[$t->id] = [
	       			'name' => $t->name,
	       			'bg_color' => $t->bg_color
	       		];
	       	}
	       	$cityName = [];
	       	$cityList = City::where('status', 1)->orderBy('display_order')->get();
	       	foreach($cityList as $city){
	       		$cityName[$city->id] = $city->name;
	       	}
	       	$collecterList = Collecter::where('status', 1)->orderBy('display_order')->get();
	       	$collecterNameArr = Helper::getCollecterNameArr();
	       	$settingArr = Settings::whereRaw('1')->pluck('value', 'name');
			$view->with( [
					// 'settingArr' => $settingArr,
					// 'articleCate' => $articleCate,
					'notiList' => $notiList,
					'routeName' => $routeName,
					// 'textArr' => $textArr,
					// 'isEdit' => $isEdit,
					'userRole' => $userRole,
					'tourSystemName' => $tourSystemName,
					'cityList' => $cityList,
					'cityName' => $cityName,
					'city_id_default' => $city_id_default,
					'userLogged' => $userLogged,
					'collecterList' => $collecterList,
					'collecterNameArr' => $collecterNameArr,
					'settingArr' => $settingArr
			] );

		});
	}

}
