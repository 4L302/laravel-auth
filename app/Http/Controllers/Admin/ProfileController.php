<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Doctrine\DBAL\Schema\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     *
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $posts = Post::paginate(3);

        } else {
            $posts = Post::where('user_id', $user->id)->paginate(3);
        }
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePostRequest  $request
     *
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $slug = Str::slug($request->title, '-');
        $data['slug'] = $slug;
        //$currentUser = Auth::user();
        $data['user_id'] = Auth::id();
        if ($request->hasFile('image')) {
            $image_path = Storage::put('uploads', $request->image);
            $data['image'] = asset('storage/' . $image_path);
        }
        $post = Post::create($data);

        if ($request->has('tags')) {
            $post->tags()->attach($request->tags);
        }
        return redirect()->route('admin.posts.show', $post->slug);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     *
     *
     */
    public function show(Post $post)
    {
        if (!Auth::user()->is_admin && $post->user_id !== Auth::id()) {
            abort(403);
        }
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     *
     */
    public function edit(Post $post)
    {
        if (!Auth::user()->is_admin && $post->user_id !== Auth::id()) {
            abort(403);
        }
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     *
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $data = $request->validated();
        $slug = Str::slug($request->title, '-');
        $data['slug'] = $slug;
        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::delete($post->image);
            }
            $image_path = Storage::put('uploads', $request->image);
            $data['image'] = asset('storage/' . $image_path);
        }
        $post->update($data);
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        } else {
            $post->tags()->sync([]);
        }
        return redirect()->route('admin.posts.show', $post->slug)->with('message', 'Il post è stato aggiornato');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     *
     */
    public function destroy(Post $post)
    {
        if ($post->image) {
            $datogliere = "";
            $imagetoremove = str_replace($datogliere, '', $post->image);
            //dd($imagetoremove);
            Storage::delete($imagetoremove);
        }
        $post->delete();
        return redirect()->route('admin.posts.index')->with('message', "$post->title deleted successfully.");
    }
}