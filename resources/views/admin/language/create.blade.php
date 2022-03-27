<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{{__('add new')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form id="ajaxForm" class="" action="{{route('admin.language.store')}}" method="POST">
          @csrf
          <input type="hidden" id="image" name="" value="">
          <div class="form-group">
            <label for="">{{__('Name')}} **</label>
            <input type="text" class="form-control" name="name" placeholder="{{__('Name')}}..." value="">
            <p id="errname" class="mb-0 text-danger em"></p>
          </div>
          <div class="form-group">
            <label for="">{{__('code')}} **</label>
            <input type="text" class="form-control" name="code" placeholder="{{__('code')}}..." value="">
            <p id="errcode" class="mb-0 text-danger em"></p>
          </div>
          <div class="form-group">
            <label for="">{{__('Direction')}} **</label>
            <select name="direction" class="form-control">
                <option value="" selected disabled>{{__('Direction')}}...</option>
                <option value="0">{{__('LTR (Left to Right)')}}</option>
                <option value="1">{{__('RTL (Right to Left)')}}</option>
            </select>
            <p id="errdirection" class="mb-0 text-danger em"></p>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
        <button id="submitBtn" type="button" class="btn btn-primary">{{__('Submit')}}</button>
      </div>
    </div>
  </div>
</div>
