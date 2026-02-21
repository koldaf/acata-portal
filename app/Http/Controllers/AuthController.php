<?php
//some of the code in this file is based on the default Laravel authentication scaffolding, but has been customized to fit the specific needs of the application. The controller handles user registration, login, and password reset functionalities, as well as displaying the appropriate views for these actions. It also interacts with the Members model to create new member records and associate interests with them during registration.
namespace App\Http\Controllers;
use App\Models\Interest;
use App\Models\Members;
use App\Models\MembershipTypes;
use App\Models\Countries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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

        $remember = $request->has('remember') ? true : false;

        // Attempt to authenticate the user
        if (Auth::attempt($credentials, $remember)) {
            //dd(Auth::user());
            // Authentication passed...
            $request->session()->regenerate();
            
            return redirect()->intended('/dashboard');
        }

        // Authentication failed...
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function showRegistrationForm()
    {
        return view('auth.register', [
            'interests' => Interest::all(), 
            'memtypes' => MembershipTypes::all(),
            'countries' => Countries::all()
        ]);
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

    //password reset methods
    public function showLinkRequestForm(){
        return view('auth.passwords.email');
    }
    public function sendResetLinkEmail(Request $request){
        $request->validate([
            'email' => 'required|email',
        ]);
       
        $status = Password::sendResetLink(
            $request->only('email')
        );
        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->with(['status' => "If that email address exists in our system, a password reset link has been sent to it."]);
    }
    public function showResetForm($token){
        return view('auth.passwords.reset', ['token' => $token]);
    }
    /*public function reset(Request $request){
        $request->validate([
        */
    public function reset(Request $request){
        //dd($request->all());
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                $user->setRememberToken(Str::random(60));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}

