@extends('admin.layout')

@section('js')
  <script>
    function showInput(el) {
      $($(el).parent()[0].children[1]).css("display", "flex");
    }

    function hideInput(el) {
      $($(el).parent()[0]).css("display", "none");
      $($(el).parent()[0].children[0]).val("");
    }

    function remainingAmount(el, order_number) {
      let card = $(el).parent().parent().parent().children();
      let amount =
          parseFloat(card[6].children[1].children[1].innerText) -
              $($(el).parent()[0].children[0]).val() <
          0
              ? 0
              : parseFloat(card[6].children[1].children[1].innerText) -
                $($(el).parent()[0].children[0]).val();
      card[6].children[1].children[1].innerText = amount.toFixed(2);
      $.ajax({
          type: "POST",
          url: "{{ route('admin.sessionVariable') }}",
          data: {
              _token: $('meta[name="csrf-token"]').attr("content"),
              var_name: order_number,
              amount: amount,
          },
          success: function (response) {
              console.log(response);
          },
          error: function (response) {
              alert("Une erreur est survenue");
              console.log(response);
          },
      });
      $($(el).parent()[0].children[0]).val("");
    }
  </script>
@endsection

@section('content')
  <div class="mt-2 mb-4">
    <h2 class="text-white pb-2">{{__('Welcome Back')}}, {{Auth::guard('admin')->user()->first_name}} {{Auth::guard('admin')->user()->last_name}}!</h2>
  </div>
  <div class="row">

		<div class="col-sm-6 col-md-3">
			<div class="card card-stats card-primary card-round">
				<div class="card-body ">
					<div class="row">
						<div class="col-5">
							<div class="icon-big text-center">
								<i class="fas fa-utensils"></i>
							</div>
						</div>
						<div class="col-7 col-stats">
							<div class="numbers">
								<p class="card-category">{{__('Products')}}</p>
								<h4 class="card-title">{{$currentLang->products()->count()}}</h4>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-6 col-md-3">
			<div class="card card-stats card-info card-round">
				<div class="card-body ">
					<div class="row">
						<div class="col-5">
							<div class="icon-big text-center">
								<i class="fas fa-users"></i>
							</div>
						</div>
						<div class="col-7 col-stats">
							<div class="numbers">
								<p class="card-category">{{__('Registered Customers')}}</p>
								<h4 class="card-title">{{App\Models\User::count()}}</h4>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-md-3">
			<div class="card card-stats card-warning card-round">
				<div class="card-body ">
					<div class="row">
						<div class="col-5">
							<div class="icon-big text-center">
							    <i class="fas fa-shopping-cart"></i>
							</div>
						</div>
						<div class="col-7 col-stats">
							<div class="numbers">
								<p class="card-category">{{__('Orders')}} </p>
								<h4 class="card-title">{{$orders_count}}</h4>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-md-3">
			<div class="card card-stats card-success card-round">
				<div class="card-body ">
					<div class="row">
						<div class="col-5">
							<div class="icon-big text-center">
								<i class="fas fa-money-bill-wave"></i>
							</div>
						</div>
						<div class="col-7 col-stats">
							<div class="numbers">
								<p class="card-category">Chiffre d'affaires </p>
								<h4 class="card-title">  {{$orders_total}} € </h4>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

  <div class="card">
    <div class="card-header">
      <div class="row test">

        <div class="col-lg-12 d-flex flex-wrap justify-content-between">
          <div class="btn-group d-flex flex-wrap justify-content-center mb-2" role="group">
            <a href="{{route('admin.dashboard',['state'=>0 , 'type'=>0])}}" type="button" class="mb-1 btn {{ ($state == "0" && $type == "0")? 'btn-warning' : 'btn-outline-warning' }}">Nouvelles commandes boissons</a>
            <a href="{{route('admin.dashboard',['state'=>0 , 'type'=>1])}}" type="button" class="mb-1 btn {{ ($state == "0" && $type == "1")? 'btn-warning' : 'btn-outline-warning' }}">Nouvelles commandes plats</a>
          </div>
          <div class="btn-group d-flex flex-wrap justify-content-center mb-2" role="group">
            <a href="{{route('admin.dashboard',['state'=>1 , 'type'=>0])}}" type="button" class="mb-1 btn {{ ($state == "1" && $type == "0")? 'btn-success' : 'btn-outline-success' }}">Commandes Traitées boissons</a>
            <a href="{{route('admin.dashboard',['state'=>1 , 'type'=>1])}}" type="button" class="mb-1 btn {{ ($state == "1" && $type == "1")? 'btn-success' : 'btn-outline-success' }}">Commandes Traitées plats</a>
          </div>
          <div class="btn-group d-flex flex-wrap justify-content-center mb-2" role="group">
            <a href="{{route('admin.dashboard',['state'=>2])}}" type="button" class="btn btn{{ ($state == "2")? '-outline' : '' }}-secondary">Commandes Finalisées</a>
          </div>
        </div>
      </div>
      <div class="test2" >
          <a href="{{route('admin.dashboard',['state'=>0 , 'type'=>0])}}" type="button" class="mb-2 btn {{ ($state == "0" && $type == "0")? 'btn-warning' : 'btn-outline-warning' }}">Nouvelles commandes boissons</a>
          <a href="{{route('admin.dashboard',['state'=>0 , 'type'=>1])}}" type="button" class="mb-2 btn {{ ($state == "0" && $type == "1")? 'btn-warning' : 'btn-outline-warning' }}">Nouvelles commandes plats</a>
          <a href="{{route('admin.dashboard',['state'=>1 , 'type'=>0])}}" type="button" class="mb-2 btn {{ ($state == "1" && $type == "0")? 'btn-success' : 'btn-outline-success' }}">Commandes Traitées boissons</a>
          <a href="{{route('admin.dashboard',['state'=>1 , 'type'=>1])}}" type="button" class="mb-2 btn {{ ($state == "1" && $type == "1")? 'btn-success' : 'btn-outline-success' }}">Commandes Traitées plats</a>
          <a href="{{route('admin.dashboard',['state'=>2])}}" type="button" class="btn btn{{ ($state == "2")? '-outline' : '' }}-secondary">Commandes Finalisées</a>
      </div>
    </div>

    <div class="card-body">
      <div class="row">
           <div class="input-group m-3" style="margin-top:0;display:flex;flex-wrap:wrap;justify-content:center">
              <input id="table_number" type="number" value="{{ isset($_GET['table']) ? $_GET['table'] : ''}}" class="form-control" placeholder="Numéro de table" style="max-width: 200px;" aria-describedby="basic-addon2">
              <div class="input-group-append">
                <button class="btn btn-dark" onclick="filterOrders()" type="button" style=" border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important; ">Filtrer par table</button>
                 <a href={{route('admin.dashboard',['state'=>$state , 'type'=>$type])}} class="btn btn-success" type="button" style=" border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; margin-left:0 !important ">Tous</a>

              </div>
            </div>
          @if (isset($sub_orders) && count($sub_orders) == 0)
            <h3 class="text-center p-4">{{__('NO ORDER FOUND')}}</h3>
            @elseif($state == 2 )
             @foreach($orders as $order)
              @if(count($order->orderitems)>0 && ( !isset($_GET['table']) || ( isset($_GET['table']) && strcmp($order->table_number,$_GET['table']) == 0) ) )
              <div class="card m-2 order">
                <div class="card-body">
                  <h5 class="card-title">
                  {{__('Order')}} #{{$order->order_number}}
                  <br> <small style=" color: #979da1; "> <i class="fa fa-clock"></i> {{$order->updated_at->format('d/m/Y  h:m')}}</small>
                  </h5> <br>

                  <p class="card-text">
                       <b>Table : {{$order->table_number}}</b>
                       </p>

                  <ul class="list-group list-group-flush mb-2">
                    @foreach($order->orderitems as $item)
                    <li class="list-group-item"> {{ $item->product->title }}
                       <b class="mx-2"> x{{ $item->qty }}  </b>
                       <br>
                          @php
                              $variations = json_decode($item->variations, true);
                          @endphp
                          @if (!empty($variations))
                            <strong class="ml-3">Variation:</strong> {{$variations["name"]}}
                            <br>
                          @endif
                          @php
                              $addons = json_decode($item->addons, true);
                          @endphp
                          @if (!empty($addons))
                            <strong class="ml-3">Suppléments:</strong>

                            @foreach ($addons as $addon)
                                {{$addon["name"]}}
                                @if (!$loop->last)
                                ,
                                @endif
                            @endforeach
                          @endif
                        @if (!empty($item["notes"]))
                            <p class="mb-0"><strong>{{__("Comment")}}:</strong> {{$item["notes"]}}</p>
                        @endif


                    </li>
                    @endforeach
                  </ul>

                  <a class="btn btn-success w-100 mb-2" style="color: white" onclick="printReceipt('{{route('admin.printReceipt',$order->id)}}');">Reçu<i class="fas fa-print mx-2"></i></a>
                  <a class="btn btn-default w-100" style="color: white" href="{{route('admin.hideOrder',$order->id)}}">{{__("Hide")}} <i class="fas fa-archive mx-2"></i></a>

                </div>
              </div>
              @endif
              @endforeach
          @else

            {{-- BAR - BOISSON --}}
            <div class="col-md-12 d-flex flex-wrap justify-content-center" style="border-right: 1px solid #dbdde0;">
              @foreach($sub_orders as $s_order)
              @if(count($s_order->products)>0 && ( !isset($_GET['table']) || ( isset($_GET['table']) && strcmp($s_order->order->table_number,$_GET['table']) == 0) ))
              <div class="card m-2 order">
                <div class="card-body">
                  <h5 class="card-title">
                       {{__('Order')}} #{{$s_order->order->order_number}}
                       <br> <small style=" color: #979da1; "> <i class="fa fa-clock"></i> {{$s_order->updated_at->format('d/m/Y  h:m')}}</small>
                       </h5> <br>
                  <p class="card-text"> <b>Table : {{$s_order->order->table_number}}</b>
					@if ($state != 2)
					<button type="button" class="btn btn-gray mx-2" data-toggle="modal" data-target="#changeTable-{{$s_order->order->id}}">
                        <span class="btn-title"><i class="fa fa-exchange-alt"></i></span>
                    </button>
                    <button type="button" class="btn btn-gray mx-2" data-toggle="modal" data-target="#transfertProducts-{{$s_order->order->id}}">

                        <i class="fa fa-share"></i>
                    </button>
					@endif
				 </p>
                    <!-- Modal Change table -->
                    <div class="modal fade" id="changeTable-{{$s_order->order->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">  Modifier le numéro de la table de la {{__('Order')}} #{{$s_order->order->order_number}}  </h5>

                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="post" action="{{route('admin.order.changeTable')}}">
                                    @csrf
                                <div class="modal-body">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">{{ __('ChangeTableText') }} </label>
                                            <select name="table_number" class="form-control">
                                                @foreach($tables as $table)
                                                    @if($s_order->order->table_number != $table->table_no )
                                                     <option value="{{$table->table_no}}">{{$table->table_no}} {{  ($table->status == 0 )?' ( non disponible )':'' }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="order_id" value="{{$s_order->order->id}}">
                                            <input type="hidden" name="sub_order_id" value="{{$s_order->id}}">
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Valider</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal transfert products -->
                    <div class="modal fade" id="transfertProducts-{{$s_order->order->id}}" tabindex="-1" role="dialog"  aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel"> Transférer un produit de la {{__('Order')}}  #{{$s_order->order->order_number}} vers une autre table. </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="post" action="{{route('admin.order.transferProducts')}}">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Choisissez la nouvelle table et le plat à transférer :</label>
                                            <select name="table_number" class="form-control">
                                                @foreach($tables as $table)
                                                    @if( strcmp($s_order->order->table_number , $table->table_no) !== 0 )
                                                    <option
                                                        @if($s_order->order->table_number == $table->table_no ) selected @endif
                                                        value="{{$table->table_no}}">{{$table->table_no}}
                                                    </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="order_id" value="{{$s_order->order->id}}">
                                            <input type="hidden" name="sub_order_id" value="{{$s_order->id}}">
                                        </div>
                                        <ul class="list-group list-group-flush mb-2 mb-2 p-3">
                                            @php
                                                $total = 0;
                                            @endphp
                                            @foreach($s_order->products as $bp)
                                                <li class="list-group-item">
                                                        <input type="checkbox" class="form-check-input" name="sub-order-product-{{$bp->id}}" id="check-{{$bp->item->id."-".$bp->id}}">
                                                        <label class="form-check-label mx-2" for="check-{{$bp->item->id."-".$bp->id}}">
                                                            {{ $bp->item->product->title }}
                                                        </label>
                                                        <b class="mx-2"> x{{ $bp->quantity }}   </b>
                                                        <br>
                                                        @php
                                                            $item = $bp->item ;
                                                            $variations = json_decode($item->variations, true);
                                                            $total += $item->total;
                                                        @endphp
                                                        @if (!empty($variations))
                                                            <strong class="ml-3">Variation:</strong> {{$variations["name"]}}
                                                            <br>
                                                        @endif
                                                        @php
                                                            $addons = json_decode($item->addons, true);
                                                        @endphp
                                                        @if (!empty($addons))
                                                            <strong class="ml-3">Suppléments:</strong>

                                                            @foreach ($addons as $addon)
                                                                {{$addon["name"]}}
                                                                @if (!$loop->last)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        @if (!empty($item["notes"]))
                                                            <p class="mb-0"><strong>{{__("Comment")}}:</strong> {{$item["notes"]}}</p>
                                                        @endif

                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Transferer les produits <i class="fa fa-share mx-2"></i> </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                  <ul class="list-group list-group-flush mb-2">
                    @foreach($s_order->products as $bp)
                    <li class="list-group-item"> {{ $bp->item->product->title }}
                       <b class="mx-2"> x{{ $bp->quantity }} </b>
                       <form method="post" class="d-inline" action= "{{route('admin.orderitem.delete',  ['id' => $bp->id ] )}}">
                           @csrf
                           <button  class="pull-right btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>

                       </form>
                       <br>
                          @php
                              $item = $bp->item ;
                              $variations = json_decode($item->variations, true);
                          @endphp
                          @if (!empty($variations))
                            <strong class="ml-3">Variation:</strong> {{$variations["name"]}}
                            <br>
                          @endif
                          @php
                              $addons = json_decode($item->addons, true);
                          @endphp
                          @if (!empty($addons))
                            <strong class="ml-3">Suppléments:</strong>

                            @foreach ($addons as $addon)
                                {{$addon["name"]}}
                                @if (!$loop->last)
                                ,
                                @endif
                            @endforeach
                          @endif
                        @if (!empty($item["notes"]))
                            <p class="mb-0"><strong>{{__("Comment")}}:</strong> {{$item["notes"]}}</p>
                        @endif


                    </li>
                    @endforeach
                  </ul>

                  <div class="mb-4 d-flex justify-content-between"><strong>Montant à payer</strong><div style="display: inline-block;"><span>{{$be->base_currency_symbol_position == 'left' ? $be->base_currency_symbol : ''}}</span><span class="amount"> @if (session()->has("#" . $s_order->order->order_number)) {{ number_format((float)session("#" . $s_order->order->order_number), 2, '.', '') }} @else {{ number_format((float)$total, 2, '.', '') }} @endif </span><span>{{$be->base_currency_symbol_position == 'right' ? $be->base_currency_symbol : ''}}</span></div></div>
                  @switch($state)
                  @case(1)
                        <div class="btn-group btn-group-justified w-100">
                            <a href="{{route('admin.order.update.state',['sub_order_id'=> $s_order->id,'state'=>0 ])}}" class="btn btn-default">

                                <i class="fa fa-arrow-left"></i>
                            </a>

                            <a style="flex: 1;" href="{{route('admin.order.update.state',['sub_order_id'=> $s_order->id,'state'=>2 ])}}" class="btn btn-secondary w-80">
                                Terminer la commande
                                <i class="fa fa-arrow-right mx-2"></i>
                            </a>
                        </div>
                        <div class="pay-separately mt-1">
                            <button style="display: block; width: 100%;" class="btn btn-primary" onclick="showInput(this)">
                              Régler une partie de la note
                              <i class="fa fa-cash-register mx-2"></i>
                            </button>
                            <div class="pay-separately-input mt-2" style="display: none;">
                              <input class="form-control" type="number" placeholder="Montant à soustraire" name="payment" min="0">
                              <button class="btn btn-secondary" onclick="remainingAmount(this, '{{ $s_order->order->order_number }}')"><i class="fa fa-check"></i></button>
                              <button class="btn btn-dark" onclick="hideInput(this)"><i class="fa fa-times"></i></button>
                            </div>
                          </div>

                      @break

                  @case(2)
                      <a class="btn btn-success w-100" style="color: white" onclick="printReceipt('{{route('admin.printReceipt',$s_order->product_orders_id)}}');"><i class="fas fa-print mx-2"></i></a>
                      @break

                  @default
                  <a href="{{route('admin.order.update.state',['sub_order_id'=> $s_order->id,'state'=>1 ])}}" class="btn btn-success w-100" >
                      Commande traitée
                      <i class="fa fa-arrow-right mx-2"></i>
                  </a>
                  <form action="{{ route('admin.product.order.delete') }}" method="POST">
                    @csrf
                    <input type="number" name="order_id" value="{{ $s_order->order->id }}" hidden>
                    <button type="submit" class="btn btn-secondary w-100 mt-2">Refuser la commande</button>
                  </form>
                  @endswitch

                </div>
              </div>
              @endif
              @endforeach
            </div>

          @endif

      </div>
    </div>

  </div>


  @if((isset($sub_orders) && count($sub_orders)>12) || isset($orders) && count($orders)>12)
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="d-inline-block mx-auto my-2 no-ul">
          {{$sub_orders->links()}}
        </div>
      </div>
    </div>
  </div>
  @endif

	<div class="row">
		<div class="col-lg-12">
		  <div class="row row-card-no-pd">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<div class="card-head-row">
								<h4 class="card-title">{{__('Recent Reservation Requests')}}</h4>
							</div>
							<p class="card-category">
							{{__('Top 10 latest table reservation requests')}}</p>
						</div>
						<div class="card-body">
				  <div class="row">
					  <div class="col-lg-12">
						  @if (count($table_books) == 0)
						  <h3 class="text-center">{{__('NO RESERVATION REQUEST FOUND')}}</h3>
						  @else
						  <div class="table-responsive">
							<table class="table table-striped mt-3">
								<thead>
								  <tr>
                                    <th scope="col">{{__('Name')}}</th>
                                    <th scope="col">{{__('Email Address')}}</th>
                                    <th scope="col">{{__('Status')}}</th>
                                    <th scope="col">{{__('Details')}}</th>
								  </tr>
								</thead>
								<tbody>
                                    @foreach ($table_books as $key => $reservation)
                                    @if ($reservation->status == 1)
                                    <tr>
                                      <td>{{convertUtf8($reservation->name)}}</td>
                                      <td>{{convertUtf8($reservation->email)}}</td>
                                      <td>
                                      <form id="statusForm{{$reservation->id}}" class="d-inline-block" action="{{route('admin.status.table.resevations')}}" method="post">
                                          @csrf
                                          <input type="hidden" name="table_id" value="{{$reservation->id}}">
                                          <select class="form-control form-control-sm form-rounded w-auto text-light border-0 px-2
                                          @if ($reservation->status == 1)
                                            bg-warning
                                          @elseif ($reservation->status == 2)
                                            bg-success
                                          @elseif ($reservation->status == 3)
                                            bg-danger
                                          @endif
                                          " name="status" onchange="document.getElementById('statusForm{{$reservation->id}}').submit();">
                                            <option value="1" {{$reservation->status == 1 ? 'selected' : ''}}>{{__('Pending')}}</option>
                                            <option value="2" {{$reservation->status == 2 ? 'selected' : ''}}>{{__('Accepted')}}</option>
                                            <option value="3" {{$reservation->status == 3 ? 'selected' : ''}}>{{__('Rejected')}}</option>
                                          </select>
                                        </form>
                                      </td>
                                      <td>
                                        <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#detailsModal{{$reservation->id}}"><i class="fas fa-eye"></i> {{__('Details')}}</button>
                                      </td>
                                    </tr>
                                    @endif
                                    @includeif('admin.reservations.reservation-details')
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
		</div>
	  </div>

      <iframe id="receiptToPrint" style="display:none">

      </iframe>


<script>
    function printReceipt(link) {
     $.ajax({
        url: link,
        type: 'GET',
        dataType: 'html',
        success: (resp) => {
            if(resp) {
                var iframe = document.getElementById('receiptToPrint');
                if(iframe) {
                    iframe.srcdoc=`${resp}`
                    document.body.appendChild(iframe);
                    iframe.focus();
                    iframe.contentWindow.print();
                }
                }
            }
        });
        }
    function filterOrders(){
        let table_number = $('#table_number').val();
        if(table_number<=0){
            alert("entrer un numéro de table! ")
        }else{
            var url = window.location.href;
            if (url.indexOf('?') > -1){
               url += '&table=' + table_number ;
            }else{
               url += '?table=' + table_number ;
            }
            window.location.href = url;
        }

    }
</script>

@endsection
