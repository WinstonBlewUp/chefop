<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MenuLink;
use App\Models\Page;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'page_id' => 'required|exists:pages,id',
        ]);

        if (MenuLink::count() >= 3) {
            return back()->with('error', 'Le menu ne peut contenir que 3 pages.');
        }

        MenuLink::create(['page_id' => $request->page_id]);

        return back()->with('success', 'Page ajoutée au menu.');
    }

    public function destroy(MenuLink $menuLink)
    {
        $menuLink->delete();

        return back()->with('success', 'Page retirée du menu.');
    }
}
