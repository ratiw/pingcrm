<?php

namespace App\Http\Controllers;

class UploadController extends Controller
{
	protected $uploadPath = 'public/storage/upload/';
	
	public function store()
	{
		$file = Input::file('filedata');
		$filename = str_random(16);
		$extension = $file->getClientOriginalExtension();
		$size = $file->getSize();
		$fullname = $filename.'.'.$extension;
		
		$uploadSuccess = $file->move($this->uploadPath, $fullname);
		
		if ($uploadSuccess)
		{
			return Response::json(['name' => $fullname, 'size' => $size], 200);
		}
		else
		{
			return Response::json('error', 400);
		}
	}
	
	public function destroy()
	{
		$postId = Input::get('post_id', null);
		
		if ( ! is_null($postId))
		{
			$post = $this->postRepo->findById($postId);
			$this->allowOwner($post);
		}
		
		$file = Input::get('filepath');
		$path = public_path() . $file;
		
		if (File::exists($path))
		{
			File::delete($path);
			
			$filename = File::name($file).'.'.File::extension($file);
			
			if ( ! is_null($postId))
			{
				Attachment::where('post_id', $postId)
				->where('filename', $filename)
				->delete();
			}
			
			return Response::json($file.' deleted', 200);
		}
		else{
			return Response::json('error', 400);
		}
	}
	
	
}



