@extends('admin.layout')

@section('css')
   <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endsection

@section('content')
<div class="page-header">
   <h4 class="page-title">{{__('Items Management')}}: {{__('add new')}}</h4>
   <ul class="breadcrumbs">
      <li class="nav-home">
         <a href="#">
         <i class="flaticon-home"></i>
         </a>
      </li>
      <li class="separator">
         <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
         <a href="#">{{__('Items Management')}}</a>
      </li>
      <li class="separator">
         <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
         <a href="#">{{__('add new')}}</a>
      </li>
   </ul>
</div>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header">
            <div class="card-title d-inline-block">{{__('add new')}}</div>
            <a class="btn btn-info btn-sm float-right d-inline-block" href="{{route('admin.product.index') . '?language=' . request()->input('language')}}">
            <span class="btn-label">
            <i class="fas fa-backward"></i>
            </span>
            Back
            </a>
         </div>
         <div class="card-body pt-5 pb-5">
            <div class="row">
               <div class="col-lg-6 offset-lg-3">
                  {{-- Slider images upload start --}}
                  <!--<div class="px-2">-->
                  <!--   <label for="" class="mb-2"><strong>{{__('Slider')}}</strong></label>-->
                  <!--   <form action="{{route('admin.product.sliderstore')}}" id="my-dropzone" enctype="multipart/formdata" class="dropzone create">-->
                  <!--      @csrf-->
                  <!--      <div class="fallback">-->
                  <!--         <input name="file" type="file" multiple  />-->
                  <!--      </div>-->
                  <!--   </form>-->
                  <!--   <p class="em text-danger mb-0" id="errslider_images"></p>-->
                  <!--</div>-->

                  {{-- Featured image upload end --}}
                  <form id="sendForm" class="" action="{{route('admin.product.store')}}" method="post" enctype="multipart/form-data">
                     @csrf
                     <div class="row">
                        <div class="col-lg-12">
                          <div class="form-group">
                            <div class="col-12 mb-2">
                              <label for="image"><strong>{{__('Image')}}</strong></label>
                            </div>
                            <div class="col-md-12 showImage mb-3">
                              <img id="img-display" src="{{asset('assets/admin/img/noimage.jpg')}}" alt="..." class="img-thumbnail">
                            </div>
                            <div class="d-flex">
                              <input type="file" name="feature_image" id="image" class="form-control image col-9">
                              <button type="button" class="btn btn-primary col-3 d-inline-block" data-toggle="modal" data-target="#gallery">{{__('Choose from gallery')}}</button>
                            </div>
                            <p id="errfeature_image" class="mb-0 text-danger em"></p>
                          </div>
                        </div>
                      </div>
                     <div id="sliders"></div>
                     <div class="row">
                        <div class="col-lg-6">
                           <div class="form-group">
                              <label for="">{{__('Language')}} **</label>
                              <select id="language" name="language_id" class="form-control">
                                 <option value="" selected disabled>{{__('Select a Language')}}</option>
                                 @foreach ($langs as $lang)
                                 <option value="{{$lang->id}}">{{$lang->name}}</option>
                                 @endforeach
                              </select>
                              <p id="errlanguage_id" class="mb-0 text-danger em"></p>
                           </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                               <label for="">{{__('Status')}} **</label>
                               <select class="form-control " name="status">
                                  <option value="" selected disabled>{{__('Status')}}</option>
                                  <option value="1">{{__('Show')}}</option>
                                  <option value="0">{{__('Hide')}}</option>
                               </select>
                               <p id="errstatus" class="mb-0 text-danger em"></p>
                            </div>
                         </div>
                     </div>
                     <div class="row">
                        <div class="col-lg-6">
                           <div class="form-group">
                              <label for="">{{__('Title')}} **</label>
                              <input type="text" class="form-control" name="title" value="" placeholder="{{__('Title')}}...">
                              <p id="errtitle" class="mb-0 text-danger em"></p>
                           </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                               <label for="category">{{__('Category')}} **</label>
                               <select  class="form-control categoryData" name="category_id" id="category">
                                  <option value="" selected disabled>{{__('Category')}}</option>
                                  @foreach ($categories as $categroy)
                                  <option value="{{$categroy->id}}">{{$categroy->name}}</option>
                                  @endforeach
                               </select>
                               <p id="errcategory_id" class="mb-0 text-danger em"></p>
                            </div>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-lg-6">
                           <div class="form-group">
                              <label for=""> {{__('Current Price')}} ({{$be->base_currency_text}})**</label>
                              <input type="number" class="form-control ltr" name="current_price" value=""  placeholder="{{__('Current Price')}}...">
                              <p id="errcurrent_price" class="mb-0 text-danger em"></p>
                           </div>
                        </div>
                        <div class="col-lg-6">
                           <div class="form-group">
                              <label for="">{{__('Previous Price')}} ({{$be->base_currency_text}})</label>
                              <input type="number" class="form-control ltr" name="previous_price" value="" placeholder="{{__('Previous Price')}}...">
                              <p id="errprevious_price" class="mb-0 text-danger em"></p>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                              <label for="">{{__('Fidelity points')}}</label>
                              <input type="number" class="form-control ltr" name="fidelity_score" value="" placeholder="{{__('Fidelity points')}}...">
                           </div>
                        </div>
                     </div>

                     <div class="row">

                        <div class="col-lg-12">
                           <div class="form-group">
                              <label for="summary">{{__('Description')}}</label>
                              <textarea name="summary" id="summary" class="form-control" rows="4" placeholder="{{__('Summary')}}..."></textarea>
                              <p id="errsummary" class="mb-0 text-danger em"></p>
                           </div>
                        </div>
                     </div>
                     <!--<div class="row">-->
                     <!--   <div class="col-lg-12">-->
                     <!--      <div class="form-group">-->
                     <!--         <label for="">{{__('Description')}}</label>-->
                     <!--         <textarea class="form-control summernote" name="description" placeholder="{{__('Description')}}..." data-height="300"></textarea>-->
                     <!--         <p id="errdescription" class="mb-0 text-danger em"></p>-->
                     <!--      </div>-->
                     <!--   </div>-->
                     <!--</div>-->


                     <div id="app">
                        {{-- Variations Start --}}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="" class="d-block mb-2">{{__('Variations')}}</label>
                                    <button class="btn btn-primary" @click="addVariant">{{__('add new')}}</button>
                                </div>
                            </div>
                        </div>


                        <div class="row" v-for="(variant, index) in variants" :key="variant.uniqid">
                            <div class="col-lg-6">
                                <div class="form-group">
                                <label for="">{{__('Name')}}</label>
                                    <input name="variant_names[]" type="text" class="form-control" placeholder="eg. Petit, Moyen, Grand etc...">
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label for="">{{__('Additional Price')}} ({{$be->base_currency_text}})</label>
                                    <input name="variant_prices[]" type="text" class="form-control ltr" autocomplete="off" value="0">
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <button class="btn btn-danger text-white mt-4" @click="removeVariant(index)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Variations End --}}

                        {{-- Addons Start --}}
                        <div class="row mt-2">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="" class="d-block mb-2">{{__("Add On's")}}</label>
                                    <button class="btn btn-primary" @click="addAddOn">{{__('add new')}}</button>
                                </div>
                            </div>
                        </div>


                        <div class="row" v-for="(addon, index) in addons" :key="addon.uniqid">
                            <div class="col-lg-6">
                                <div class="form-group">
                                <label for="">{{__('Name')}}</label>
                                    <input name="addon_names[]" type="text" class="form-control" placeholder="eg. Fromage, Oignons, Sauce etc...">
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label for="">{{__('Additional Price')}} ({{$be->base_currency_text}})</label>
                                    <input name="addon_prices[]" type="text" class="form-control ltr" autocomplete="off" value="0">
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <button class="btn btn-danger text-white mt-4" @click="removeAddOn(index)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Addons End --}}
                     </div>
                     <input id="gallery_img" type="text" name="gallery_img" hidden value="">
                  </form>
               </div>
            </div>
         </div>
         <div class="card-footer">
            <div class="form">
               <div class="form-group from-show-notify row">
                  <div class="col-12 text-center">
                     <button type="submit" class="btn btn-success" onclick="sendForm()">{{__('Submit')}}</button>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal fade" id="gallery" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 80%;">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">{{__('Choose an image')}}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                    <section style="background-color: #f2f2f2; padding: 20px; border-radius: 10px; margin-top: 30px; width: 100%;">
                     <div class="row" style="display: flex; justify-content: center; align-items: center; height: 700px; overflow: auto;">
                         @foreach ($pictures as $picture)
                           <div onclick="selectElement(this)" style="margin: 25px 10px; padding: 5px;">
                              <img data-picture-name="{{ $picture }}" style="object-fit: contain; width: 300px;" src="{{ asset('assets/front/img/product/featured/gallery/' . $picture) }}" alt="" >
                           </div>

                         @endforeach
                     </div>
                 </section>
                </div>
                <div class="modal-footer" style="justify-content: space-between;">
                  <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                    <button class="btn btn-primary" onclick="getImgPath()" data-dismiss="modal">{{__('Save')}}</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
   </div>
