<!-- Modal -->
<div class="modal fade" id="gatewaysModal{{$sm->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">{{__('Payment Gateways')}} ({{__($sm->name)}})</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form action="{{route('admin.product.servingMethodGateways')}}" method="POST" id="gatewaysForm{{$sm->id}}">
                @csrf
                <input type="hidden" name="serving_method" value="{{$sm->id}}">
                <div class="form-group">
                    <label class="form-label">{{__('Select the Offline Gateways that should be active for')}} <strong class="text-warning">{{__($sm->name)}}</strong></label>
                    <div class="selectgroup selectgroup-pills">
                        @foreach ($ogateways as $ogateway)
                            <label class="selectgroup-item">
                                @php
                                    $gateways = json_decode($sm->gateways, true);
                                @endphp
                                <input type="checkbox" name="gateways[]" value="{{$ogateway->id}}" class="selectgroup-input" {{is_array($gateways) && in_array($ogateway->id, $gateways) ? 'checked' : ''}}>
                                <span class="selectgroup-button">{{__($ogateway->name)}}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

            </form>
        </div>
        <div class="modal-footer justify-content-center">
            <button type="submit" class="btn btn-success" form="gatewaysForm{{$sm->id}}">{{__('Submit')}}</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('Close')}}</button>
        </div>
      </div>
    </div>
  </div>
