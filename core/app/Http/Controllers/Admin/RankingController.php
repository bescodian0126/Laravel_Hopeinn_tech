<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRanking;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index()
    {
        $pageTitle = "User Rankings";
        $userRankings = UserRanking::paginate(getPaginate());
        return view('admin.user_ranking.list', compact('pageTitle', 'userRankings'));
    }

    public function store(Request $request, $id = 0)
    {
        $validateRule = $id ? 'nullable' : 'required';
        $request->validate([
            'level'    => 'required|integer:gt:0',
            'name'     => 'required',
            'bv_left'  => 'required|integer|gt:0',
            'bv_right' => 'required|integer|gt:0',
            'bonus'    => 'required|numeric|min:0',
            'icon'     => [$validateRule, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $userRanking = UserRanking::findOrFail($id);
            $notify[]    = ['success', 'User ranking updated successfully'];
        } else {
            $userRanking = new UserRanking();
            $notify[]    = ['success', 'User ranking added successfully'];
        }

        if ($request->hasFile('icon')) {
            try {
                $userRanking->icon = fileUploader($request->icon, getFilePath('userRanking'), getFileSize('userRanking'), $userRanking->icon);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your icon'];
                return back()->withNotify($notify);
            }
        }

        $userRanking->level    = $request->level;
        $userRanking->name     = $request->name;
        $userRanking->bv_left  = $request->bv_left;
        $userRanking->bv_right = $request->bv_right;
        $userRanking->bonus    = $request->bonus;
        $userRanking->save();

        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return UserRanking::changeStatus($id);
    }
}
