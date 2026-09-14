<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\AlatActions;
use App\Http\Controllers\Admin\DashboardActions;
use App\Http\Controllers\Admin\KategoriActions;
use App\Http\Controllers\Admin\PeminjamanActions;
use App\Http\Controllers\Admin\PengembalianActions;
use App\Http\Controllers\Admin\UserActions;

class AdminController extends Controller
{
    use DashboardActions;
    use AlatActions;
    use UserActions;
    use KategoriActions;
    use PeminjamanActions;
    use PengembalianActions;
}
