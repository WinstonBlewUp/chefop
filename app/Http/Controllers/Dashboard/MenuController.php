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

        // Obtenir le prochain ordre disponible
        $nextOrder = MenuLink::max('order') + 1;

        // Vérifier que l'élément existe selon le type
        if ($request->type === 'page') {
            $request->validate(['item_id' => 'exists:pages,id']);
            MenuLink::create(['page_id' => $request->item_id, 'order' => $nextOrder]);
            $message = 'Page ajoutée au menu.';
        } else {
            $request->validate(['item_id' => 'exists:categories,id']);
            MenuLink::create(['category_id' => $request->item_id, 'order' => $nextOrder]);
            $message = 'Catégorie ajoutée au menu.';
        }

        return back()->with('success', $message);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_links,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($request->items as $item) {
            MenuLink::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(MenuLink $menuLink)
    {
        $menuLink->delete();

        return back()->with('success', 'Élément retiré du menu.');
    }
}
