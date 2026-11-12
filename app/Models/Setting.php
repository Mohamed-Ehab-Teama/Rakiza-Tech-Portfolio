<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * One saved site setting (key → value). Read them with setting('key');
 * see App\Support\Settings and config/settings.php.
 */
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    //
}