</div>
@endsection

@section('variables')
<script>
    "use strict";
    var storeUrl = "{{route('admin.product.sliderstore')}}";
    var removeUrl = "{{route('admin.product.sliderrmv')}}";
</script>
@endsection

@section('js')
   <script>
      function selectElement(img) {
         $('.selectedPicture').removeClass('selectedPicture');
         $(img).addClass('selectedPicture');
      }

      function getImgPath() {
         $("#img-display").attr('src', $(document.querySelector('.selectedPicture img')).attr('src'));
         $("#gallery_img").val( $(document.querySelector('.selectedPicture img')).attr('data-picture-name') );
      }

      function sendForm() {
         $('#sendForm').trigger('submit');
      }
   </script>
@endsection

@section('vuescripts')
    <script>
        let app = new Vue({
            el: '#app',
            data() {
                return  {
                    variants: [],
                    addons: []
                }
            },
            methods: {
                addVariant() {
                    let n = Math.floor(Math.random() * 11);
                    let k = Math.floor(Math.random() * 1000000);
                    let m = String.fromCharCode(n) + k;
                    this.variants.push({uniqid: m});
                },
                removeVariant(index) {
                    this.variants.splice(index, 1);
                },
                addAddOn() {
                    let n = Math.floor(Math.random() * 11);
                    let k = Math.floor(Math.random() * 1000000);
                    let m = String.fromCharCode(n) + k;
                    this.addons.push({uniqid: m});
                },
                removeAddOn(index) {
                    this.addons.splice(index, 1);
                }
            }
        });
    </script>
@endsection
