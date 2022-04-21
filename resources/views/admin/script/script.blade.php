@extends('admin.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">Importer vos produits a partir d'un fichier excel</h4>
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
        <a href="#">{{__('Import script')}}</a>
      </li>
    </ul>
  </div>
    <a href="{{route('admin.indexScriptshow')}}">
        <span class="btn btn-info btn-sm editbtn">{{__('Executer le Cron')}}</span>
    </a>

    <form  class="" action="{{ route('admin.indexScriptStore') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-12">
              <div class="form-group">
                <div class="col-12 mb-2">
                  <label for="image"><strong>{{__('Importer votre fichier excel')}}</strong></label>
                </div>
                <input type="file" name="csv_file" id="csv_file" class="form-control image">
                <p id="errfeature_image" class="mb-0 text-danger em"></p>
              </div>
            </div>
        </div>
        <div class="col-12 text-center">
            <button type="submit" id="submitBtn" class="btn btn-success">{{__('Importer le fichier')}}</button>
        </div>
    </form>
@endsection
