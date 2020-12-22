<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Training;
use File;
use Storage;
use App\Http\Requests\StoreTrainingRequest;

class TrainingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        // query trainings from trainings table using model
        // $trainings = Training::paginate(5);

        // get current authenticate user
        $user = auth()->user();
        // get user trainings using relationship with pagination 5
        $trainings = $user->trainings()->paginate(5);

        // dd($trainings); // dump and die

        // return to view with $trainings
        // resources/views/trainings/index.blade.php
        return view('trainings.index', compact('trainings'));
    }

    public function create()
    {
        // return training create form
        // resources/views/trainings/create.blade.php
        return view('trainings.create');
    }

    public function store(StoreTrainingRequest $request)
    {
        // $this->validate(
        //     $request,
        //     [
        //         'title' => 'required|min:50',
        //         'description' => 'required|min:10',
        //         'attachment' => 'required|mimes:pdf'
        //     ]
        // );

        // store all data from form to trainings table
        // dd($request->all());

        //Method 1 - POPO - Plain Old PHP Object
        $training = new Training();
        $training->title = $request->title;
        $training->description = $request->description;
        $training->trainer = $request->trainer;
        $training->user_id = auth()->user()->id;
        $training->save();

        if ($request->hasFile('attachment')) {
            // rename file 10-2020-12-22.jpg
            $filename = $training->id.'-'.date("Y-m-d").'.'.$request->attachment->getClientOriginalExtension();

            // store file on storage
            Storage::disk('public')->put($filename, File::get($request->attachment));

            // update row with filename
            $training->update(['attachment' => $filename]);
        }

        return redirect()
            ->route('training:list')
            ->with([
                'alert-type' => 'alert-primary',
                'alert' => 'Your training has been created!'
            ]);
    }

    public function show(Training $training)
    {
        // return to view
        return view('trainings.show', compact('training'));
    }

    public function edit(Training $training)
    {
        // return to view
        return view('trainings.edit', compact('training'));
    }

    public function update(Training $training, Request $request)
    {
        // update training with edited attributes
        // Method 2 - Mass Assignment
        $training->update($request->only('title', 'description', 'trainer'));

        // return to /trainings
        return redirect()
            ->route('training:list')
            ->with([
                'alert-type' => 'alert-success',
                'alert' => 'Your training has been updated!'
            ]);
    }

    public function delete(Training $training)
    {
        if ($training->attachment != null) {
            Storage::disk('public')->delete($training->attachment);
        }
        
        $training->delete();

        return redirect()
            ->route('training:list')
            ->with([
                'alert-type' => 'alert-danger',
                'alert' => 'Your training has been deleted!'
            ]);
    }
}
