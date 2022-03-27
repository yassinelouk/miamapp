<div class="main-header">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark2">

      <a href="{{route('front.index')}}" class="logo" target="_blank">
        <img src="{{asset('assets/front/img/'.$bs->logo)}}" alt="navbar brand" class="navbar-brand" width="120">
      </a>
      <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon">
          <i class="icon-menu"></i>
        </span>
      </button>
      <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
      <div class="nav-toggle">
        <button class="btn btn-toggle
          @if(request()->routeIs('admin.pos') || request()->routeIs('admin.table.qrbuilder') || request()->routeIs('admin.qrcode'))
              sidenav-overlay-toggler
          @else
              toggle-sidebar
          @endif">
          <i class="icon-menu"></i>
        </button>
      </div>
    </div>
    <!-- End Logo Header -->

    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-expand-lg" data-background-color="dark">

      <div class="container-fluid">
        <div class="d-flex align-items-center mx-4" >
            <i class="far fa-sun fa-lg mr-2"></i>
            <label class="switch">
            <input type="checkbox">
            <span class="slider round"></span>
            </label>
            <i class="far fa-moon fa-lg ml-2"></i>
        </div>
        <form id="languageForm" action="" class='mr-2'>
            <select class="form-control form-control-md" onchange="document.getElementById('languageForm').setAttribute('action', '{{url('changelanguage')}}' + '/' + this.value + '/admin'); document.getElementById('languageForm').submit()">
                @foreach($langs as $lang)
                @if ($lang->rtl == 0)
                <option value="{{$lang->code}}" {{$currentLang->code == $lang->code ? 'selected' : ''}}>{{$lang->name}}</option>
                @endif
                @endforeach
            </select>
        </form>
        <ul class="navbar-nav topbar-nav ml-md-auto align-items-center mb-2">

          <li class="nav-item dropdown hidden-caret">
            <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
              <div class="avatar-sm">
                @if (!empty(Auth::guard('admin')->user()->image))
                  <img src="{{asset('assets/admin/img/propics/'.Auth::guard('admin')->user()->image)}}" alt="..." class="avatar-img rounded-circle contain">
                @else
                  <img src="{{asset('assets/admin/img/propics/blank_user.jpg')}}" alt="..." class="avatar-img rounded-circle contain">
                @endif
              </div>
            </a>
            <ul class="dropdown-menu dropdown-user animated fadeIn">
              <div class="dropdown-user-scroll scrollbar-outer">
                <li>
                  <div class="user-box">
                    <div class="avatar-lg">
                      @if (!empty(Auth::guard('admin')->user()->image))
                        <img src="{{asset('assets/admin/img/propics/'.Auth::guard('admin')->user()->image)}}" alt="..." class="avatar-img rounded contain">
                      @else
                        <img src="{{asset('assets/admin/img/propics/blank_user.jpg')}}" alt="..." class="avatar-img rounded contain">
                      @endif
                    </div>
                    <div class="u-text">
                      <h4>{{Auth::guard('admin')->user()->first_name}}</h4>
                      <p class="text-muted">{{Auth::guard('admin')->user()->email}}</p><a href="{{route('admin.editProfile')}}" class="btn btn-xs btn-secondary btn-sm">Edit Profile</a>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="{{route('admin.editProfile')}}">{{__('Edit Profile')}}</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="{{route('admin.changePass')}}">{{__('Change Password')}}</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="{{route('admin.logout')}}">{{__('Logout')}}</a>
                </li>
              </div>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
    <!-- End Navbar -->
  </div>
