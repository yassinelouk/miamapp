@extends('admin.layout')

@section('content')

<div class="page-header">
    <h4 class="page-title">Ajouter des images à la galerie</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{route('admin.dashboard')}}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Pictures gallery')}}</a>
      </li>
    </ul>
</div>

<form action="{{ route('admin.gallery.storePictures') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
            <div class="col-12 mb-2">
                <label for="pictures"><strong>{{__('Importer des images')}}</strong></label>
            </div>
                <input type="file" name="pictures[]" id="pictures" accept="image/*" class="form-control image" multiple required>
            <p id="errfeature_image" class="mb-0 text-danger em"></p>
            </div>
        </div>
    </div>
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-success">{{__('Importer les images')}}</button>
    </div>
</form>

<h2 class="mt-2">Galerie actuelle</h2>

<section style="background-color: #f2f2f2; padding: 20px; border-radius: 10px; margin-top: 30px;">
    <div class="row" style="display: flex; justify-content: center; align-items: top; height: 450px; overflow: auto;">
        @foreach ($pictures as $picture)
            <img style="padding: 0; margin: 15px 5px; object-fit: contain; width: 300px;" src="{{ asset('assets/front/img/product/featured/gallery/' . $picture) }}" alt="">
        @endforeach
    </div>
</section>

@endsection
