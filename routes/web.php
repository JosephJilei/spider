<?php
/*
 * @Author: your name
 * @Date: 2021-12-27 20:27:23
 * @LastEditTime: 2021-12-27 20:56:46
 * @LastEditors: your name
 * @Description: 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 * @FilePath: \spider\routes\web.php
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::any('madeinchina', [PaController::class, 'index']);
Route::any('alibaba', [PaController::class, 'alibaba']);
Route::any('drill', [PaController::class, 'drill']);
Route::any('alibabachina', [PaController::class, 'alibabachina']);