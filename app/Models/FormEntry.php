<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormEntry extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',           // The ID of the user submitting the form
        'form_id',           // The ID of the form
        'responses',         // The JSON response data
        'advice',            // Any advice generated
        'score',             // The form score (if applicable)
        'active_page',       // The current active page in the survey
        'percentage_complete', // How much of the survey is complete
        'status',            // Status of the form entry (pending, completed, etc.)
        'completed_at',      // Timestamp for when the form was completed
    ];

    protected $casts = [
        'percentage_complete' => 'integer',
        'completed_at' => 'datetime',
    ];


// Relationships

    public function getQAJson(): string {
        $QAs = [];

        $formEntry = $this;
        if (!$formEntry) {
            return json_encode($QAs);
        }

        $form = Form::find($formEntry->form_id);
        if (!$form) {
            return json_encode($QAs);
        }

        // 1) Build an ordered map: fieldName => questionText
        $questions = [];
        foreach (json_decode($form->form_json)->pages as $page) {
            foreach ($page->elements as $element) {
                $questions[$element->name] = $element->title;
            }
        }

        // 2) Decode the saved responses
        $responses = json_decode($formEntry->responses, true);
        if (!is_array($responses)) {
            return json_encode($QAs);
        }

        // 3) Iterate questions in order and grab the corresponding response
        foreach ($questions as $fieldName => $questionText) {
            if (array_key_exists($fieldName, $responses)) {
                $QAs[$questionText] = $responses[$fieldName];
            }
        }

        // 4) Return pretty‑printed JSON
        return json_encode($QAs, JSON_PRETTY_PRINT);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function form() {
        return $this->belongsTo(Form::class);
    }

    public function notes() {
        return $this->hasMany(Note::class);
    }

    public function media() {
        return $this->belongsToMany(Media::class, 'form_entry_media');

        // Usage
        // Attach a media to a form entry
        // $formEntry = FormEntry::find(1);
        // $formEntry->media()->attach($media);

        // Retrieve the media associated with a form entry
        // $media = Media::find(1);
        // $formEntries = $media->formEntries; // Collection of FormEntry models
    }

}
