<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Feed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>News Feed</h2>

    @foreach ($news as $article)
        <div class="card mb-3">
            <img src="{{ asset('storage/' . $article->image_path) }}" class="card-img-top" alt="News Image">
            <div class="card-body">
                <h5 class="card-title">{{ $article->title }}</h5>
                <p class="card-text">{{ $article->description }}</p>
                <p class="card-text"><small class="text-muted">Published on {{ $article->created_at->format('Y-m-d H:i:s') }}</small></p>
                <p class="card-text">Tags:
                    @foreach ($article->tags as $tag)
                        <span class="badge bg-primary">{{ $tag->name }}</span>
                    @endforeach
                </p>
                <div class="btn-group">
                    <form action="{{ route('news.like', $article->id) }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-success">Like</button>
                    </form>
                    <form action="{{ route('news.dislike', $article->id) }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-danger">Dislike</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{ $news->links() }}
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
