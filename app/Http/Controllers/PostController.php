<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified')->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('posts.index', ['posts' => \App\Post::with('categories')->orderBy('published_at', 'DESC')->simplePaginate(7)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create', ['posts' => \App\Post::with('categories')->simplePaginate(7), 'categories' => \App\Category::get()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //return $request->toArray();
        $validatedData = $request->validate([
            'title' => 'required|unique:posts|min:5|max:255',
            'body_markdown' => 'required|min:10',
            'excerpt' => 'required|min:10|max:255',
            'header_image' => 'required|image',
        ]);

        $image_path = $request->file('header_image')->store('header_images');
        if (config('filesystems.default') == 'public') {
            $image_path = '/storage/' . $image_path;
        }
        $result = new \App\Post(
            [
                'title' => $request->input('title'),
                'body_markdown' => $request->input('body_markdown'),
                'excerpt' => $request->input('excerpt'),
                'slug' => str_slug($request->input('title')),
                'header_image' => $image_path,
                'user_id' => $request->user()->id,
                'published_at' => now(),

            ]
        );
        $result->save();

        $result->categories()->attach($request->input('category_id'));

        return $result->load('categories');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        return view('posts.show', ['post' => \App\Post::where('slug', $slug)->with('user', 'categories')->firstOrFail()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}