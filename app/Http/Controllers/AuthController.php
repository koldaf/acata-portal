<?php

namespace App\Http\Controllers;
use App\Models\Interest;
use App\Models\Members;
use App\Models\MembershipTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //show Login Form
    public function showLoginForm()
    {
        return view('auth.login');
    }
    //login
    public function login(Request $request)
    {
        // Validate the incoming request data
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        //dd(Auth::attempt($credentials)); 

       /* $user = Members::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            return redirect('/dashboard');
        }*/

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            //dd(Auth::user());
            // Authentication passed...
            return redirect()->intended('/dashboard');
        }

        // Authentication failed...
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function showRegistrationForm()
    {
        return view('auth.register', ['interests' => Interest::all(), 'memtypes' => MembershipTypes::all()]);
    }

    public function register(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^\\+?[0-9]{10,15}$/',
            'organization' => 'required|string|max:100',
            'job_title' => 'required|string',
            'country' => 'required|string',
            'terms' => 'required',
            'title' => 'required|string|max:255',
            'membership_type' => 'required|string|max:255',
        ]);
        //create members
        $member = Members::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'affiliation' => $request->organization,
            'job_title' => $request->job_title,
            'country' => $request->country,
            'password' => $request->password,
            'title' => $request->title,
            'membership_type' => $request->membership_type,
        ]);
        if ($member) {
            //insert interests
            $member->addInterests($request->interests);
        }

        // Redirect to a desired location after registration
        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
    }
}
