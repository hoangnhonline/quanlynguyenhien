<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Route::get('/setPartner', ['uses' => 'TestController@setPartner', 'as' => 'set-partner']);
Route::get('/test-mail', ['uses' => 'HomeController@testMail', 'as' => 'test-mail']);
Route::get('/transDriver', ['uses' => 'TestController@transDriver', 'as' => 'trans-driver']);
Route::get('/testCommision/{userId}', ['uses' => 'TestController@testCommision', 'as' => 'testCommision']);
Route::get('/testGenerateCode', ['uses' => 'TestController@testGenerateCode']);
Route::get('/import-sao-Ke', ['uses' => 'TestController@importSaoKe']);

Route::get('/set-city-id-default', ['uses' => 'GeneralController@setCityDefault', 'as' => 'set-city-id-default']);
Route::get('/diem-danh/{code}', ['as' => 'diem-danh-public', 'uses' => 'MediaController@diemDanhPublic']);
Route::get('/ajax-store-public', ['as' => 'ajax-store-public', 'uses' => 'MediaController@ajaxStorePublic']);
Route::get('/debug', ['as' => 'debugg', 'uses' => 'GeneralController@debug']);
Route::get('/pdf-tour', ['uses' => 'PdfController@tour', 'as' => 'pdf-tour']);
Route::get('/view-pdf', ['uses' => 'PdfController@viewPdf', 'as' => 'view-pdf']);
Route::get('/', ['uses' => 'Frontend\HomeController@getChild', 'as' => 'get-child']);

Route::get('/revertExport', ['uses' => 'BookingController@revertExport', 'as' => 'revertExport']);
Route::get('/cal-tour', ['uses' => 'BookingController@calTour', 'as' => 'calTour']);
Route::get('/daily', ['uses' => 'BookingController@daily', 'as' => 'daily']);
Route::get('/total', ['uses' => 'BookingController@totalByUser', 'as' => 'total-by-user']);

Route::get('/test', ['uses' => 'TestController@index', 'as' => 'xxxx']);

Route::post('/get-child', ['uses' => 'Frontend\HomeController@getChild', 'as' => 'get-child']);
Route::get('parse', ['uses' => 'CrawlerController@parse', 'as' => 'parse']);
Route::get('chatbot-zalo', ['uses' => 'ZaloController@index', 'as' => 'chatbot-zalo']);
Route::get('zalo', ['uses' => 'ZaloController@zalo', 'as' => 'zalo']);
Route::get('zalo2', ['uses' => 'ZaloController@zalo2', 'as' => 'zalo2']);
Route::get('reset-pass', ['uses' => 'AccountController@resetPass', 'as' => 'resetPass']);
Route::get('/change-value-chung', ['as' => 'change-value-by-column-general', 'uses' => 'GeneralController@changeValue']);
Route::post('api-sms-payment', ['uses' => 'ZaloController@smsPayment', 'as' => 'api-sms-payment']);


Route::get('/affiliate', ['uses' => 'AffiliateController@index']);


Route::get('booking/insurance',   ['as' => 'booking.insurance', 'uses' => 'BookingController@insurance']);
Route::get('booking/insurance/pdf',   ['as' => 'booking.insurance.pdf', 'uses' => 'BookingController@insurancePdf']);
Route::get('booking/insurance/mail',   ['as' => 'booking.insurance.mail', 'uses' => 'BookingController@insuranceMail']);

