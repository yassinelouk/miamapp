<div class="modal fade" tabindex="-1" role="dialog" id="checkoutVariationModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
                <span>{{__('Thank you.')}}</span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>


        <div class="modal-body">
            <div class="modal-body-btns">
                    <div class="placeorder-button {{$rtl == 1 ? 'text-right' : 'text-left'}} mt-4">
                        <button class="main-btn" id="sendOrderBtn"><span
                            class="btn-title"><i class="fas fa-paper-plane"></i> {{ __('Checkout') }}</span></button>
                    </div>
                    <div class="placeorder-button {{$rtl == 1 ? 'text-right' : 'text-left'}} mt-4">
                        <button class="main-btn" type="submit" form="payment" id="placeOrderBtn"><span
                            class="btn-title"><i class="fas fa-receipt"></i> {{ __('Ask for the bill') }}</span></button>
                    </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="row" style="display: flex; align-items: center;">
                
            </div>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->


