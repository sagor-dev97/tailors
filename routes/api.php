<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FirebaseTokenController;
use App\Http\Controllers\Api\FriendRequestController;
use App\Http\Controllers\Api\FriendsController;
use App\Http\Controllers\Api\Frontend\AddTocart\AddToCartController;
use App\Http\Controllers\Api\Frontend\AdminUser\AdminUserController;
use App\Http\Controllers\Api\Frontend\Blog\BlogController;
use App\Http\Controllers\Api\Frontend\categoryController;
use App\Http\Controllers\Api\Frontend\CheckRedmeeCodeController;
use App\Http\Controllers\Api\Frontend\FaqController;
use App\Http\Controllers\Api\Frontend\FestiveAlbum\FestiveAlbumController;
use App\Http\Controllers\Api\Frontend\Gallery\GalleryController;
use App\Http\Controllers\Api\Frontend\GetPhoneNumber\GetPhoneNumberController;
use App\Http\Controllers\Api\Frontend\HomeController;
use App\Http\Controllers\Api\Frontend\ImageController;
use App\Http\Controllers\Api\Frontend\Invitation\SendInvitationController;
use App\Http\Controllers\Api\Frontend\Order\OrderController;
use App\Http\Controllers\Api\Frontend\PageController;
use App\Http\Controllers\Api\Frontend\PostController;
use App\Http\Controllers\Api\Frontend\PrivecyPolicyController;
use App\Http\Controllers\Api\Frontend\Question\QuestionController;
use App\Http\Controllers\Api\Frontend\RedeemCode\RedemeeCodeController;
use App\Http\Controllers\Api\Frontend\RedeemCode\RedmeeCodeController;
use App\Http\Controllers\Api\Frontend\Review\ReviewController;
use App\Http\Controllers\Api\Frontend\SettingsController;
use App\Http\Controllers\Api\Frontend\SocialLinksController;
use App\Http\Controllers\Api\Frontend\SubcategoryController;
use App\Http\Controllers\Api\Frontend\SubscriberController;
use App\Http\Controllers\Api\Frontend\Users\UsersListController;
use App\Http\Controllers\Api\Frontend\Wishlist\WishlistController;
use App\Http\Controllers\Api\Gateway\Stripe\StripeOnBoardingController;
use App\Http\Controllers\Api\InAppPurchase\InAppPurchaseController;
use App\Http\Controllers\Api\NotificationController;
use App\Models\BoostingPayment;
use Illuminate\Support\Facades\Route;

// health check
Route::get('/health-check', function () {
    return "All Right... 👍";
});

//page
Route::get('/page/home', [HomeController::class, 'index']);
Route::get('/social/links', [SocialLinksController::class, 'index']);
Route::get('/settings', [SettingsController::class, 'index']);
Route::get('/faq', [FaqController::class, 'index']);

Route::post('subscriber/store', [SubscriberController::class, 'store'])->name('api.subscriber.store');

// Friend request system

// Sinle chatting system
Route::middleware(['auth:api'])->controller(ChatController::class)->prefix('auth/chat')->group(function () {
    Route::get('/list', 'list'); // working
    Route::post('/send/{receiver_id}', 'send'); // working
    Route::get('/conversation/{receiver_id}', 'conversation'); // working
    Route::get('room/{receiver_id}', 'room');
    Route::get('/search', 'search'); // working
    Route::get('/seen/all/{receiver_id}', 'seenAll'); // working
    Route::get('/seen/single/{chat_id}', 'seenSingle'); // working
    Route::delete('/delete/{receiver_id}', 'deleteChat'); // working
    Route::delete('/delete/chat/messages', 'deleteMessages'); // working
});


Route::middleware(['auth:api'])->controller(ImageController::class)->prefix('auth/post/image')->group(function () {
    Route::get('/', 'index');
    Route::post('/store', 'store');
    Route::get('/delete/{id}', 'destroy');
});



