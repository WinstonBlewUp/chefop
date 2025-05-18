<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuLink extends Model
{
    protected $fillable = ['page_id'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
