@extends('admin.layout')

@php
$selLang = \App\Models\Language::where('code', request()->input('language'))->first();
@endphp
@if(!empty($selLang) && $selLang->rtl == 1)
@section('styles')
<style>
    form:not(.modal-form) input,
    form:not(.modal-form) textarea,
    form:not(.modal-form) select,
    select[name='language'] {
        direction: rtl;
    }
    form:not(.modal-form) .note-editor.note-frame .note-editing-area .note-editable {
        direction: rtl;
        text-align: right;
    }
</style>
@endsection
@endif

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{__('Jobs')}}</h4>
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
        <a href="#">{{__('Career Page')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('Applications')}}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card-title d-inline-block">{{__('Applications')}}</div>
                </div>
                <div class="col-lg-3">
                    @if (!empty($langs))
                        <select name="language" class="form-control" onchange="window.location='{{url()->current() . '?language='}}'+this.value">
                            <option value="" selected disabled>{{__('Select a Language')}}</option>
                            @foreach ($langs as $lang)
                                <option value="{{$lang->code}}" {{$lang->code == request()->input('language') ? 'selected' : ''}}>{{$lang->name}}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
                    <button class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete" data-href="{{route('admin.job.bulk.delete')}}"><i class="flaticon-interface-5"></i> {{__('Delete')}}</button>
                </div>
            </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($jobs_applications) == 0)
                <h3 class="text-center">{{__('NO JOB FOUND')}}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3" id="basic-datatables">
                    <thead>
                      <tr>
                        <th scope="col">
                            <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col">{{__('Full Name')}}</th>
                        <th scope="col">{{__('Subject')}}</th>
                        <th scope="col">{{__('Title')}}</th>
                        <th scope="col">{{__('Details')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($jobs_applications as $key => $job_app)
                        <tr>
                          <td>
                            <input type="checkbox" class="bulk-check" data-val="{{$job_app->id}}">
                          </td>
                          <td>{{strlen(convertUtf8($job_app->full_name)) > 70 ? convertUtf8(substr($job_app->full_name, 0, 70)) . '...' : convertUtf8($job_app->full_name)}}</td>
                          <td>
                              {{convertUtf8($job_app->subject)}}
                          </td>
                          <td>{{$job_app->slug}}</td>
                          <td class="d-flex align-items-center" >
                            <a class="btn btn-secondary btn-sm" href="{{route('admin.job.applications.details', $job_app->id)}}">
                            <span class="btn-label">
                              <i class="fas fa-edit"></i>
                            </span>
                            {{__('Details')}}
                            </a>
                            <form class="deleteform d-inline-block" action="{{route('admin.job.applications.delete')}}" method="post">
                              @csrf
                              <input type="hidden" name="job_app_id" value="{{$job_app->id}}">
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

      </div>
    </div>
  </div>
@endsection
