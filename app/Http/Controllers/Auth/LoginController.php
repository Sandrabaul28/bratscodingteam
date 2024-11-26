<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only('logout');
    }

    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            // If the user is already authenticated, redirect them based on their role
            $user = Auth::user();
            return $this->redirectUserBasedOnRole($user);
        }

        // If not authenticated, show the login form
        return view('auth.login', [
            'title' => 'Welcome to Crops Bontoc'
        ]);
    }

    /**
     * Handle the login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validate the login form inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // Check if a user exists with the provided email
        if (!User::where('email', $request->email)->exists()) {
            return redirect()->route('login')->withErrors(['email' => 'No account found with this email.']);
        }

        // Attempt to authenticate the user using the provided credentials
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            // Regenerate the session to prevent session fixation attacks
            $request->session()->regenerate();

            // Authentication passed, check user role and redirect accordingly
            $user = Auth::user();
            return $this->redirectUserBasedOnRole($user);
        }

        // Authentication failed, redirect back to login with error message
        return redirect()->route('login')->withErrors(['email' => 'Invalid credentials provided.']);
    }

    /**
     * Redirect the user based on their role.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectUserBasedOnRole($user)
    {
        switch ($user->role->role_name) { // Assuming 'role_name' is the column in the roles table
            case 'Admin':
                return redirect()->route('admin.dashboard');
            case 'User':
                return redirect()->route('user.dashboard');
            case 'Aggregator':
                return redirect()->route('aggregator.dashboard');
            default:
                Auth::logout(); // Log out unauthorized users
                return redirect()->route('login')->withErrors(['email' => 'Unauthorized access.']);
        }
    }

    /**
     * Handle the logout request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login'); // Redirect to login page
    }
}
