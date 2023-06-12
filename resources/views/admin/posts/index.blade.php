@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Index Post</h1>

        <a class="btn btn-success text-white" href="{{ route('admin.posts.create') }}">Crea nuovo post</a>

    </div>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Title</th>
                <th scope="col">Image</th>
                <th scope="col">Category</th>
                <th scope="col">Created</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($posts as $post)
                <tr>
                    <th scope="row">{{ $post->id }}</th>
                    <td>{{ $post->title }}</td>
                    <td><img class="img-thumbnail" style="width:100px" src="{{ $post->image }}" alt="{{ $post->title }}">
                    </td>
                    <td>
                        {{ $post->category ? $post->category->name : 'Senza categoria' }}
                    </td>
                    <td>{{ $post->created_at }}</td>
                    <td>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.posts.show', $post->slug) }}" class="btn btn-primary text-white"><i
                                    class="fa-solid fa-eye"></i></a>
                            <a href="{{ route('admin.posts.edit', $post->slug) }}" class="btn btn-warning text-white"><i
                                    class="fa-solid fa-pencil"></i></a>
                            <form action="{{route('admin.posts.destroy', $post->slug) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type='submit' class="delete-button btn btn-danger text-white"
                                    data-item-title="{{ $post->title }}"> <i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $posts->links('vendor.pagination.bootstrap-5') }}
@endsection