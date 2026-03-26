<?php

namespace App\Http\Controllers;

use App\Models\OrganizationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationDocumentController extends Controller
{
    /**
     * Display all documents for members
     */
    public function index()
    {
        $documents = OrganizationDocument::with('uploader')
            ->orderByDesc('created_at')
            ->paginate(12);

        $documentsByType = OrganizationDocument::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');

        return view('documents.index', compact('documents', 'documentsByType'));
    }

    /**
     * Display documents by type
     */
    public function byType($type)
    {
        $validTypes = ['deed', 'resolution', 'notice', 'report', 'other'];
        if (!in_array($type, $validTypes)) {
            return redirect()->route('documents.index');
        }

        $documents = OrganizationDocument::with('uploader')
            ->ofType($type)
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('documents.index', compact('documents', 'type'));
    }

    /**
     * Show single document
     */
    public function show(OrganizationDocument $document)
    {
        return view('documents.show', compact('document'));
    }

    /**
     * Download document
     */
    public function download(OrganizationDocument $document)
    {
        $path = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($path)) {
            return back()->with('error', 'ফাইল খুঁজে পাওয়া যায়নি');
        }

        return response()->download($path, $document->title . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION));
    }

    // ========== Admin Methods ==========

    /**
     * Admin document list
     */
    public function adminIndex()
    {
        $documents = OrganizationDocument::with('uploader')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.documents.index', compact('documents'));
    }

    /**
     * Create document form
     */
    public function create()
    {
        return view('admin.documents.create');
    }

    /**
     * Store new document
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:deed,resolution,notice,report,other',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ], [
            'title.required' => 'Title is required',
            'type.required' => 'Please select document type',
            'file.required' => 'Please upload a file',
            'file.max' => 'File size cannot exceed 10 MB',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        OrganizationDocument::create([
            'title' => $request->title,
            'title_bn' => $request->title_bn,
            'description' => $request->description,
            'type' => $request->type,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document uploaded successfully!');
    }

    /**
     * Edit document form
     */
    public function edit(OrganizationDocument $document)
    {
        return view('admin.documents.edit', compact('document'));
    }

    /**
     * Update document
     */
    public function update(Request $request, OrganizationDocument $document)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:deed,resolution,notice,report,other',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        $data = [
            'title' => $request->title,
            'title_bn' => $request->title_bn,
            'description' => $request->description,
            'type' => $request->type,
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            Storage::disk('public')->delete($document->file_path);

            // Upload new file
            $file = $request->file('file');
            $data['file_path'] = $file->store('documents', 'public');
            $data['file_type'] = $file->getClientOriginalExtension();
            $data['file_size'] = $file->getSize();
        }

        $document->update($data);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document updated successfully!');
    }

    /**
     * Delete document
     */
    public function destroy(OrganizationDocument $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document deleted successfully!');
    }
}
