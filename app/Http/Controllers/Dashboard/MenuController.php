<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MenuLink;
use App\Models\Page;
use App\Models\Category;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:page,category',
            'item_id' => 'required|integer',
        ]);

        if (MenuLink::count() >= 3) {
            return back()->with('error', 'Le menu ne peut contenir que 3 éléments.');
        }

        // Vérifier que l'élément existe selon le type
        if ($request->type === 'page') {
            $request->validate(['item_id' => 'exists:pages,id']);
            MenuLink::create(['page_id' => $request->item_id]);
            $message = 'Page ajoutée au menu.';
        } else {
            $request->validate(['item_id' => 'exists:categories,id']);
            MenuLink::create(['category_id' => $request->item_id]);
            $message = 'Catégorie ajoutée au menu.';
        }

        return back()->with('success', $message);
    }

    public function destroy(MenuLink $menuLink)
    {
        $menuLink->delete();

        return back()->with('success', 'Page retirée du menu.');
    }
}
