<?php

class ProjectController extends \BaseController {

    protected $paramValidators = array(
        'within_zip' => 'numeric',
        'near_point' => 'longlat', // long, lat
        'radius' => 'numeric',
    );
    
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $input = Input::all();
        $allowedParams = Validator::make($input, $this->paramValidators);
        if ($allowedParams->fails()) {
            return 'invalid';
        }
		return '';
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $model = new stdClass();
        $input = Input::all();
        $validator = Validator::make($input, $model->validatorRules);
        // location for everything "miller park" or whatever
        // long lat
        // chcek for private land and it's theirs check box for forraging stuff
        $data = array(
            'securityHash' => $project->adminHash
        );
		Mail::send('emails.new-project', $data, function($message)
        {
            $message->to('email_just_given@example.com', $owner->name)->subject('Welcome!');
        });
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return '';
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
