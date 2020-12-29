<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Training;
use File;
use Storage;
use App\Http\Requests\StoreTrainingRequest;
use Mail;
use Notification;
use App\Notifications\DeleteTrainingNotification;

class TrainingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','admin']);
    }
    
    public function index(Request $request)
    {
        if ($request->keyword) {
            $search = $request->keyword;

            // $trainings = Training::where('title', 'LIKE', '%'.$search.'%')
            //     ->orWhere('description', 'LIKE', '%'.$search.'%')
            //     ->paginate(5);

            $trainings = auth()->user()->trainings()->where('title', 'LIKE', '%'.$search.'%')
                ->orWhere('description', 'LIKE', '%'.$search.'%')
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        } else {
            // query trainings from trainings table using model
            $trainings = Training::latest()->paginate(5);

            // get current authenticate user
            // $user = auth()->user();
            // get user trainings using relationship with pagination 5
            // $trainings = $user->trainings()->paginate(5);
        }
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

        // send email to user
        // Mail::send('email.training-created', [
        //     'title' => $training->title,
        //     'description' => $training->description
        // ], function ($message) {
        //     $message->to('tarmizi@mizi.my');
        //     $message->subject('Training Created Email using Inline Mail');
        // });

        // send email to user using Mailable Class
        // Mail::to('tarmizi@mizi.my')->send(new \App\Mail\TrainingCreated($training));

        dispatch(new \App\Jobs\SendEmailJob($training));

        return redirect()
            ->route('training:list')
            ->with([
                'alert-type' => 'alert-primary',
                'alert' => 'Your training has been created!'
            ]);
    }

    public function show(Training $training)
    {
        $this->authorize('view', $training);

        // return to view
        return view('trainings.show', compact('training'));
    }

    public function edit(Training $training)
    {
        $this->authorize('update', $training);

        // return to view
        return view('trainings.edit', compact('training'));
    }

    public function update(Training $training, Request $request)
    {
        $this->authorize('update', $training);

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
        $this->authorize('delete', $training);

        $user = auth()->user();
        Notification::send($user, new DeleteTrainingNotification());

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

    public function forceDelete(Training $training)
    {
        $this->authorize('delete', $training);

        $user = auth()->user();
        Notification::send($user, new DeleteTrainingNotification());

        if ($training->attachment != null) {
            Storage::disk('public')->delete($training->attachment);
        }
        
        $training->forceDelete();

        return redirect()
            ->route('training:list')
            ->with([
                'alert-type' => 'alert-danger',
                'alert' => 'Your training has been deleted!'
            ]);
    }
}