Route::group([
    'middleware' => 'auth',
], function () {
    Route::get('/', 'HomeController@dashboard')->name('dashboard');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/sms', ['as' => 'media.sms', 'uses' => 'MediaController@smsList']);
    Route::get('/book-phong', 'HomeController@bookPhong')->name('book-phong');
    Route::get('/book-tour-cau-muc', 'HomeController@bookTourCauMuc')->name('book-tour-cau-muc');
    Route::get('/confirm-phong', 'HomeController@confirmPhong')->name('confirm-phong');
    Route::get('/mail-preview', 'HomeController@mailPreview')->name('mail-preview');
    Route::get('/mail-confirm', 'HomeController@mailConfirm')->name('mail-confirm');
    Route::get('/signature', ['uses' => 'SignatureController@index'])->name('signature');

    Route::get('/save-hoa-hong', 'HomeController@saveHoaHong')->name('save-hoa-hong');
    Route::get('/hh', ['uses' => 'BookingController@tinhHoaHong', 'as' => 'tinhHoaHong']);
    Route::get('/get-boat-prices', 'GeneralController@getBoatPrices')->name('get-boat-prices');
    Route::get('/booking-qrcode/{id}', 'BookingController@qrCode')->name('booking-qrcode');
    Route::group(['prefix' => 'hoa-hong'], function () {
        Route::get('/hotel', ['as' => 'hoa-hong-hotel', 'uses' => 'BookingHotelController@commission']);
        Route::get('/tour', ['as' => 'hoa-hong-tour', 'uses' => 'BookingTourController@commission']);
    });
    Route::get('/hoa-hong-khach-san', 'BookingController@calCommissionHotel')->name('hoa-hong-khach-san');
    Route::group(['prefix' => 'access'], function () {
        Route::get('/', ['as' => 'access.index', 'uses' => 'AccessController@index']);
        Route::get('/create', ['as' => 'access.create', 'uses' => 'AccessController@create']);
        Route::post('/store', ['as' => 'access.store', 'uses' => 'AccessController@store']);
        Route::get('{id}/edit',   ['as' => 'access.edit', 'uses' => 'AccessController@edit']);
        Route::post('/update', ['as' => 'access.update', 'uses' => 'AccessController@update']);
        Route::get('{id}/destroy', ['as' => 'access.destroy', 'uses' => 'AccessController@destroy']);
    });
    Route::group(['prefix' => 'media'], function () {
        Route::get('/', ['as' => 'media.index', 'uses' => 'MediaController@index']);
        Route::get('/diem-danh', ['as' => 'media.diem-danh', 'uses' => 'MediaController@diemDanh']);
        Route::get('/create', ['as' => 'media.create', 'uses' => 'MediaController@create']);
        Route::post('/store', ['as' => 'media.store', 'uses' => 'MediaController@store']);
        Route::get('/ajax-store', ['as' => 'media.ajax-store', 'uses' => 'MediaController@ajaxStore']);

        Route::get('{id}/edit',   ['as' => 'media.edit', 'uses' => 'MediaController@edit']);
        Route::post('/update', ['as' => 'media.update', 'uses' => 'MediaController@update']);
        Route::get('{id}/destroy', ['as' => 'media.destroy', 'uses' => 'MediaController@destroy']);
    });
    Route::group(['prefix' => 'media-rating'], function () {
        Route::get('/', ['as' => 'media-rating.index', 'uses' => 'MediaRatingController@index']);
        Route::get('{id}/destroy', ['as' => 'media-rating.destroy', 'uses' => 'MediaRatingController@destroy']);
    });
    Route::group(['prefix' => 'partner'], function () {
        Route::get('/', ['as' => 'partner.index', 'uses' => 'PartnerController@index']);
        Route::get('/create', ['as' => 'partner.create', 'uses' => 'PartnerController@create']);
        Route::post('/store', ['as' => 'partner.store', 'uses' => 'PartnerController@store']);
        Route::get('{id}/edit',   ['as' => 'partner.edit', 'uses' => 'PartnerController@edit']);
        Route::post('/update', ['as' => 'partner.update', 'uses' => 'PartnerController@update']);
        Route::get('{id}/destroy', ['as' => 'partner.destroy', 'uses' => 'PartnerController@destroy']);
        Route::get('/change-value', ['as' => 'partner.change-value-by-column', 'uses' => 'PartnerController@changeValueByColumn']);
    });
    Route::group(['prefix' => 'revenue'], function () {
        Route::get('/', ['as' => 'revenue.index', 'uses' => 'RevenueController@index']);
        Route::get('/create', ['as' => 'revenue.create', 'uses' => 'RevenueController@create']);
        Route::post('/store', ['as' => 'revenue.store', 'uses' => 'RevenueController@store']);
        Route::get('{id}/edit',   ['as' => 'revenue.edit', 'uses' => 'RevenueController@edit']);
        Route::post('/update', ['as' => 'revenue.update', 'uses' => 'RevenueController@update']);
        Route::get('{id}/destroy', ['as' => 'revenue.destroy', 'uses' => 'RevenueController@destroy']);
        Route::get('/change-value', ['as' => 'revenue.change-value-by-column', 'uses' => 'RevenueController@changeValueByColumn']);
    });
    Route::group(['prefix' => 'debt'], function () {
        Route::get('/', ['as' => 'debt.index', 'uses' => 'DebtController@index']);
        Route::get('/report', ['as' => 'debt.report', 'uses' => 'DebtController@report']);
        Route::get('/export', ['as' => 'debt.export', 'uses' => 'DebtController@export']);
        Route::get('/create', ['as' => 'debt.create', 'uses' => 'DebtController@create']);
        Route::post('/store', ['as' => 'debt.store', 'uses' => 'DebtController@store']);
        Route::get('{id}/edit',   ['as' => 'debt.edit', 'uses' => 'DebtController@edit']);
        Route::post('/update', ['as' => 'debt.update', 'uses' => 'DebtController@update']);
        Route::get('{id}/destroy', ['as' => 'debt.destroy', 'uses' => 'DebtController@destroy']);
        Route::get('/change-value', ['as' => 'debt.change-value-by-column', 'uses' => 'DebtController@changeValueByColumn']);
    });
    Route::group(['prefix' => 'drivers'], function () {
        Route::get('/', ['as' => 'drivers.index', 'uses' => 'DriversController@index']);
        Route::get('/create', ['as' => 'drivers.create', 'uses' => 'DriversController@create']);
        Route::post('/store', ['as' => 'drivers.store', 'uses' => 'DriversController@store']);
        Route::get('{id}/edit',   ['as' => 'drivers.edit', 'uses' => 'DriversController@edit']);
        Route::post('/update', ['as' => 'drivers.update', 'uses' => 'DriversController@update']);
        Route::get('{id}/destroy', ['as' => 'drivers.destroy', 'uses' => 'DriversController@destroy']);
        Route::get('/change-value', ['as' => 'drivers.change-value-by-column', 'uses' => 'DriversController@changeValueByColumn']);
    });
    Route::group(['prefix' => 'coupon-code'], function () {
        Route::get('/', ['as' => 'coupon-code.index', 'uses' => 'CouponCodeController@index']);
        Route::get('/oto', ['as' => 'coupon-code.index-oto', 'uses' => 'CouponCodeController@indexOto']);
        Route::get('/create', ['as' => 'coupon-code.create', 'uses' => 'CouponCodeController@create']);
        Route::post('/store', ['as' => 'coupon-code.store', 'uses' => 'CouponCodeController@store']);
        Route::get('/ajax-store', ['as' => 'coupon-code.ajax-store', 'uses' => 'CouponCodeController@ajaxStore']);
        Route::get('{id}/edit',   ['as' => 'coupon-code.edit', 'uses' => 'CouponCodeController@edit']);
        Route::post('/update', ['as' => 'coupon-code.update', 'uses' => 'CouponCodeController@update']);
        Route::get('{id}/destroy', ['as' => 'coupon-code.destroy', 'uses' => 'CouponCodeController@destroy']);
        Route::get('/change-value', ['as' => 'coupon-code.change-value', 'uses' => 'CouponCodeController@changeValue']);
    });
    Route::group(['prefix' => 'location'], function () {
        Route::get('/', ['as' => 'location.index', 'uses' => 'LocationController@index']);
        Route::get('/save-toa-do', ['as' => 'location.save-toa-do', 'uses' => 'LocationController@saveToaDo']);
        Route::get('/ajax-delete', ['as' => 'location.ajax-delete', 'uses' => 'LocationController@ajaxDelete']);
        Route::get('/save-name', ['as' => 'location.save-name', 'uses' => 'LocationController@saveName']);
        Route::get('/delete-multi', ['as' => 'location.delete-multi', 'uses' => 'LocationController@deleteMulti']);
        Route::get('/create', ['as' => 'location.create', 'uses' => 'LocationController@create']);
        Route::post('/store', ['as' => 'location.store', 'uses' => 'LocationController@store']);
        Route::post('/ajaxSave', ['as' => 'location.ajax-save', 'uses' => 'LocationController@ajaxSave']);
        Route::get('/ajax-list', ['as' => 'location.ajax-list', 'uses' => 'LocationController@ajaxList']);
        Route::get('{id}/edit',   ['as' => 'location.edit', 'uses' => 'LocationController@edit']);
        Route::post('/update', ['as' => 'location.update', 'uses' => 'LocationController@update']);
        Route::get('{id}/destroy', ['as' => 'location.destroy', 'uses' => 'LocationController@destroy']);
        Route::get('/save-value-column', ['as' => 'location.save-value-column', 'uses' => 'LocationController@saveValueColumn']);
        Route::get('/update-lat-lng', ['as' => 'location.update-lat-lng', 'uses' => 'LocationController@updateLatLng']);
        Route::get('/change-value-by-column', ['as' => 'location.change-value-by-column', 'uses' => 'LocationController@changeValueByColumn']);

        Route::get('/set-distance', ['as' => 'set-distance', 'uses' => 'LocationController@setDistance']);
    });

    Route::group(['prefix' => 'bank-info'], function () {
        Route::get('/', ['as' => 'bank-info.index', 'uses' => 'BankInfoController@index']);
        Route::get('{id}/edit',   ['as' => 'bank-info.edit', 'uses' => 'BankInfoController@edit']);
        Route::post('/ajaxSave', ['as' => 'bank-info.ajax-save', 'uses' => 'BankInfoController@ajaxSave']);
        Route::get('/ajax-list', ['as' => 'bank-info.ajax-list', 'uses' => 'BankInfoController@ajaxList']);

        Route::post('/update', ['as' => 'bank-info.update', 'uses' => 'BankInfoController@update']);
    });
    Route::group(['prefix' => 'ticket'], function () {
        Route::get('/manage', ['as' => 'ticket.manage', 'uses' => 'TicketController@manage']);
        Route::get('{id}/edit',   ['as' => 'ticket.edit', 'uses' => 'TicketController@edit']);
        Route::get('{id}/view-pdf',   ['as' => 'ticket.view-pdf', 'uses' => 'TicketController@viewPdf']);
        Route::post('/update', ['as' => 'ticket.update', 'uses' => 'BookingController@updateTicket']);
    });
    Route::group(['prefix' => 'notification'], function () {
        Route::get('/', ['as' => 'noti.index', 'uses' => 'NotiController@index']);
        Route::get('/update-noti', ['as' => 'noti.update-multi', 'uses' => 'NotiController@updateMulti']);
        Route::get('{id}/edit',   ['as' => 'noti.edit', 'uses' => 'NotiController@edit']);
        Route::get('{id}/read',   ['as' => 'noti.read', 'uses' => 'NotiController@read']);
        Route::get('{id}/detail',   ['as' => 'noti.detail', 'uses' => 'NotiController@detail']);
        Route::post('/noti-read-all',   ['as' => 'noti.noti-read-all', 'uses' => 'NotiController@readAllNoti']);
    });
    Route::group(['prefix' => 'ticket-type-system'], function () {
        Route::get('/', ['as' => 'ticket-type-system.index', 'uses' => 'TicketTypeSystemController@index']);
        Route::get('{id}/edit',   ['as' => 'ticket-type-system.edit', 'uses' => 'TicketTypeSystemController@edit']);
        Route::post('/update', ['as' => 'ticket-type-system.update', 'uses' => 'TicketTypeSystemController@update']);
        Route::get('/create',   ['as' => 'ticket-type-system.create', 'uses' => 'TicketTypeSystemController@create']);
        Route::post('/store',   ['as' => 'ticket-type-system.store', 'uses' => 'TicketTypeSystemController@store']);
    });
    Route::group(['prefix' => 'hotel'], function () {
        Route::get('/', ['as' => 'hotel.index', 'uses' => 'HotelController@index']);
        Route::get('/create', ['as' => 'hotel.create', 'uses' => 'HotelController@create']);
        Route::post('/store', ['as' => 'hotel.store', 'uses' => 'HotelController@store']);
        Route::get('{id}/edit',   ['as' => 'hotel.edit', 'uses' => 'HotelController@edit']);
        Route::post('/update', ['as' => 'hotel.update', 'uses' => 'HotelController@update']);
        Route::get('{id}/destroy', ['as' => 'hotel.destroy', 'uses' => 'HotelController@destroy']);
    });
    Route::group(['prefix' => 'room'], function () {
        Route::get('/', ['as' => 'room.index', 'uses' => 'RoomController@index']);
        Route::get('/create', ['as' => 'room.create', 'uses' => 'RoomController@create']);
        Route::post('/store', ['as' => 'room.store', 'uses' => 'RoomController@store']);
        Route::get('{id}/edit',   ['as' => 'room.edit', 'uses' => 'RoomController@edit']);
        Route::get('{id}/price',   ['as' => 'room.price', 'uses' => 'RoomController@price']);
        Route::post('/store-price', ['as' => 'room.store-price', 'uses' => 'RoomController@storePrice']);
        Route::post('/store-price-new', ['as' => 'room.store-price-new', 'uses' => 'RoomController@storePrice']);
        Route::post('/update', ['as' => 'room.update', 'uses' => 'RoomController@update']);
        Route::get('{id}/destroy', ['as' => 'room.destroy', 'uses' => 'RoomController@destroy']);
    });
    Route::group(['prefix' => 'export'], function () {
        Route::get('/cong-no-tour', ['as' => 'export.cong-no-tour', 'uses' => 'ExportController@congNoTour']);
        Route::get('/gui-tour', ['as' => 'export.gui-tour', 'uses' => 'ExportController@exportGui']);
        Route::get('/cong-no-hotel', ['as' => 'export.cong-no-hotel', 'uses' => 'ExportController@congNoHotel']);
    });
    Route::group(['prefix' => 'report'], function () {
        Route::get('/customer-by-level', ['as' => 'report.customer-by-level', 'uses' => 'ReportController@customerByLevel']);
        Route::get('/general', ['as' => 'report.general', 'uses' => 'ReportController@general']);
        Route::get('/detail-by-type', ['as' => 'report.detail-by-type', 'uses' => 'ReportController@detailByType']);
        Route::get('/ve-cap-treo', ['as' => 'report.ve-cap-treo', 'uses' => 'ReportController@veCapTreoTheoThang']);
        Route::get('/phan-an', ['as' => 'report.phan-an', 'uses' => 'ReportController@phanAnThang']);
        Route::get('/customer-by-level-month', ['as' => 'report.customer-by-level-and-month', 'uses' => 'ReportController@customerByLevelAndMonth']);
        Route::get('/hotel-recent', ['as' => 'report.hotel-recent', 'uses' => 'ReportController@hotelRecent']);
        Route::get('/hotel-by-user', ['as' => 'report.hotel-by-user', 'uses' => 'ReportController@hotelByUser']);
        Route::get('/doanh-thu-thang', ['as' => 'report.doanh-thu-thang', 'uses' => 'ReportController@doanhthuthang']);
        Route::get('/loi-nhuan-thang', ['as' => 'report.loi-nhuan-thang', 'uses' => 'ReportController@loinhuanthang']);
        Route::get('/doanh-so-doi-tac', ['as' => 'report.ds-doi-tac', 'uses' => 'ReportController@dsDoitac']);
        Route::get('/cano', ['as' => 'report.cano', 'uses' => 'ReportController@cano']);
        Route::get('/car', ['as' => 'report.car', 'uses' => 'ReportController@car']);
        Route::get('/cano-detail', ['as' => 'report.cano-detail', 'uses' => 'ReportController@canoDetail']);
        Route::get('/ajax-detail-cost', ['as' => 'report.ajax-detail-cost', 'uses' => 'ReportController@detailCostByPartner']);
        Route::get('/ben', ['as' => 'report.ben', 'uses' => 'ReportController@ben']);
        Route::get('/ajax-search-ben', ['as' => 'report.ajax-search-ben', 'uses' => 'ReportController@ajaxSearchBen']);
        Route::get('/thu-tien', ['as' => 'report.thu-tien', 'uses' => 'ReportController@thuTien']);
        Route::get('/doi-tac-theo-nam', ['as' => 'report.doi-tac-theo-nam', 'uses' => 'ReportController@yearDoiTac']);
        Route::get('/customer', ['as' => 'report.customer', 'uses' => 'ReportController@customer']);
        Route::get('/weekly', ['as' => 'report.weekly', 'uses' => 'ReportController@weeklyReport']);
        Route::get('/average-guest-by-level', ['as' => 'report.average-guest-by-level', 'uses' => 'ReportController@averageGuestByLevel']);
    });

    Route::group(['prefix' => 'booking'], function () {
        Route::get('/', ['as' => 'booking.index', 'uses' => 'BookingController@index']);
        Route::get('/update-cost', ['as' => 'booking.update-cost', 'uses' => 'BookingController@updateCost']);
        
        Route::get('/fast-search', ['as' => 'booking.fast-search', 'uses' => 'BookingController@fastSearch']);
        Route::get('/export', ['as' => 'booking.export', 'uses' => 'BookingController@export']);
        Route::get('/not-export', ['as' => 'booking.not-export', 'uses' => 'BookingController@notExport']);
        Route::get('/change-export-status', ['as' => 'change-export-status', 'uses' => 'BookingController@changeExport']);
        Route::get('/change-status', ['as' => 'change-status', 'uses' => 'BookingController@changeStatus']);
        Route::get('/change-value', ['as' => 'booking.change-value-by-column', 'uses' => 'BookingController@changeValueByColumn']);
        Route::get('/create', ['as' => 'booking.create', 'uses' => 'BookingController@create']);
        Route::get('/create-short', ['as' => 'booking.create-short', 'uses' => 'BookingController@createShort']);
        Route::post('/create-note', ['as' => 'booking.create-note', 'uses' => 'BookingController@createNotes']);

        Route::get('/get-info', ['as' => 'booking.info', 'uses' => 'BookingController@info']);
        Route::get('/save-info', ['as' => 'booking.save-info', 'uses' => 'BookingController@saveInfo']);
        Route::post('/store', ['as' => 'booking.store', 'uses' => 'BookingController@store']);
        Route::post('/storeShort', ['as' => 'booking.store-short', 'uses' => 'BookingController@storeShort']);
        Route::post('/store-hotels', ['as' => 'booking.store-hotels', 'uses' => 'BookingController@storeHotel']);
        Route::post('/store-car', ['as' => 'booking.store-car', 'uses' => 'BookingController@storeCar']);
        Route::post('/update-hotels', ['as' => 'booking.update-hotels', 'uses' => 'BookingController@updateHotel']);
        Route::post('/update-car', ['as' => 'booking.update-car', 'uses' => 'BookingController@updateCar']);
        Route::get('{id}/edit',   ['as' => 'booking.edit', 'uses' => 'BookingController@edit']);
        Route::get('{id}/create-general',   ['as' => 'booking.create-general', 'uses' => 'BookingController@createGeneral']);

        Route::get('{id}/history',   ['as' => 'history.booking', 'uses' => 'HistoryController@booking']);
        Route::post('/update', ['as' => 'booking.update', 'uses' => 'BookingController@update']);
        Route::get('{id}/destroy', ['as' => 'booking.destroy', 'uses' => 'BookingController@destroy']);
        Route::post('/store-cam', ['as' => 'booking.store-cam', 'uses' => 'BookingController@storeCam']);
        Route::post('/update-cam', ['as' => 'booking.update-cam', 'uses' => 'BookingController@updateCam']);
        Route::get('/check-error', ['as' => 'booking.checkError', 'uses' => 'BookingController@checkError']);
        Route::get('/check-unc', ['as' => 'booking.check-unc', 'uses' => 'BookingController@checkUnc']);
        Route::get('/export-customer', ['as' => 'booking.export-customer', 'uses' => 'ExportController@customerTour']);
        Route::get('/maps', ['as' => 'booking.maps', 'uses' => 'BookingController@maps']);
        Route::get('/xe-free', ['as' => 'booking.xe-free', 'uses' => 'BookingController@xeFree']);
        Route::post('/store-xe-free', ['as' => 'booking.store-xe-free', 'uses' => 'BookingController@storeXeFree']);
        Route::get('/list-xe-free', ['as' => 'booking.list-xe-free', 'uses' => 'BookingController@listXeFree']);
        Route::get('/get-confirm-nop', ['as' => 'booking.get-confirm-nop', 'uses' => 'BookingController@getConfirmNop']);
        Route::get('/get-content-nop', ['as' => 'booking.get-content-nop', 'uses' => 'BookingController@getContentNop']);
        Route::get('/get-content-nop-dt', ['as' => 'booking.get-content-nop-dt', 'uses' => 'BookingController@getContentNopDoiTac']);
        Route::get('/get-confirm-chi', ['as' => 'booking.get-confirm-chi', 'uses' => 'BookingController@getConfirmChi']);
        Route::get('/get-content-chi', ['as' => 'booking.get-content-chi', 'uses' => 'BookingController@getContentChi']);
    });
    Route::group(['prefix' => 'booking-hotel'], function () {
        Route::get('/', ['as' => 'booking-hotel.index', 'uses' => 'BookingHotelController@index']);
        Route::get('/acc', ['as' => 'booking-hotel.acc', 'uses' => 'BookingHotelController@acc']);
        Route::get('/create', ['as' => 'booking-hotel.create', 'uses' => 'BookingHotelController@create']);
        Route::post('/store', ['as' => 'booking-hotel.store', 'uses' => 'BookingHotelController@store']);
        Route::post('/update', ['as' => 'booking-hotel.update', 'uses' => 'BookingHotelController@update']);
        Route::get('{id}/edit',   ['as' => 'booking-hotel.edit', 'uses' => 'BookingHotelController@edit']);
        Route::get('{id}/destroy', ['as' => 'booking-hotel.destroy', 'uses' => 'BookingHotelController@destroy']);
        Route::get('/related', ['as' => 'booking-hotel.related', 'uses' => 'BookingHotelController@related']);
        Route::get('/ajax-room-list', ['as' => 'booking-hotel.ajax-room-list', 'uses' => 'BookingHotelController@ajaxRoomList']);
        Route::get('/ajax-get-price', ['as' => 'booking-hotel.ajax-get-price', 'uses' => 'BookingHotelController@ajaxGetRoomPrices']);
        Route::get('/check-payment', ['as' => 'booking-hotel.check-payment', 'uses' => 'BookingHotelController@checkPayment']);
        Route::get('/saveBookingCode', 'BookingHotelController@saveBookingCode')->name('saveBookingCode');
    });
    Route::group(['prefix' => 'booking-ticket'], function () {
        Route::get('/', ['as' => 'booking-ticket.index', 'uses' => 'BookingTicketController@index']);
        Route::get('/create', ['as' => 'booking-ticket.create', 'uses' => 'BookingTicketController@create']);
        Route::post('/store', ['as' => 'booking-ticket.store', 'uses' => 'BookingTicketController@store']);
        Route::post('/update', ['as' => 'booking-ticket.update', 'uses' => 'BookingTicketController@update']);
        Route::get('{id}/edit',   ['as' => 'booking-ticket.edit', 'uses' => 'BookingTicketController@edit']);
        Route::get('{id}/destroy', ['as' => 'booking-ticket.destroy', 'uses' => 'BookingTicketController@destroy']);
    });
    Route::group(['prefix' => 'booking-xe-free'], function () {
        Route::get('/', ['as' => 'booking-xe-free.index', 'uses' => 'BookingXeFreeController@index']);
        Route::get('/create', ['as' => 'booking-xe-free.create', 'uses' => 'BookingXeFreeController@create']);
        Route::post('/store', ['as' => 'booking-xe-free.store', 'uses' => 'BookingXeFreeController@store']);
        Route::post('/update', ['as' => 'booking-xe-free.update', 'uses' => 'BookingXeFreeController@update']);
        Route::get('{id}/edit',   ['as' => 'booking-xe-free.edit', 'uses' => 'BookingXeFreeController@edit']);
        Route::get('{id}/destroy', ['as' => 'booking-xe-free.destroy', 'uses' => 'BookingXeFreeController@destroy']);
    });
    Route::group(['prefix' => 'booking-camera'], function () {
        Route::get('/', ['as' => 'booking-camera.index', 'uses' => 'BookingCameraController@index']);
        Route::get('/create', ['as' => 'booking-camera.create', 'uses' => 'BookingCameraController@create']);
        Route::post('/store', ['as' => 'booking-camera.store', 'uses' => 'BookingCameraController@store']);
        Route::post('/update', ['as' => 'booking-camera.update', 'uses' => 'BookingCameraController@update']);
        Route::get('{id}/edit',   ['as' => 'booking-camera.edit', 'uses' => 'BookingCameraController@edit']);
        Route::get('{id}/destroy', ['as' => 'booking-camera.destroy', 'uses' => 'BookingCameraController@destroy']);
    });
    Route::group(['prefix' => 'booking-car'], function () {
        Route::get('/', ['as' => 'booking-car.index', 'uses' => 'BookingCarController@index']);
        Route::get('/create', ['as' => 'booking-car.create', 'uses' => 'BookingCarController@create']);
        Route::post('/store', ['as' => 'booking-car.store', 'uses' => 'BookingCarController@store']);
        Route::post('/update', ['as' => 'booking-car.update', 'uses' => 'BookingCarController@update']);
        Route::get('{id}/edit',   ['as' => 'booking-car.edit', 'uses' => 'BookingCarController@edit']);
        Route::get('{id}/destroy', ['as' => 'booking-car.destroy', 'uses' => 'BookingCarController@destroy']);
        Route::get('/calendar', ['as' => 'booking-car.calendar', 'uses' => 'BookingCarController@calendar']);
    });
    Route::group(['prefix' => 'booking-tu-lai'], function () {
        Route::get('/', ['as' => 'booking-tu-lai.index', 'uses' => 'BookingTuLaiController@index']);
        Route::get('/create', ['as' => 'booking-tu-lai.create', 'uses' => 'BookingTuLaiController@create']);
        Route::post('/store', ['as' => 'booking-tu-lai.store', 'uses' => 'BookingTuLaiController@store']);
        Route::post('/update', ['as' => 'booking-tu-lai.update', 'uses' => 'BookingTuLaiController@update']);
        Route::get('{id}/edit',   ['as' => 'booking-tu-lai.edit', 'uses' => 'BookingTuLaiController@edit']);
        Route::get('{id}/destroy', ['as' => 'booking-tu-lai.destroy', 'uses' => 'BookingTuLaiController@destroy']);
    });
    Route::group(['prefix' => 'booking-xe-may'], function () {
        Route::get('/', ['as' => 'booking-xe-may.index', 'uses' => 'BookingXeMayController@index']);
        Route::get('/create', ['as' => 'booking-xe-may.create', 'uses' => 'BookingXeMayController@create']);
        Route::post('/store', ['as' => 'booking-xe-may.store', 'uses' => 'BookingXeMayController@store']);
        Route::post('/update', ['as' => 'booking-xe-may.update', 'uses' => 'BookingXeMayController@update']);
        Route::get('{id}/edit',   ['as' => 'booking-xe-may.edit', 'uses' => 'BookingXeMayController@edit']);
        Route::get('{id}/destroy', ['as' => 'booking-xe-may.destroy', 'uses' => 'BookingXeMayController@destroy']);
    });
    Route::group(['prefix' => 'cost-payment'], function () {
        Route::get('/', ['as' => 'cost-payment.index', 'uses' => 'CostPaymentController@index']);
        Route::get('/create', ['as' => 'cost-payment.create', 'uses' => 'CostPaymentController@create']);
        Route::post('/store', ['as' => 'cost-payment.store', 'uses' => 'CostPaymentController@store']);

        Route::get('{id}/edit',   ['as' => 'cost-payment.edit', 'uses' => 'CostPaymentController@edit']);
        Route::post('/update', ['as' => 'cost-payment.update', 'uses' => 'CostPaymentController@update']);
        Route::get('{id}/destroy', ['as' => 'cost-payment.destroy', 'uses' => 'CostPaymentController@destroy']);
    });
    Route::group(['prefix' => 'booking-payment'], function () {
        Route::get('/', ['as' => 'booking-payment.index', 'uses' => 'BookingPaymentController@index']);
        Route::get('/create', ['as' => 'booking-payment.create', 'uses' => 'BookingPaymentController@create']);
        Route::post('/store', ['as' => 'booking-payment.store', 'uses' => 'BookingPaymentController@store']);

        Route::get('{id}/edit',   ['as' => 'booking-payment.edit', 'uses' => 'BookingPaymentController@edit']);
        Route::post('/update', ['as' => 'booking-payment.update', 'uses' => 'BookingPaymentController@update']);
        Route::get('{id}/destroy', ['as' => 'booking-payment.destroy', 'uses' => 'BookingPaymentController@destroy']);
    });
    Route::group(['prefix' => 'booking-bill'], function () {
        Route::get('/', ['as' => 'booking-bill.index', 'uses' => 'BookingBillController@index']);
        Route::get('/create', ['as' => 'booking-bill.create', 'uses' => 'BookingBillController@create']);
        Route::post('/store', ['as' => 'booking-bill.store', 'uses' => 'BookingBillController@store']);

        Route::get('{id}/edit',   ['as' => 'booking-bill.edit', 'uses' => 'BookingBillController@edit']);
        Route::post('/update', ['as' => 'booking-bill.update', 'uses' => 'BookingBillController@update']);
        Route::get('{id}/destroy', ['as' => 'booking-bill.destroy', 'uses' => 'BookingBillController@destroy']);
    });
    Route::group(['prefix' => 'food'], function () {
        Route::get('/', ['as' => 'food.index', 'uses' => 'FoodController@index']);
        Route::get('/create', ['as' => 'food.create', 'uses' => 'FoodController@create']);
        Route::post('/store', ['as' => 'food.store', 'uses' => 'FoodController@store']);

        Route::get('{id}/edit',   ['as' => 'food.edit', 'uses' => 'FoodController@edit']);
        Route::post('/update', ['as' => 'food.update', 'uses' => 'FoodController@update']);
        Route::get('{id}/destroy', ['as' => 'food.destroy', 'uses' => 'FoodController@destroy']);
    });
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', ['as' => 'orders.index', 'uses' => 'OrdersController@index']);
        Route::get('/image', ['as' => 'orders.image', 'uses' => 'OrdersController@image']);
        Route::get('/create', ['as' => 'orders.create', 'uses' => 'OrdersController@create']);
        Route::post('/store', ['as' => 'orders.store', 'uses' => 'OrdersController@store']);

        Route::get('{id}/edit',   ['as' => 'orders.edit', 'uses' => 'OrdersController@edit']);
        Route::post('/update', ['as' => 'orders.update', 'uses' => 'OrdersController@update']);
        Route::get('{id}/destroy', ['as' => 'orders.destroy', 'uses' => 'OrdersController@destroy']);
    });
    Route::group(['prefix' => 'settings'], function () {
        Route::get('/', ['as' => 'settings.index', 'uses' => 'SettingsController@index']);

        Route::post('/store', ['as' => 'settings.store', 'uses' => 'SettingsController@store']);
        Route::post('/update', ['as' => 'settings.update', 'uses' => 'SettingsController@update']);
    });
    Route::group(['prefix' => 'cost'], function () {
        Route::get('/', ['as' => 'cost.index', 'uses' => 'CostController@index']);
        Route::get('/sms', ['as' => 'cost.sms', 'uses' => 'CostController@sms']);
        Route::get('/group-code', ['as' => 'cost.group-code', 'uses' => 'CostController@groupCodeChiTien']);
        Route::post('/parse-sms', ['as' => 'cost.parse-sms', 'uses' => 'CostController@parseSms']);

        Route::get('/get-confirm-ung', ['as' => 'cost.get-confirm-ung', 'uses' => 'CostController@getConfirmUng']);
        Route::get('/get-content-ung', ['as' => 'cost.get-content-ung', 'uses' => 'CostController@getContentUng']);
        Route::get('/get-confirm-chi', ['as' => 'cost.get-confirm-chi', 'uses' => 'CostController@getConfirmChi']);
        Route::get('/get-content-chi', ['as' => 'cost.get-content-chi', 'uses' => 'CostController@getContentChi']);
        Route::get('/cal', ['as' => 'cost.cal', 'uses' => 'CostController@cal']);
        Route::get('/export', ['as' => 'cost.export', 'uses' => 'CostController@export']);
        Route::get('/ajax-doi-tac', ['as' => 'cost.ajax-doi-tac', 'uses' => 'CostController@ajaxDoiTac']);
        Route::get('/image', ['as' => 'cost.image', 'uses' => 'CostController@image']);
        Route::get('/create', ['as' => 'cost.create', 'uses' => 'CostController@create']);
        Route::post('/store', ['as' => 'cost.store', 'uses' => 'CostController@store']);
        Route::get('{id}/edit',   ['as' => 'cost.edit', 'uses' => 'CostController@edit']);
        Route::get('{id}/copy',   ['as' => 'cost.copy', 'uses' => 'CostController@copy']);
        Route::post('/update', ['as' => 'cost.update', 'uses' => 'CostController@update']);
        Route::get('/viewQRCode', ['as' => 'cost.view-qr-code', 'uses' => 'CostController@viewQRCode']);
        Route::get('{id}/destroy', ['as' => 'cost.destroy', 'uses' => 'CostController@destroy']);
        Route::get('/change-value', ['as' => 'cost.change-value-by-column', 'uses' => 'CostController@changeValueByColumn']);
        Route::get('/ajax-cost-type', ['as' => 'cost.ajax-cost-type', 'uses' => 'CostController@ajaxGetCostType']);
    });
    Route::group(['prefix' => 'booking-vmb'], function () {
        Route::get('/', ['as' => 'booking-vmb.index', 'uses' => 'BookingVmbController@index']);
        Route::get('/create', ['as' => 'booking-vmb.create', 'uses' => 'BookingVmbController@create']);
        Route::post('/store', ['as' => 'booking-vmb.store', 'uses' => 'BookingVmbController@store']);
        Route::post('/update', ['as' => 'booking-vmb.update', 'uses' => 'BookingVmbController@update']);
        Route::get('{id}/edit',   ['as' => 'booking-vmb.edit', 'uses' => 'BookingVmbController@edit']);
        Route::get('{id}/destroy', ['as' => 'booking-vmb.destroy', 'uses' => 'BookingVmbController@destroy']);
    });
    Route::group(['prefix' => 'grandworld-schedule'], function () {
        Route::get('/', ['as' => 'grandworld-schedule.index', 'uses' => 'GrandworldScheduleController@index']);
    });
    Route::post('/change-value', ['as' => 'change-value', 'uses' => 'GeneralController@changeValue']);
    Route::get('/set-price', ['as' => 'set-price', 'uses' => 'HotelController@price']);
    Route::get('w-text', ['as' => 'w-text.index', 'uses' => "WSettingsController@text"]);
    Route::post('w-save-text', ['as' => 'w-text.save', 'uses' => "WSettingsController@saveText"]);
    Route::get('dashboard', ['as' => 'dashboard.index', 'uses' => "HomeController@dashboard"]);
    Route::post('save-content', ['as' => 'save-content', 'uses' => "WSettingsController@saveContent"]);

    Route::get('daily-report', ['as' => 'daily-report.index', 'uses' => "DailyReportController@index"]);
    Route::get('daily-report-hotel', ['as' => 'daily-report.hotel', 'uses' => "DailyReportController@hotel"]);

    Route::post('/tmp-upload', ['as' => 'image.tmp-upload', 'uses' => 'UploadController@tmpUpload']);
    Route::post('/tmp-upload-multiple', ['as' => 'image.tmp-upload-multiple', 'uses' => 'UploadController@tmpUploadMultiple']);

    Route::post('/update-order', ['as' => 'update-order', 'uses' => 'GeneralController@updateOrder']);
    Route::post('/ck-upload', ['as' => 'ck-upload', 'uses' => 'UploadController@ckUpload']);
    Route::post('/get-slug', ['as' => 'get-slug', 'uses' => 'GeneralController@getSlug']);

    Route::group(['prefix' => 'package'], function () {
        Route::get('/', ['as' => 'package.index', 'uses' => 'PackageController@index']);
        Route::get('/create', ['as' => 'package.create', 'uses' => 'PackageController@create']);
        Route::post('/store', ['as' => 'package.store', 'uses' => 'PackageController@store']);
        Route::get('{id}/edit',   ['as' => 'package.edit', 'uses' => 'PackageController@edit']);
        Route::post('/update', ['as' => 'package.update', 'uses' => 'PackageController@update']);
        Route::get('{id}/destroy', ['as' => 'package.destroy', 'uses' => 'PackageController@destroy']);
    });
    Route::group(['prefix' => 'account'], function () {
        Route::get('/', ['as' => 'account.index', 'uses' => 'AccountController@index']);
        Route::get('/doi-tac', ['as' => 'account.doi-tac', 'uses' => 'AccountController@doitac']);
        Route::get('/change-password', ['as' => 'account.change-pass', 'uses' => 'AccountController@changePass']);
        Route::post('/store-password', ['as' => 'account.store-pass', 'uses' => 'AccountController@storeNewPass']);
        Route::get('{id}/kpi', ['as' => 'account.kpi', 'uses' => 'AccountController@kpi']);
        Route::post('/store-kpi', ['as' => 'account.store-kpi', 'uses' => 'AccountController@storeKpi']);
        Route::get('/update-status/{status}/{id}', ['as' => 'account.update-status', 'uses' => 'AccountController@updateStatus']);
        Route::get('/create', ['as' => 'account.create', 'uses' => 'AccountController@create']);
        Route::get('/create-tx', ['as' => 'account.create-tx', 'uses' => 'AccountController@createTx']);
        Route::get('/create-dt', ['as' => 'account.create-dt', 'uses' => 'AccountController@createDt']);
        Route::post('/store', ['as' => 'account.store', 'uses' => 'AccountController@store']);
        Route::post('/store-tx', ['as' => 'account.store-tx', 'uses' => 'AccountController@storeTx']);
        Route::get('{id}/edit',   ['as' => 'account.edit', 'uses' => 'AccountController@edit']);
        Route::post('/update', ['as' => 'account.update', 'uses' => 'AccountController@update']);
        Route::get('{id}/destroy', ['as' => 'account.destroy', 'uses' => 'AccountController@destroy']);
        Route::post('/ajaxSave', ['as' => 'account.ajax-save', 'uses' => 'AccountController@ajaxSave']);
        Route::get('/ajax-list', ['as' => 'account.ajax-list', 'uses' => 'AccountController@ajaxList']);
    });
    Route::group(['prefix' => 'articles'], function () {
        Route::get('/', ['as' => 'articles.index', 'uses' => 'ArticlesController@index']);
        Route::get('/create', ['as' => 'articles.create', 'uses' => 'ArticlesController@create']);
        Route::post('/store', ['as' => 'articles.store', 'uses' => 'ArticlesController@store']);
        Route::get('{id}/edit',   ['as' => 'articles.edit', 'uses' => 'ArticlesController@edit']);
        Route::post('/update', ['as' => 'articles.update', 'uses' => 'ArticlesController@update']);
        Route::get('{id}/destroy', ['as' => 'articles.destroy', 'uses' => 'ArticlesController@destroy']);
    });
    Route::group(['prefix' => 'customer'], function () {
        Route::get('/', ['as' => 'customer.index', 'uses' => 'CustomerController@index']);
        Route::get('/create', ['as' => 'customer.create', 'uses' => 'CustomerController@create']);
        Route::post('/store', ['as' => 'customer.store', 'uses' => 'CustomerController@store']);
        Route::get('{id}/edit',   ['as' => 'customer.edit', 'uses' => 'CustomerController@edit']);
        Route::get('/hen',   ['as' => 'customer.hen', 'uses' => 'CustomerController@hen']);
        Route::post('/update', ['as' => 'customer.update', 'uses' => 'CustomerController@update']);
        Route::post('/save-hen', ['as' => 'customer.save-hen', 'uses' => 'CustomerController@saveHen']);
        Route::get('{id}/destroy', ['as' => 'customer.destroy', 'uses' => 'CustomerController@destroy']);
        Route::get('/noti',   ['as' => 'customer.noti', 'uses' => 'CustomerController@getListNoti']);
        Route::get('/create-booking',   ['as' => 'customer.create-booking', 'uses' => 'CustomerController@createBooking']);
        Route::get('/get-product',   ['as' => 'customer.get-product', 'uses' => 'CustomerController@getProduct']);

        Route::get('/update-status/{status}/{id}', ['as' => 'customer.update-status', 'uses' => 'CustomerController@updateStatus']);
        Route::get('/export',   ['as' => 'customer.export', 'uses' => 'CustomerController@export']);
    });

    Route::group(['prefix' => 'staff'], function () {
        Route::get('/', ['as' => 'staff.index', 'uses' => 'StaffController@index']);
        Route::get('{id}/edit',   ['as' => 'staff.edit', 'uses' => 'StaffController@edit']);
        Route::get('/create', ['as' => 'staff.create', 'uses' => 'StaffController@create']);
        Route::post('/store', ['as' => 'staff.store', 'uses' => 'StaffController@store']);
        Route::post('/update', ['as' => 'staff.update', 'uses' => 'StaffController@update']);
        Route::get('{id}/destroy', ['as' => 'staff.destroy', 'uses' => 'StaffController@destroy']);
        Route::get('/update-status/{status}/{id}', ['as' => 'custaffstomer.update-status', 'uses' => 'StaffController@updateStatus']);
        Route::get('/export',   ['as' => 'staff.export', 'uses' => 'StaffController@export']);
        Route::get('{id}/reset-pass', ['uses' => 'StaffController@editPass', 'as' => 'staff.editPass']);
        Route::get('modal-staff', ['uses' => 'StaffController@getModalStaff', 'as' => 'staff.getModal']);
    });
    Route::group(['prefix' => 'ctv'], function () {
        Route::get('/', ['as' => 'ctv.index', 'uses' => 'CtvController@index']);
        Route::get('{id}/destroy', ['as' => 'ctv.destroy', 'uses' => 'CtvController@destroy']);
        Route::get('{id}/edit',   ['as' => 'ctv.edit', 'uses' => 'CtvController@edit']);
        Route::get('/create', ['as' => 'ctv.create', 'uses' => 'CtvController@create']);
        Route::post('/store', ['as' => 'ctv.store', 'uses' => 'CtvController@store']);
        Route::post('/update', ['as' => 'ctv.update', 'uses' => 'CtvController@update']);

        Route::get('/update-status/{status}/{id}', ['as' => 'ctv.update-status', 'uses' => 'CtvController@updateStatus']);
        Route::get('/export',   ['as' => 'staff.export', 'uses' => 'CtvController@export']);
        Route::get('{id}/reset-pass', ['uses' => 'CtvController@editPass', 'as' => 'ctv.editPass']);
        Route::get('modal-staff', ['uses' => 'CtvController@getModalStaff', 'as' => 'ctv.getModal']);
    });

    Route::group(['prefix' => 'payment-request'], function () {
        Route::get('/', ['as' => 'payment-request.index', 'uses' => 'PaymentRequestController@index']);
        Route::get('/urgent', ['as' => 'payment-request.urgent', 'uses' => 'PaymentRequestController@urgent']);
        Route::get('/diem-danh', ['as' => 'payment-request.diem-danh', 'uses' => 'PaymentRequestController@diemDanh']);
        Route::get('/create', ['as' => 'payment-request.create', 'uses' => 'PaymentRequestController@create']);
        Route::post('/store', ['as' => 'payment-request.store', 'uses' => 'PaymentRequestController@store']);
        Route::get('/ajax-store', ['as' => 'payment-request.ajax-store', 'uses' => 'PaymentRequestController@ajaxStore']);

        Route::get('{id}/edit',   ['as' => 'payment-request.edit', 'uses' => 'PaymentRequestController@edit']);
        Route::post('/update', ['as' => 'payment-request.update', 'uses' => 'PaymentRequestController@update']);
        Route::get('{id}/destroy', ['as' => 'payment-request.destroy', 'uses' => 'PaymentRequestController@destroy']);
        Route::get('/change-value', ['as' => 'payment-request.change-value-by-column', 'uses' => 'PaymentRequestController@changeValueByColumn']);
        Route::get('/export',   ['as' => 'payment-request.export', 'uses' => 'PaymentRequestController@export']);
        Route::get('/get-confirm-ung', ['as' => 'payment-request.get-confirm-ung', 'uses' => 'PaymentRequestController@getConfirmUng']);
        Route::get('/get-content-ung', ['as' => 'payment-request.get-content-ung', 'uses' => 'PaymentRequestController@getContentUng']);
        Route::get('/get-confirm-chi', ['as' => 'payment-request.get-confirm-chi', 'uses' => 'PaymentRequestController@getConfirmChi']);
        Route::get('/get-content-chi', ['as' => 'payment-request.get-content-chi', 'uses' => 'PaymentRequestController@getContentChi']);
        Route::get('/viewQRCode', ['as' => 'payment-request.view-qr-code', 'uses' => 'PaymentRequestController@viewQRCode']);
    });

    Route::group(['prefix' => 'task'], function () {
        Route::get('/reports', ['as' => 'task.reports', 'uses' => 'TaskController@report']);
        Route::get('/calendar', ['as' => 'task.calendar', 'uses' => 'TaskController@calendar']);

        Route::get('/', ['as' => 'task.index', 'uses' => 'TaskController@index']);
        Route::get('/create', ['as' => 'task.create', 'uses' => 'TaskController@create']);
        Route::post('/store', ['as' => 'task.store', 'uses' => 'TaskController@store']);
        Route::get('{id}',   ['as' => 'task.edit', 'uses' => 'TaskController@show']);
        Route::get('{id}/edit',   ['as' => 'task.edit', 'uses' => 'TaskController@edit']);
        Route::post('{id}/update', ['as' => 'task.update', 'uses' => 'TaskController@update']);
        Route::post('{id}/status', ['as' => 'task.update', 'uses' => 'TaskController@ajaxUpdateTaskStatus']);
        Route::get('{id}/destroy', ['as' => 'task.destroy', 'uses' => 'TaskController@destroy']);
        Route::get('{id}/delete', ['as' => 'task.delete', 'uses' => 'TaskController@delete']);
        Route::post('/ajaxSave', ['as' => 'task.ajax-save', 'uses' => 'TaskController@ajaxSave']);
        Route::get('calendar/ajax-list', ['as' => 'task.ajax-list', 'uses' => 'TaskController@ajaxList']);

        Route::get('/{id}/logs', ['as' => 'task-log.show', 'uses' => 'TaskLogController@index']);
        Route::post('/{id}/logs', ['as' => 'task-log.store', 'uses' => 'TaskLogController@store']);
        Route::get('/{id}/logs/{logId}', ['as' => 'task-log.edit', 'uses' => 'TaskLogController@edit']);
        Route::post('/{id}/logs/{logId}', ['as' => 'task-log.update', 'uses' => 'TaskLogController@update']);
        Route::post('/{id}/logs/{logId}/destroy', ['as' => 'task-log.update', 'uses' => 'TaskLogController@destroy']);

        // Route::get('/{taskId}/todos', ['as' => 'todo.show', 'uses' => 'TaskDetailController@index']);
        Route::get('/{taskId}/todos/create', ['as' => 'todo.create', 'uses' => 'TaskDetailController@create']);
        Route::post('/{taskId}/todos/store', ['as' => 'todo.store', 'uses' => 'TaskDetailController@store']);
        Route::post('/{taskId}/todos/{todoId}/update', ['as' => 'todo.update', 'uses' => 'TaskDetailController@update']);
    });

    Route::group(['prefix' => 'plan'], function () {
        Route::get('/', ['as' => 'plan.index', 'uses' => 'PlanController@index']);
        Route::get('/create', ['as' => 'plan.create', 'uses' => 'PlanController@create']);
        Route::post('/store', ['as' => 'plan.store', 'uses' => 'PlanController@store']);
        Route::get('{id}/edit',   ['as' => 'plan.edit', 'uses' => 'PlanController@edit']);
        Route::post('/update', ['as' => 'plan.update', 'uses' => 'PlanController@update']);
        Route::get('{id}/destroy', ['as' => 'plan.destroy', 'uses' => 'PlanController@destroy']);
    });

    Route::group(['prefix' => 'combo'], function () {
        Route::get('/', ['as' => 'combo.index', 'uses' => 'ComboController@index']);
        Route::get('/create', ['as' => 'combo.create', 'uses' => 'ComboController@create']);
        Route::post('/store', ['as' => 'combo.store', 'uses' => 'ComboController@store']);
        Route::get('{id}/edit',   ['as' => 'combo.edit', 'uses' => 'ComboController@edit']);
        Route::post('/update', ['as' => 'combo.update', 'uses' => 'ComboController@update']);
        Route::get('{id}/destroy', ['as' => 'combo.destroy', 'uses' => 'ComboController@destroy']);
    });
    Route::group(['prefix' => 'booking-car'], function () {
        Route::get('/', ['as' => 'booking-car.index', 'uses' => 'BookingCarController@index']);
    });
    Route::group(['prefix' => 'customer-care'], function () {
        Route::get('/', ['as' => 'customer-care.index', 'uses' => 'CustomerCareController@index']);
        Route::get('/create', ['as' => 'customer-care.create', 'uses' => 'CustomerCareController@create']);
        Route::post('/store', ['as' => 'customer-care.store', 'uses' => 'CustomerCareController@store']);
        Route::get('{id}/edit',   ['as' => 'customer-care.edit', 'uses' => 'CustomerCareController@edit']);
        Route::post('/update', ['as' => 'customer-care.update', 'uses' => 'CustomerCareController@update']);
        Route::get('{id}/destroy', ['as' => 'customer-care.destroy', 'uses' => 'CustomerCareController@destroy']);
    });

    Route::group(['prefix' => 'restaurants'], function () {
        Route::get('/', ['as' => 'restaurants.index', 'uses' => 'RestaurantController@index']);
        Route::get('/create', ['as' => 'restaurants.create', 'uses' => 'RestaurantController@create']);
        Route::post('/store', ['as' => 'restaurants.store', 'uses' => 'RestaurantController@store']);
        Route::get('{id}/edit', ['as' => 'restaurants.edit', 'uses' => 'RestaurantController@edit']);
        Route::post('/update', ['as' => 'restaurants.update', 'uses' => 'RestaurantController@update']);
        Route::get('{id}/destroy', ['as' => 'restaurants.destroy', 'uses' => 'RestaurantController@destroy']);
    });
    Route::group(['prefix' => 'menu-cate'], function () {
        Route::get('/', ['as' => 'menu-cate.index', 'uses' => 'MenuCateController@index']);
        Route::get('/create', ['as' => 'menu-cate.create', 'uses' => 'MenuCateController@create']);
        Route::post('/store', ['as' => 'menu-cate.store', 'uses' => 'MenuCateController@store']);
        Route::get('{id}/edit', ['as' => 'menu-cate.edit', 'uses' => 'MenuCateController@edit']);
        Route::post('/update', ['as' => 'menu-cate.update', 'uses' => 'MenuCateController@update']);
        Route::get('{id}/destroy', ['as' => 'menu-cate.destroy', 'uses' => 'MenuCateController@destroy']);
    });
    Route::group(['prefix' => 'menu-food'], function () {
        Route::get('/', ['as' => 'menu-food.index', 'uses' => 'MenuFoodController@index']);
        Route::get('/create', ['as' => 'menu-food.create', 'uses' => 'MenuFoodController@create']);
        Route::post('/store', ['as' => 'menu-food.store', 'uses' => 'MenuFoodController@store']);
        Route::get('{id}/edit', ['as' => 'menu-food.edit', 'uses' => 'MenuFoodController@edit']);
        Route::post('/update', ['as' => 'menu-food.update', 'uses' => 'MenuFoodController@update']);
        Route::get('{id}/destroy', ['as' => 'menu-food.destroy', 'uses' => 'MenuFoodController@destroy']);
    });
    Route::group(['prefix' => 'booking-dn'], function () {
        Route::get('/', ['as' => 'booking-dn.index', 'uses' => 'BookingDnController@index']);
        Route::get('/create', ['as' => 'booking-dn.create', 'uses' => 'BookingDnController@create']);
        Route::post('/store', ['as' => 'booking-dn.store', 'uses' => 'BookingDnController@store']);
        Route::post('/update', ['as' => 'booking-dn.update', 'uses' => 'BookingDnController@update']);
        Route::get('{id}/edit',   ['as' => 'booking-dn.edit', 'uses' => 'BookingDnController@edit']);
        Route::get('{id}/destroy', ['as' => 'booking-dn.destroy', 'uses' => 'BookingDnController@destroy']);
        Route::get('/ajax-get-price', ['as' => 'booking-dn.ajax-get-price', 'uses' => 'BookingDnController@ajaxGetPrice']);
    });

    Route::group(['prefix' => 'tour-system'], function () {
        Route::get('/', ['as' => 'tour-system.index', 'uses' => 'TourSystemController@index']);
        Route::get('/create', ['as' => 'tour-system.create', 'uses' => 'TourSystemController@create']);
        Route::post('/store', ['as' => 'tour-system.store', 'uses' => 'TourSystemController@store']);
        Route::get('{id}/edit',   ['as' => 'tour-system.edit', 'uses' => 'TourSystemController@edit']);
        Route::post('/update', ['as' => 'tour-system.update', 'uses' => 'TourSystemController@update']);
        Route::get('{id}/destroy', ['as' => 'tour-system.destroy', 'uses' => 'TourSystemController@destroy']);
        Route::get('/price',   ['as' => 'tour-system.price', 'uses' => 'TourSystemController@price']);
        Route::get('{id}/edit-price',   ['as' => 'tour-system.edit-price', 'uses' => 'TourSystemController@editPrice']);
        Route::post('/store-price', ['as' => 'tour-system.store-price', 'uses' => 'TourSystemController@storePrice']);
        Route::post('/store-price-new', ['as' => 'tour-system.store-price-new', 'uses' => 'TourSystemController@storePrice']);
        Route::post('/update-price', ['as' => 'tour-system.update-price', 'uses' => 'TourSystemController@updatePrice']);
    });

    Route::group(['prefix' => 'don-tien-free-payment'], function () {
        Route::get('/', ['as' => 'don-tien-free-payment.index', 'uses' => 'DonTienFreePaymentController@index']);
        Route::get('/create', ['as' => 'don-tien-free-payment.create', 'uses' => 'DonTienFreePaymentController@create']);
        Route::post('/store', ['as' => 'don-tien-free-payment.store', 'uses' => 'DonTienFreePaymentController@store']);

        Route::get('{id}/edit',   ['as' => 'don-tien-free-payment.edit', 'uses' => 'DonTienFreePaymentController@edit']);
        Route::post('/update', ['as' => 'don-tien-free-payment.update', 'uses' => 'DonTienFreePaymentController@update']);
        Route::get('{id}/destroy', ['as' => 'don-tien-free-payment.destroy', 'uses' => 'DonTienFreePaymentController@destroy']);
    });
    Route::group(['prefix' => 'deposit'], function () {
        Route::get('/', ['as' => 'deposit.index', 'uses' => 'DepositController@index']);
        Route::get('/create', ['as' => 'deposit.create', 'uses' => 'DepositController@create']);
        Route::post('/store', ['as' => 'deposit.store', 'uses' => 'DepositController@store']);
        Route::get('{id}/edit', ['as' => 'deposit.edit', 'uses' => 'DepositController@edit']);
        Route::post('/update', ['as' => 'deposit.update', 'uses' => 'DepositController@update']);
        Route::get('{id}/destroy', ['as' => 'deposit.destroy', 'uses' => 'DepositController@destroy']);
        Route::get('/change-value', ['as' => 'deposit.change-value-by-column', 'uses' => 'DepositController@changeValueByColumn']);
    });
    Route::group(['prefix' => 'acc-logs'], function () {
        Route::get('/', ['as' => 'acc-logs.index', 'uses' => 'AccLogsController@index']);
    });
    Route::group(['prefix' => 'booking-combo'], function () {
        Route::get('/', ['as' => 'booking-combo.index', 'uses' => 'BookingComboController@index']);
        Route::get('/create', ['as' => 'booking-combo.create', 'uses' => 'BookingComboController@create']);
        Route::post('/store', ['as' => 'booking-combo.store', 'uses' => 'BookingComboController@store']);
        Route::get('{id}/edit', ['as' => 'booking-combo.edit', 'uses' => 'BookingComboController@edit']);
        Route::post('/update', ['as' => 'booking-combo.update', 'uses' => 'BookingComboController@update']);
        Route::get('{id}/destroy', ['as' => 'booking-combo.destroy', 'uses' => 'BookingComboController@destroy']);
        Route::get('hotel-rooms', ['as' => 'booking-combo.getHotelRooms', 'uses' => 'BookingComboController@getHotelRooms']);
        Route::get('calculate-price', ['as' => 'booking-combo.calculatePrice', 'uses' => 'BookingComboController@calculatePrice']);
    });
    Route::group(['prefix' => 'sms-transaction'], function () {
        Route::get('/', ['as' => 'sms-transaction.index', 'uses' => 'SmsTransactionController@index']);
        Route::get('{id}/edit', ['as' => 'sms-transaction.edit', 'uses' => 'SmsTransactionController@edit']);
        Route::post('/update', ['as' => 'sms-transaction.update', 'uses' => 'SmsTransactionController@update']);
        Route::get('/change-value', ['as' => 'sms-transaction.change-value-by-column', 'uses' => 'SmsTransactionController@changeValueByColumn']);
    });
    Route::group(['prefix' => 'report-setting'], function () {
        Route::get('/', ['as' => 'report-setting.index', 'uses' => 'ReportSettingsController@index']);
        Route::get('{id}/edit',   ['as' => 'report-setting.edit', 'uses' => 'ReportSettingsController@edit']);
        Route::post('/update', ['as' => 'report-setting.update', 'uses' => 'ReportSettingsController@update']);
        Route::get('/create',   ['as' => 'report-setting.create', 'uses' => 'ReportSettingsController@create']);
        Route::post('/store',   ['as' => 'report-setting.store', 'uses' => 'ReportSettingsController@store']);
    });

    Route::group(['prefix' => 'cano'], function () {
        Route::get('/', ['as' => 'cano.index', 'uses' => 'CanoController@index']);
        Route::get('/create', ['as' => 'cano.create', 'uses' => 'CanoController@create']);
        Route::post('/store', ['as' => 'cano.store', 'uses' => 'CanoController@store']);
        Route::get('{id}/edit', ['as' => 'cano.edit', 'uses' => 'CanoController@edit']);
        Route::post('/update', ['as' => 'cano.update', 'uses' => 'CanoController@update']);
        Route::get('{id}/destroy', ['as' => 'cano.destroy', 'uses' => 'CanoController@destroy']);
    });

    Route::group(['prefix' => 'steersman'], function () {
        Route::get('/', ['as' => 'steersman.index', 'uses' => 'SteersManController@index']);
        Route::get('/create', ['as' => 'steersman.create', 'uses' => 'SteersManController@create']);
        Route::post('/store', ['as' => 'steersman.store', 'uses' => 'SteersManController@store']);
        Route::get('{id}/edit', ['as' => 'steersman.edit', 'uses' => 'SteersManController@edit']);
        Route::post('/update', ['as' => 'steersman.update', 'uses' => 'SteersManController@update']);
        Route::get('{id}/destroy', ['as' => 'steersman.destroy', 'uses' => 'SteersManController@destroy']);
    });

    Route::group(['prefix' => 'ads-campaign'], function () {
        Route::get('/', ['as' => 'ads-campaign.index', 'uses' => 'AdsCampagnController@index']);
        Route::get('/create', ['as' => 'ads-campaign.create', 'uses' => 'AdsCampagnController@create']);
        Route::post('/store', ['as' => 'ads-campaign.store', 'uses' => 'AdsCampagnController@store']);
        Route::get('{id}/edit', ['as' => 'ads-campaign.edit', 'uses' => 'AdsCampagnController@edit']);
        Route::post('/update', ['as' => 'ads-campaign.update', 'uses' => 'AdsCampagnController@update']);
        Route::get('{id}/destroy', ['as' => 'ads-campaign.destroy', 'uses' => 'AdsCampagnController@destroy']);
    });

    Route::group(['prefix' => 'user-balance-withdraw'], function () {
        Route::get('/', ['as' => 'user-balance-withdraw.index', 'uses' => 'UserBalanceWithdrawController@index']);
        Route::get('/create', ['as' => 'user-balance-withdraw.create', 'uses' => 'UserBalanceWithdrawController@create']);
        Route::post('/store', ['as' => 'user-balance-withdraw.store', 'uses' => 'UserBalanceWithdrawController@store']);
        Route::get('{id}/edit', ['as' => 'user-balance-withdraw.edit', 'uses' => 'UserBalanceWithdrawController@edit']);
        Route::post('/update', ['as' => 'user-balance-withdraw.update', 'uses' => 'UserBalanceWithdrawController@update']);
        Route::get('{id}/destroy', ['as' => 'user-balance-withdraw.destroy', 'uses' => 'UserBalanceWithdrawController@destroy']);
        Route::get('/viewQRCode', ['as' => 'user-balance-withdraw.view-qr-code', 'uses' => 'UserBalanceWithdrawController@viewQRCode']);
    });

    Route::group(['prefix' => 'maxi'], function () {
        Route::get('/', ['as' => 'maxi.index', 'uses' => 'MaxiController@index']);
        Route::get('/create', ['as' => 'maxi.create', 'uses' => 'MaxiController@create']);
        Route::post('/store', ['as' => 'maxi.store', 'uses' => 'MaxiController@store']);
        Route::get('{id}/edit', ['as' => 'maxi.edit', 'uses' => 'MaxiController@edit']);
        Route::post('/update', ['as' => 'maxi.update', 'uses' => 'MaxiController@update']);
        Route::get('{id}/destroy', ['as' => 'maxi.destroy', 'uses' => 'MaxiController@destroy']);

        Route::get('{id}/history', ['as' => 'maxi.history', 'uses' => 'MaxiHistoryController@index']);
        Route::post('/maxi/history/store', ['as' => 'maxi.history.store', 'uses' => 'MaxiHistoryController@store']);
        Route::get('{id}/history-destroy', ['as' => 'maxi.history.destroy', 'uses' => 'MaxiHistoryController@destroy']);

    });
});
