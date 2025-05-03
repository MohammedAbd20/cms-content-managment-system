<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    // Display the admin dashboard
    public function index()
    {
        $usersCount = User::count();
        $articlesCount = Article::count();
        $commentsCount = Comment::count();

        return view('admin.dashboard', compact('usersCount', 'articlesCount', 'commentsCount'));
    }

    // Manage users in the CMS
    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Create a new user
    public function createUser()
    {
        return view('admin.users.create');
    }

    // Store a newly created user
    public function storeUser(Request $request)
    {
        // Validate the input data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create a new user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully!');
    }

    // Edit a user's details
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    // Update a user's details
    public function updateUser(Request $request, $id)
    {
        // Validate the input data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    // Delete a user
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }

    // Manage articles in the CMS
    public function articles()
    {
        $articles = Article::paginate(10);
        return view('admin.articles.index', compact('articles'));
    }

    // Create a new article
    public function createArticle()
    {
        return view('admin.articles.create');
    }

    // Store a newly created article
    public function storeArticle(Request $request)
    {
        // Validate the input data
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|string|in:published,draft',
        ]);

        // Create a new article
        Article::create([
            'title' => $request->title,
            'content' => $request->content,
            'status' => $request->status,
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.articles')->with('success', 'Article created successfully!');
    }

    // Edit an article
    public function editArticle($id)
    {
        $article = Article::findOrFail($id);
        return view('admin.articles.edit', compact('article'));
    }

    // Update an article
    public function updateArticle(Request $request, $id)
    {
        // Validate the input data
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|string|in:published,draft',
        ]);

        $article = Article::findOrFail($id);
        $article->update([
            'title' => $request->title,
            'content' => $request->content,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.articles')->with('success', 'Article updated successfully!');
    }

    // Delete an article
    public function destroyArticle($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return redirect()->route('admin.articles')->with('success', 'Article deleted successfully!');
    }

    // Manage comments in the CMS
    public function comments()
    {
        $comments = Comment::paginate(10);
        return view('admin.comments.index', compact('comments'));
    }

    // Delete a comment
    public function destroyComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return redirect()->route('admin.comments')->with('success', 'Comment deleted successfully!');
    }

    // Manage system settings
    public function settings()
    {
        $settings = Setting::all();
        return view('admin.settings.index', compact('settings'));
    }

    // Update system settings
    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'required|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $settings = Setting::first();
        $settings->update([
            'site_name' => $request->site_name,
            'site_description' => $request->site_description,
        ]);

        if ($request->hasFile('site_logo')) {
            $logoPath = $request->file('site_logo')->store('logos', 'public');
            $settings->update(['site_logo' => $logoPath]);
        }

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully!');
    }

    // Change admin password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors('Current password is incorrect.');
        }

        // Update password
        $admin->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Password updated successfully!');
    }
}
