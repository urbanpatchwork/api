<?php

class CategoryController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// fetch some data from the file system or database or whatever
        return json_encode([
            [
                'id' => 1,
                'title' => 'Bee keeping',
                'content' => '<h1>Bee Keeping</h1><p>Some content</p>'
            ],
            [
                'id' => 2,
                'title' => 'Home Gardens',
                'content' => '<h1>Gardens!</h1><p>Some are hidden!</p>'
            ]
        ]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return json_encode([
            'id' => $id,
            'title' => 'Home Gardens',
            'content' => '<h1>Gardens!</h1><p>Some are hidden!</p>'
        ]);
	}


}
