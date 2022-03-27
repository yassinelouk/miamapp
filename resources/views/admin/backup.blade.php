@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{__('Backup')}}</h4>
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
        <a href="#">{{__('Backup')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{__('Database Backup')}}</div>
          <form class="d-inline-block float-right" action="{{route('admin.backup.store')}}" method="post">
            @csrf
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('add new')}}</button>
          </form>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              <div class="alert alert-warning normal" role="alert">

                  Créer une sauvegarde] pour sauvegarder manuellement votre base de données. Les sauvegardes sont stockées dans le dossier [core/storage/app/public/] et peuvent être téléchargées à partir de la liste ci-dessous (mais ne sont pas accessibles publiquement).
 </div>

              @if (count($backups) == 0)
                <h3 class="text-center">{{__('NO BACKUP FOUND')}}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">{{__('Date')}} & {{__('Time')}}</th>
                        <th scope="col">{{__('Action')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($backups as $key => $backup)
                        <tr>
                          <td>{{$backup->created_at}}</td>
                          <td class="d-flex align-items-center" >
                            <form class="d-inline-block" action="{{route('admin.backup.download', $backup->id)}}" method="post">
                              @csrf
                              <input type="hidden" name="filename" value="{{$backup->filename}}">
                              <button type="submit" class="btn btn-secondary btn-sm">
                                <span class="btn-label">
                                  <i class="fas fa-arrow-alt-circle-down"></i>
                                </span>
                                {{__('Download')}}
                              </button>
                            </form>
                            <form class="d-inline-block" action="{{route('admin.backup.delete', $backup->id)}}" method="post">
                              @csrf
                              <button type="submit" class="btn btn-danger btn-sm deletebtn">
                                <span class="btn-label">
                                  <i class="fas fa-trash"></i>
                                </span>
                                {{__('Delete')}}
                              </button>
                            </form>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="d-inline-block mx-auto">
              {{$backups->links()}}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
