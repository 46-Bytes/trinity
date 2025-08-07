<?php

namespace App\Http\Controllers;

use App\Enums\FileType;
use App\Models\Diagnostic;
use App\Models\FormEntry;
use App\Models\Media;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller {
    private int $maxFileSize = 10 * 1024 * 1024;

    public function index() {
        // Fetch tasks for the current user
        $files = Media::where('user_id', Auth::id())->get();

        return view('files.index', compact('files'));
    }

    /**
     * Download the latest diagnostic as a PDF.
     */
    public function downloadDiagnostic(int $diagnosticId) {

        // Fetch the most recent diagnostic for the current user
        $diagnostic = Diagnostic::find($diagnosticId);

        if (!$diagnostic || $diagnostic->status !== Diagnostic::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'No diagnostic found.');
        }

        $formEntry = FormEntry::find($diagnostic->form_entry_id);
        $questionAnswerData = json_decode($formEntry->getQAJson(), true);

        // Generate the PDF view
        $pdf = Pdf::loadView('diagnostic.pdf', compact('diagnostic', 'questionAnswerData'));

        return $pdf->download($diagnostic->getDownloadFilename());
    }

    /**
     * Download the latest diagnostic as a PDF.
     */
    public function downloadLatestDiagnostic() {
        // Fetch the most recent diagnostic for the current user
        $diagnostic = Diagnostic::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$diagnostic || $diagnostic->status !== Diagnostic::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'No completed diagnostic found.');
        }

        $formEntry = FormEntry::find($diagnostic->form_entry_id);
        $questionAnswerData = json_decode($formEntry->getQAJson(), true);

        // Generate the PDF view
        $pdf = Pdf::loadView('diagnostic.pdf', compact('diagnostic', 'questionAnswerData'));

        return $pdf->download($diagnostic->getDownloadFilename());
    }

    /**
     * Handle file uploads from a form and upload to GPT.
     */
    public function formFilesUpload(Request $request): JsonResponse {
        $files = $request->file('files');
        $uploadedFiles = [];

        if (!$files) {
            return response()->json(['success' => false, 'message' => 'No files uploaded.']);
        }

        foreach ($files as $file) {
            $request->validate([
                'files.*' => 'mimes:pdf,doc,docx,jpg,jpeg,png,csv,xlsx,txt|max:' . $this->maxFileSize,
            ]);

            $media = Media::upload($file);

            if ($media) {
                $uploadedFiles[] = [
                    'id' => $media->id,
                    'name' => $media->file_name,
                    'url' => route('download.file', ['id' => $media->id]),
                    'gpt_file_id' => $media->gpt_file_id,
                ];
            }
        }

        return response()->json(['success' => true, 'files' => $uploadedFiles]);
    }

    /**
     * Handle single file upload and save metadata.
     */
    public function upload(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,csv,txt|max:' . $this->maxFileSize,
            'description' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $description = $request->input('description');

        $media = Media::upload($file, $description);

        if ($media) {
            return redirect()->back()->with('success', 'File uploaded successfully!');
        }

        return redirect()->back()->with('error', 'Failed to upload file. Please try again.');
    }

    public function downloadFileById(int $id): BinaryFileResponse|RedirectResponse {
        $userId = Auth::id(); // Ensure the user is authorized to access this file

        // Retrieve the media record by ID
        $media = Media::where('id', $id)->where('user_id', $userId)->first();

        if (!$media) {
            return redirect()->back()->with('error', 'File not found or access denied.');
        }

        // Handle download for diagnostics
        if ($media->file_type == FileType::Diagnostic->value) {
            return redirect($media->file_path);
        }
        // Serve the file securely
        $absolutePath = Storage::disk('user_files')->path($media->file_path);

        if (!file_exists($absolutePath)) {
            return redirect()->back()->with('error', 'File not found on the server.');
        }

        return response()->download($absolutePath, $media->file_name);
    }

}
