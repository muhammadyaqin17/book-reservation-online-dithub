@extends('layouts.admin')

@section('page-title', 'Edit Buku')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.books.index') }}">Buku</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Buku</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Buku <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $book->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="author" class="form-label">Penulis <span class="text-danger">*</span></label>
                            <input type="text" name="author" id="author" class="form-control @error('author') is-invalid @enderror" value="{{ old('author', $book->author) }}" required>
                            @error('author')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="isbn" class="form-label">ISBN</label>
                            <input type="text" name="isbn" id="isbn" class="form-control @error('isbn') is-invalid @enderror" value="{{ old('isbn', $book->isbn) }}">
                            @error('isbn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="publisher" class="form-label">Penerbit</label>
                            <input type="text" name="publisher" id="publisher" class="form-control @error('publisher') is-invalid @enderror" value="{{ old('publisher', $book->publisher) }}">
                            @error('publisher')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="published_year" class="form-label">Tahun Terbit</label>
                            <input type="number" name="published_year" id="published_year" class="form-control @error('published_year') is-invalid @enderror" value="{{ old('published_year', $book->published_year) }}" min="1900" max="{{ date('Y') + 1 }}">
                            @error('published_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="total_stock" class="form-label">Total Stok <span class="text-danger">*</span></label>
                            <input type="number" name="total_stock" id="total_stock" class="form-control @error('total_stock') is-invalid @enderror" value="{{ old('total_stock', $book->total_stock) }}" min="1" required>
                            @error('total_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="available_stock" class="form-label">Stok Tersedia <span class="text-danger">*</span></label>
                            <input type="number" name="available_stock" id="available_stock" class="form-control @error('available_stock') is-invalid @enderror" value="{{ old('available_stock', $book->available_stock) }}" min="0" required>
                            @error('available_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="pages" class="form-label">Jumlah Halaman</label>
                            <input type="number" name="pages" id="pages" class="form-control @error('pages') is-invalid @enderror" value="{{ old('pages', $book->pages) }}" min="1">
                            @error('pages')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="language" class="form-label">Bahasa</label>
                            <input type="text" name="language" id="language" class="form-control @error('language') is-invalid @enderror" value="{{ old('language', $book->language) }}">
                            @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $book->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Cover Saat Ini</label>
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" class="img-fluid rounded mb-2" style="max-height: 200px;">
                        @else
                            <div class="bg-light rounded p-4 text-center text-muted mb-2">
                                <i class="bi bi-image display-6"></i>
                                <p class="small mb-0">Tidak ada cover</p>
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="cover_image" class="form-label">Ganti Cover</label>
                        <input type="file" name="cover_image" id="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
                        @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $book->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
                <a href="{{ route('admin.books.index') }}" class="btn btn-secondary"><i class="bi bi-x me-1"></i>Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