/*
# Auth Route
*/
Route::group(['middleware' => 'guest:api'], function ($router) {
    //register
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('/verify-email', [RegisterController::class, 'VerifyEmail']);
    Route::post('/resend-otp', [RegisterController::class, 'ResendOtp']);
    Route::post('/verify-otp', [RegisterController::class, 'VerifyEmail']);
    //login
    Route::post('login', [LoginController::class, 'login'])->name('api.login');
    //forgot password
    Route::post('/forget-password', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('/otp-token', [ResetPasswordController::class, 'MakeOtpToken']);
    Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword']);
    //social login
    Route::post('/social-login', [SocialLoginController::class, 'SocialLogin']);
});

Route::group(['middleware' => ['auth:api', 'api-otp']], function ($router) {
    Route::get('/refresh-token', [LoginController::class, 'refreshToken']);
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::get('/user-details', [UserController::class, 'me']);
    Route::get('/all-customers', [UserController::class, 'allUsers']);
    Route::get('/account/switch', [UserController::class, 'accountSwitch']);
    Route::post('/update-user', [UserController::class, 'updateProfile']);
    Route::post('/update-avatar', [UserController::class, 'updateAvatar']);
    Route::delete('/delete-profile', [UserController::class, 'destroy']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::post('/support', [UserController::class, 'support']);
});

// get faqs
Route::get('/faq', [FaqController::class, 'index']);

/*
# Firebase Notification Route
*/
Route::middleware(['auth:api'])->controller(FirebaseTokenController::class)->prefix('firebase')->group(function () {
    Route::get("test", "test");
    Route::post("token/add", "store");
    Route::post("token/get", "getToken");
    Route::post("token/delete", "deleteToken");
});

/*
# In App Notification Route
*/

Route::middleware(['auth:api'])->controller(NotificationController::class)->prefix('notify')->group(function () {
    Route::get('test', 'test');
    Route::get('/', 'index');
    Route::get('status/read/all', 'readAll');
    Route::get('status/read/{id}', 'readSingle');
});

/*
# Chat Route
*/

Route::middleware(['auth:api'])->controller(ChatController::class)->prefix('auth/chat')->group(function () {
    Route::get('/list', 'list');
    Route::post('/send/{receiver_id}', 'send');
    Route::get('/conversation/{receiver_id}', 'conversation');
    Route::get('/room/{receiver_id}', 'room');
    Route::get('/search', 'search');
    Route::get('/seen/all/{receiver_id}', 'seenAll');
    Route::get('/seen/single/{chat_id}', 'seenSingle');
});
Route::prefix('cms')->name('cms.')->group(function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('how-it-works', [HomeController::class, 'howItWorks'])->name('how_it_works');
    Route::get('/how-it-works/details/{slug}', [HomeController::class, 'howItWorksDetails']);
});

Route::get('/privacy-policy', [PrivecyPolicyController::class, 'index']);
// Route::get('/privacy-policy', [PrivecyPolicyController::class, 'privacyPolicy']);
Route::get('/terms-conditions', [PrivecyPolicyController::class, 'termsConditions']);


// dynamic page
Route::post('/subscribe', [SubscriberController::class, 'subscribe']);

Route::controller(UsersListController::class)->group(function () {
    Route::get('/user/search', 'search');
    Route::get('/seller-details/{slug}', 'userDetails');
});
Route::controller(AdminUserController::class)->group(function () {
    Route::get('/admin-users', 'getManagerUsers');
    Route::get('/seller-details/{slug}', 'userDetails');
});



Route::controller(OrderController::class)->group(function () {
    Route::post('/order', 'store');
    Route::post('/re-order/{id}', 'reOrder');
    Route::post('/order-update/{id}', 'update');
    Route::get('/orderData', 'getOrderData');
    Route::get('/user-orderdata', 'UsersOrderData');
    Route::get('/orderDetails/{id}', 'showDetails');
    Route::get('/user-orderDetails/{id}', 'UsersOrderDetails');

    Route::post('/update-order-status/{id}', 'UpdateOrderStatus');
});

Route::post('/update-profile',[UserController::class,'updateProfile']);
Route::controller(GalleryController::class)->group(function () {
    Route::post('/gallery', 'store');
    Route::post('update-gallery/{id}', 'updateGallery');
    Route::get('/get-gallery', 'getGallery');
    Route::delete('/delete-gallery/{id}', 'deleteGallery');
});
Route::controller(BlogController::class)->group(function () {
    Route::post('/blogs', 'store');
    Route::post('update-blogs/{id}', 'updateBlogs');
    Route::get('/get-blogs', 'blogs');
    Route::delete('/delete-blogs/{id}', 'deleteBlogs');
});


