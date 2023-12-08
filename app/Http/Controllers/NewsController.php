<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\News;
use App\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
{
    $news = News::withCount(['votes as rating' => function ($query) {
            $query->select(DB::raw('sum(vote_type) - sum(1 - vote_type)'));
        }])
        ->orderByDesc('rating')
        ->orderBy('created_at')
        ->paginate(10);

    return view('news.index', compact('news'));
}

    public function like(News $news)
    {
        // Создаем запись в таблице голосов
        $news->votes()->create(['vote_type' => 1]);
    
        // Увеличиваем рейтинг новости
        $news->increment('rating');
    
        return back();
    }
    

    public function dislike(News $news)
    {
        // Создаем запись в таблице голосов
        $news->votes()->create(['vote_type' => 0]);
    
        // Уменьшаем рейтинг новости
        $news->decrement('rating');
    
        return back();
    }
    
    public function parseNews()
    {
        $rss = Http::get('https://lenta.ru/rss/news')->xml();

        $newNewsCount = 0;

        foreach ($rss->channel->item as $item) {
            $link = (string)$item->link;

            if (!News::where('link', $link)->exists()) {
                $newNewsCount++;

                $imagePath = 'images/' . Str::random(12) . '.jpg';
                $image = file_get_contents((string)$item->enclosure['url']);
                Storage::disk('public')->put($imagePath, $image);

                $news = News::create([
                    'title' => (string)$item->title,
                    'description' => (string)$item->description,
                    'image_path' => $imagePath,
                    'link' => $link,
                ]);

                foreach ($item->category as $category) {
                    $tag = Tag::firstOrCreate(['name' => (string)$category]);
                    $news->tags()->attach($tag);
                }
            }
        }

        return "На сайт было добавлено $newNewsCount новостей";
    }
}

