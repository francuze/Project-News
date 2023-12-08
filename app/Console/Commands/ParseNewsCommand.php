<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\News as AppNews;
use App\Tag as AppTag;

class ParseNewsCommand extends Command
{
    protected $signature = 'parse-news {--count=10 : The number of news to parse}';
    protected $description = 'Parse news from RSS feed';

    public function handle()
    {
        $response = Http::get('https://lenta.ru/rss/news');

        if ($response->successful()) {
            $xmlString = $response->body();
            $rss = simplexml_load_string($xmlString);

            $newNewsCount = 0;

            foreach ($rss->channel->item as $item) {
                $link = (string)$item->link;

                if (!AppNews::where('link', $link)->exists()) {
                    $newNewsCount++;

                    $imagePath = 'images/' . Str::random(12) . '.jpg';
                    $image = file_get_contents((string)$item->enclosure['url']);
                    Storage::disk('public')->put($imagePath, $image);

                    $news = AppNews::create([
                        'title' => (string)$item->title,
                        'description' => (string)$item->description,
                        'image_path' => $imagePath,
                        'link' => $link,
                    ]);

                    foreach ($item->category as $category) {
                        $tag = AppTag::firstOrCreate(['name' => (string)$category]);
                        $news->tags()->attach($tag);
                    }

                    // Проверяем, не превышено ли указанное количество постов
                    if ($newNewsCount >= $this->option('count')) {
                        break;
                    }
                }
            }

            $this->info("На сайт было добавлено $newNewsCount новостей.");
        } else {
            $this->error("Failed to fetch the RSS feed.");
        }
    }
}
