<?php

namespace Modules\Setting\Http\Controllers\Admin;

use FleetCart\Blog;
use FleetCart\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Media\Entities\File;
use Modules\Product\Entities\EntityFiles;

class BlogController extends Controller
{

    private function redisUpdate(){
        Redis::del('blogs');
    }

    public function index()
    {
        $blog = Blog::query()
                    ->orderBy('id', 'desc')
                    ->paginate(20);
        return view('setting::admin.settings.blogs')->with(compact('blog'));
    }

    public function store(Request $request)
    {

        $this->redisUpdate();
        $this->validate($request, [
            'title' => 'required',
            'short_description' => 'required|max:100',
            'description' => 'required',
            'cover_image' => 'required|file'
        ]);

        $file = $request->file('cover_image');
        $path = Storage::putFile('media', $file);

        $file = File::create([
                                 'user_id' => auth()->id(),
                                 'disk' => config('filesystems.default'),
                                 'filename' => $file->getClientOriginalName(),
                                 'path' => $path,
                                 'extension' => $file->guessClientExtension() ?? '',
                                 'mime' => $file->getClientMimeType(),
                                 'size' => $file->getSize(),
                             ]);


        $blog = Blog::query()
                    ->create([
                                 'title' => $request->title,
                                 'short_description' => $request->short_description,
                                 'slug' => Str::slug($request->title),
                                 'description' => $request->description,
                                 'cover_image' => $file->path
                             ]);


        EntityFiles::query()
                   ->updateOrCreate([
                                        'entity_id' => $blog->id,
                                        'entity_type' => 'FleetCart\Blog',
                                        'file_id' => $file->id,
                                        'zone' => 'base_image'
                                    ]);

        return redirect()->route('admin.blogs');

    }

    public function create()
    {
        return view('setting::admin.settings.blogsCreate');
    }

    public function update(Request $request, $id)
    {
        $this->redisUpdate();

        $this->validate($request, [
            'title' => 'required',
            'short_description' => 'required|max:300',
            'description' => 'required',
            'cover_image' => 'nullable'
        ]);

        $blog = Blog::query()
                    ->find($id);

        if (!is_null($request->cover_image)) {

            $file = $request->file('cover_image');
            $path = Storage::putFile('media', $file);

            $file = File::create([
                                     'user_id' => auth()->id(),
                                     'disk' => config('filesystems.default'),
                                     'filename' => $file->getClientOriginalName(),
                                     'path' => $path,
                                     'extension' => $file->guessClientExtension() ?? '',
                                     'mime' => $file->getClientMimeType(),
                                     'size' => $file->getSize(),
                                 ]);

            Blog::query()
                ->where('id', $id)
                ->update([
                             'title' => $request->title,
                             'short_description' => $request->short_description,
                             'description' => $request->description,
                             'cover_image' => $file->path
                         ]);


            EntityFiles::query()
                       ->updateOrCreate([
                                            'entity_id' => $blog->id,
                                            'entity_type' => 'FleetCart\Blog',
                                            'file_id' => $file->id,
                                            'zone' => 'base_image'
                                        ]);


        } else {


            Blog::query()
                ->where('id', $id)
                ->update([
                             'title' => $request->title,
                             'short_description' => $request->short_description,
                             'description' => $request->description,
                             'cover_image' => $blog->cover_image
                         ]);

        }


        return redirect()->route('admin.blogs');

    }

    public function delete($id)
    {
        $this->redisUpdate();

        Blog::query()
            ->where('id', $id)
            ->delete();
        return redirect()->back();
    }

    public function edit($id)
    {
        $blog = Blog::query()
                    ->find($id);
        if (empty($blog)) {
            return redirect()->back();
        }
        return view('setting::admin.settings.blogsEdit')->with(compact('blog'));
    }

}
