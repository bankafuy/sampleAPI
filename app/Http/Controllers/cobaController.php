<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class cobaController extends Controller
{
    //
    public function create(Request $reguest)
    {
    	if(!Auth::check()) {
    		return response()->json([
    			'error' => "You are not authenticated"
    			], 401);
    	}

    	if(Gate::denies('create-user')) {
    		return response()->json([
    			'error' => "You are not authorized to create users"
    			], 403);
    	}

    	$data = $request->get('user');
    	$email = $data['email'];
    	$exists = User::where('email', $email)->get()->first();

    	if(!is_null($exists)) {
    		return response()->json([
    			'error' => "A user with the email $email already exists!"
    			]);
    	}

    	$user = User::create($data);
    	$activation = Activation::create($user->id);
    	$user->activation->save($activation);

    	Mail::send('user.activation', function($message) use ($user)) {
    		$m->from('hello@app.com', 'Sample API v1');
    		$m->to($user->email, $user->name)->subject('Welcome to the jungle');
    	});
		
		return response()->json($user, 201);
    }
}
