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
<div class="card-body">
        <div class="card-header">
          <div class="card-title d-inline-block">{{__('Applications')}}: {{$job_app->full_name}}</div>
          <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.job.applications') . '?language=' . $currentLang->code}}">
            <span class="btn-label">
              <i class="fas fa-backward"></i>
            </span>
            {{__('back')}}
          </a>
        </div>
          <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">{{__('Full Name')}}</th>
                        <th scope="col">{{__('Subject')}}</th>
                        <th scope="col">{{__('Title')}}</th>
                        <th scope="col">{{__('Download CV')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                        <tr>
                          <td>{{strlen(convertUtf8($job_app->full_name)) > 70 ? convertUtf8(substr($job_app->full_name, 0, 70)) . '...' : convertUtf8($job_app->full_name)}}</td>
                          <td>
                              {{convertUtf8($job_app->subject)}}
                          </td>
                          <td>{{$job_app->slug}}</td>
                          <td>
                            <a download href="{{asset('assets/admin/cvs/') . $job_app->cv_name}}" class="btn btn-success btn-block" id="downloadCVbtn" onclick=""><i style="color:#fff;" class="fas fa-download"></i></a>
                          </td>
                        </tr>
                    </tbody>
                  </table>
                </div>
            </div>
          </div>
        </div>
        <div class="row">
		<div class="col-lg-12">
		  <div class="row row-card-no-pd">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<div class="card-head-row">
								<h4 class="card-title">{{__('Message')}}</h4>
							</div>
							<p class="card-category">
							{{convertUtf8($job_app->full_name)}}</p>
						</div>
						<div class="card-body">
				  <div class="row">
					  <div class="col-lg-12">
						                 {!! nl2br(replaceBaseUrl(convertUtf8($job_app->content))) !!}
					  </div>
				  </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	  </div>
@endsection
