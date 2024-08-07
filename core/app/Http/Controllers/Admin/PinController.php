<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Pin;
use Illuminate\Http\Request;

class PinController extends Controller
{
    public function allPins()
    {
        $pageTitle = "All Pins";
        $pins = Pin::searchable(['pin'])->with('createUser')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.pin.index', compact('pageTitle', 'pins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'number' => 'required|integer|gt:0'
        ]);

        for ($i = 1; $i <= $request->number; $i++) {
            $pin = new Pin();
            $pin->amount = $request->amount;
            $pin->pin = rand(10000000,99999999).'-'.rand(10000000,99999999).'-'.rand(10000000,99999999).'-'.rand(10000000,99999999);
            $pin->details = "Created via admin";
            $pin->save();
        }

        $notify[] = ['success', 'Pin added Successfully'];
        return back()->withNotify($notify);
    }

    public function userPins()
    {
        $pageTitle = "All User Pins";
        $pins = Pin::whereNotNull('generate_user_id')->searchable(['pin'])->with('createUser')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.pin.index', compact('pageTitle', 'pins'));
    }

    public function adminPins()
    {
        $pageTitle = "All Admin Pins";
        $pins = Pin::whereNull('generate_user_id')->searchable(['pin'])->with('createUser')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.pin.index', compact('pageTitle', 'pins'));
    }

    public function usedPins()
    {
    	$pageTitle = "All Used Pins";
    	$pins = Pin::where('status', Status::ENABLE)->searchable(['pin'])->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
    	return view('admin.pin.index', compact('pageTitle', 'pins'));
    }

    public function unusedPins()
    {
    	$pageTitle = "All Unused Pins";
    	$pins = Pin::where('status', Status::DISABLE)->searchable(['pin'])->with('createUser')->orderBy('id', 'desc')->paginate(getPaginate());
    	return view('admin.pin.index', compact('pageTitle', 'pins'));
    }

}
