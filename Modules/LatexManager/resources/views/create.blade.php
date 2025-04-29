@extends('adminlte::page')

@section('title', 'Create Latex Item')

@section('content_header')
    <h1>Create New Latex Item</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.latex-manager.store') }}" method="POST" id="latex-form">
                @csrf

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                    @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="5">{{ old('content') }}</textarea>
                    @error('content')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- Removed original latex_editor textarea --}}

                {{-- Editor and Preview Row --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>LaTeX Code</label>
                            <div id="editor-container" style="border: 1px solid #ced4da; border-radius: .25rem; height: 400px;"></div>
                            {{-- Hidden textarea to store editor content for form submission --}}
                            <textarea name="latex_editor_hidden" id="latex_editor_hidden" style="display: none;"></textarea>
                        </div>
                        <button type="button" id="compile-btn" class="btn btn-success mb-3">Compile & Preview</button>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Preview</label>
                            <div id="preview-container" style="border: 1px solid #ced4da; border-radius: .25rem; height: 445px; overflow: auto;">
                                <iframe id="preview-iframe" style="width: 100%; height: 100%; border: none;"></iframe>
                                <div id="preview-error" class="p-3 text-danger" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <button type="submit" class="btn btn-primary">Create Item</button>
                <a href="{{ route('admin.latex-manager.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@stop

@section('css')
    {{-- CodeMirror CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.14/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.14/theme/monokai.min.css"> {{-- Optional: Choose a theme --}}
    <style>
        .CodeMirror {
            height: 100%; /* Make CodeMirror fill its container */
            font-size: 14px;
        }
        #preview-container {
            background-color: #f8f9fa; /* Light background for preview area */
        }
    </style>
@stop

@section('js')
    {{-- CodeMirror JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.14/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.14/mode/stex/stex.min.js"></script> {{-- LaTeX mode --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize CodeMirror
            const editorContainer = document.getElementById('editor-container');
            const hiddenTextarea = document.getElementById('latex_editor_hidden');
            const initialContent = `{!! old('latex_editor', "\\\\documentclass{article}\n\\\\usepackage[utf8]{inputenc}\n\\\\usepackage{geometry}\n\\\\geometry{a4paper, margin=1in}\n\n\\\\title{My LaTeX Document}\n\\\\author{Your Name}\n\\\\date{\\\\today}\n\n\\\\begin{document}\n\n\\\\maketitle\n\n\\\\section{Introduction}\nThis is a sample document.\n\n\\\\end{document}") !!}`; // Default content or old input

            const editor = CodeMirror(editorContainer, {
                value: initialContent.replace(/\\\\/g, '\\'), // Unescape backslashes for editor
                mode: 'stex', // LaTeX mode
                theme: 'monokai', // Optional: Choose a theme
                lineNumbers: true,
                indentUnit: 4,
                tabSize: 4,
                indentWithTabs: false, // Use spaces instead of tabs
                lineWrapping: true
            });

            // Update hidden textarea on editor change for form submission
             editor.on('change', function() {
                hiddenTextarea.value = editor.getValue();
            });
            // Set initial value for hidden textarea
            hiddenTextarea.value = editor.getValue();


            // Compile Button Logic (AJAX call to be added later)
            const compileBtn = document.getElementById('compile-btn');
            const previewIframe = document.getElementById('preview-iframe');
            const previewError = document.getElementById('preview-error');

            compileBtn.addEventListener('click', function() {
                const latexCode = editor.getValue();
                previewError.style.display = 'none'; // Hide previous errors
                previewIframe.src = 'about:blank'; // Clear previous preview

                // Show loading state (optional)
                previewIframe.style.display = 'none';
                previewError.style.display = 'block';
                previewError.textContent = 'Compiling...';

                // Add AJAX call here to send `latexCode` to the backend
                fetch('{{ route("admin.latex-manager.compile") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json', // Important for Laravel validation errors
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value // Get CSRF token from the form
                    },
                    body: JSON.stringify({ content: latexCode })
                })
                .then(response => {
                    if (!response.ok) {
                        // Handle non-2xx responses (like validation errors or server errors)
                        return response.json().then(errData => {
                            throw new Error(errData.message || `HTTP error! status: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.pdf_url) {
                        previewIframe.src = data.pdf_url;
                        previewIframe.style.display = 'block';
                        previewError.style.display = 'none';
                    } else {
                        previewIframe.style.display = 'none';
                        previewError.textContent = 'Error: ' + (data.message || 'Compilation failed.');
                        previewError.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Compile Error:', error);
                    previewIframe.style.display = 'none';
                    previewError.textContent = 'Error: ' + error.message; // Corrected: Removed duplicate catch
                    previewError.style.display = 'block';
                });
            });

            // Update hidden textarea before form submission
            const form = document.getElementById('latex-form');
            form.addEventListener('submit', function() {
                 hiddenTextarea.value = editor.getValue();
            });
        });
    </script>
@stop
