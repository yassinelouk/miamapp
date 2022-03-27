<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BasicSetting as BS;
use App\Models\BasicExtended as BE;
use App\Models\BasicExtended;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ShippingCharge;
use App\Models\ProductReview;
use Auth;
use App\Models\Pcategory;
use Session;
use App\Models\Language;
use App\Models\OfflineGateway;
use App\Models\PaymentGateway;
use App\Models\PostalCode;
use App\Models\ServingMethod;
use App\Models\TimeFrame;
use App\Models\FidelityTime;
use Carbon\Carbon;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('setlang');
    }

    public function product(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['currentLang'] = $currentLang;

        $lang_id = $currentLang->id;

        $data['categories'] = Pcategory::where('status', 1)->where('language_id', $currentLang->id)->orderBy('position')->get();

        $data['products'] = Product::where('language_id', $lang_id)->where('status', 1)->paginate(10);

        return view('front.product.product', $data);
    }

    public function productDetails($slug, $id)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        Session::put('link', route('front.product.details', ['slug' => $slug, 'id' => $id]));

        $data['product'] = Product::where('id', $id)->where('language_id', $currentLang->id)->first();
        $data['categories'] = Pcategory::where('status', 1)->where('language_id', $currentLang->id)->get();
        $data['reviews'] = ProductReview::where('product_id', $id)->get();

        $data['related_product'] = Product::where('category_id', $data['product']->category_id)->where('language_id', $currentLang->id)->where('id', '!=', $data['product']->id)->get();

        return view('front.product.details', $data);
    }

    public function items(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['currentLang'] = $currentLang;
        $lang_id = $currentLang->id;

        $data['products'] = Product::where('status', 1)->where('language_id', $currentLang->id)->paginate(6);
        $data['categories'] = Pcategory::where('status', 1)->where('language_id', $currentLang->id)->get();

        $search = $request->search;
        $minprice = $request->minprice;
        $maxprice = $request->maxprice;
        $category = $request->category_id;

        if ($request->type) {
            $type = $request->type;
        } else {
            $type = 'new';
        }


        $review = $request->review;

        $data['products'] =
            Product::when($category, function ($query, $category) {
                return $query->where('category_id', $category);
            })
            ->when($lang_id, function ($query, $lang_id) {
                return $query->where('language_id', $lang_id);
            })
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', '%' . $search . '%')->orwhere('summary', 'like', '%' . $search . '%')->orwhere('description', 'like', '%' . $search . '%');
            })
            ->when($minprice, function ($query, $minprice) {
                return $query->where('current_price', '>=', $minprice);
            })
            ->when($maxprice, function ($query, $maxprice) {
                return $query->where('current_price', '<=', $maxprice);
            })

            ->when($review, function ($query, $review) {
                return $query->where('rating', '>=', $review);
            })

            ->when($type, function ($query, $type) {
                if ($type == 'new') {
                    return $query->orderBy('id', 'DESC');
                } elseif ($type == 'old') {
                    return $query->orderBy('id', 'ASC');
                } elseif ($type == 'high-to-low') {
                    return $query->orderBy('current_price', 'DESC');
                } elseif ($type == 'low-to-high') {
                    return $query->orderBy('current_price', 'ASC');
                }
            })

            ->where('status', 1)->paginate(9);

        return view('front.product.items', $data);
    }

    public function cart()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        if (Session::has('cart')) {
            $cart = Session::get('cart');
        } else {
            $cart = null;
        }
        return view('front.product.cart', compact('cart'));
    }

    public function addToCart($id)
    {
        $cart = Session::get('cart');
        $data = explode(',,,', $id);
        $id = (int)$data[0];
        $qty = (int)$data[1];
        $total = (float)$data[2];
        $variant = json_decode($data[3], true);
        $addons = json_decode($data[4], true);
        $notes = $data[5];


        $product = Product::findOrFail($id);

        // validations
        if ($qty < 1) {
            return response()->json(['error' => __('Quanty must be 1 or more than 1')]);
        }
        $pvariant = json_decode($product->variations, true);
        if (!empty($pvariant) && empty($variant)) {
            return response()->json(['error' => __('You must select a variant')]);
        }


        if (!$product) {
            abort(404);
        }
        $cart = Session::get('cart');
        $ckey = uniqid();

        // if cart is empty then this the first product
        if (!$cart) {

            $cart = [
                $ckey => [
                    "id" => $id,
                    "name" => $product->title,
                    "qty" => (int)$qty,
                    "variations" => $variant,
                    "addons" => $addons,
                    "product_price" => (float)$product->current_price,
                    "total" => $total,
                    "photo" => $product->feature_image,
                    "fidelity_score" => (int)$product->fidelity_score,
                    "notes" => $notes
                ]
            ];

            Session::put('cart', $cart);
            return response()->json(['message' => __('Product added to cart successfully !')]);
        }

        // if cart not empty then check if this product (with same variation and comment) exist then increment quantity
        foreach ($cart as $key => $cartItem) {
            if ($cartItem["id"] == $id && $variant == $cartItem["variations"] && $addons == $cartItem["addons"] && (isset($cartItem["notes"]) && strcasecmp($notes, $cartItem["notes"]) == 0 || !isset($cartItem["notes"]))) {
                $cart[$key]['qty'] = (int)$cart[$key]['qty'] + $qty;
                $cart[$key]['total'] = (float)$cart[$key]['total'] + $total;
                Session::put('cart', $cart);
            return response()->json(['message' => __('Product added to cart successfully !')]);
            }
        }

        // if item not exist in cart then add to cart with quantity = 1
        $cart[$ckey] = [
            "id" => $id,
            "name" => $product->title,
            "qty" => (int)$qty,
            "variations" => $variant,
            "addons" => $addons,
            "product_price" => (float)$product->current_price,
            "total" => $total,
            "photo" => $product->feature_image,
            "fidelity_score" => (int)$product->fidelity_score,
            "notes" => $notes
        ];


        Session::put('cart', $cart);

            return response()->json(['message' =>__('Product added to cart successfully !')]);
    }


    public function updatecart(Request $request)
    {
        $cart = Session::get('cart');
        $qtys = $request->qty;
        $i = 0;


        foreach ($cart as $cartKey => $cartItem) {
            $total = 0;
            $cart[$cartKey]["qty"] = (int)$qtys[$i];

            // calculate total
            $addons = $cartItem["addons"];
            if (is_array($addons)) {
                foreach ($addons as $key => $addon) {
                    $total += (float)$addon["price"];
                }
            }
            if (is_array($cartItem["variations"])) {
                foreach($cartItem["variations"] as $var){
                    $total += (float)$var["price"];
                }

            }
            $total += (float)$cartItem["product_price"];
            $total = $total * $qtys[$i];

            // save total in the cart item
            $cart[$cartKey]["total"] = $total;

            $i++;
        }

        Session::put('cart', $cart);

        return response()->json(['message' => __('Cart Update Successfully')]);
    }


    public function cartitemremove($id)
    {
        if ($id) {
            $cart = Session::get('cart');
            unset($cart[$id]);
            Session::put('cart', $cart);

            return response()->json(['message' => __('Item removed successfully')]);
        }
    }


    public function checkout(Request $request)
    {
        if ($request->type != 'guest' && !Auth::check()) {
            Session::put('link', route('front.checkout'));
            return redirect(route('user.login', ['redirected' => 'checkout']));
        }

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        if (Session::has('cart')) {
            $data['cart'] = Session::get('cart');
        } else {
            $data['cart'] = null;
        }
        $data['shippings'] = ShippingCharge::where('language_id', $currentLang->id)->get();
        $data['postcodes'] = PostalCode::where('language_id', $currentLang->id)->orderBy('serial_number', 'ASC')->get();
        $data['ogateways'] = OfflineGateway::where('status', 1)->orderBy('serial_number', 'ASC')->get();
        $data['stripe'] = PaymentGateway::find(14);
        $data['paypal'] = PaymentGateway::find(15);
        $data['paystackData'] = PaymentGateway::whereKeyword('paystack')->first();
        $data['paystack'] = $data['paystackData']->convertAutoData();
        $data['flutterwave'] = PaymentGateway::find(6);
        $data['razorpay'] = PaymentGateway::find(9);
        $data['instamojo'] = PaymentGateway::find(13);
        $data['paytm'] = PaymentGateway::find(11);
        $data['mollie'] = PaymentGateway::find(17);
        $data['mercadopago'] = PaymentGateway::find(19);
        $data['payumoney'] = PaymentGateway::find(18);

        $data['scharges'] = $currentLang->shippings;
        $data['smethods'] = ServingMethod::where('website_menu', 1)->orderBy('serial_number', 'ASC')->get();

        $data['discount'] = session()->has('coupon') && !empty(session()->get('coupon')) ? session()->get('coupon') : 0;
        $data['fidelity_discount'] = session()->has('fidelity_discount') && !empty(session()->get('fidelity_discount')) ? session()->get('fidelity_discount') : 0;

        $days = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
        $disDays = [];
        foreach ($days as $key => $day) {
            $count = TimeFrame::where('day', $day)->count();
            if ($count == 0) {
                if ($day == 'sunday') {
                    $disDays[] = 0;
                } elseif ($day == 'monday') {
                    $disDays[] = 1;
                } elseif ($day == 'tuesday') {
                    $disDays[] = 2;
                } elseif ($day == 'wednesday') {
                    $disDays[] = 3;
                } elseif ($day == 'thursday') {
                    $disDays[] = 4;
                } elseif ($day == 'friday') {
                    $disDays[] = 5;
                } elseif ($day == 'saturday') {
                    $disDays[] = 6;
                }
            }
        }
        $data['disDays'] = $disDays;
        Session::put('id_table', Session::get('table'));
        return view('front.product.checkout', $data);
    }


    public function Prdouctcheckout(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)->first();

        if (!$product) {
            abort(404);
        }

        if ($request->qty) {
            $qty = $request->qty;
        } else {
            $qty = 1;
        }


        $cart = Session::get('cart');
        $id = $product->id;
        // if cart is empty then this the first product
        if (!($cart)) {
            if ($product->stock <  $qty) {
                Session::flash('error', __('Out of stock'));
                return back();
            }
            $cart = [
                $id => [
                    "name" => $product->title,
                    "qty" => $qty,
                    "price" => $product->current_price,
                    "photo" => $product->feature_image
                ]
            ];

            Session::put('cart', $cart);

            return redirect(route('front.checkout'));
        }

        // if cart not empty then check if this product exist then increment quantity
        if (isset($cart[$id])) {

            if ($product->stock < $cart[$id]['qty'] + $qty) {
                Session::flash('error', __('Out of stock'));
                return back();
            }
            $qt = $cart[$id]['qty'];
            $cart[$id]['qty'] = $qt + $qty;

            Session::put('cart', $cart);

            return redirect(route('front.checkout'));
        }

        if ($product->stock <  $qty) {
            Session::flash('error', __('Out of stock'));
            return back();
        }


        $cart[$id] = [
            "name" => $product->title,
            "qty" => $qty,
            "price" => $product->current_price,
            "photo" => $product->feature_image
        ];
        Session::put('cart', $cart);

        return redirect(route('front.checkout'));
    }
    public function useFidelity(Request $request) {
        if(Session::has('fidelity_discount')) {
            Session::forget('fidelity_discount');
            return response()->json(['status' => 'success', 'message' => __('Canceled fidelity points discount successfully'), 'btn_state' => '<button id="fidelity-btn" class="btn btn-primary base-btn" type="button" onclick="useFidelityPoints();"><i class="fas fa-tag"></i>   '.__("Use my fidelity points").'</button>']);
        } else {
            // get todays day & time
            $now = Carbon::now();
            $todaysDay = strtolower($now->format('l'));
            $currentTime = strtotime($now->toTimeString());

            // search in database by today's day & retrieve start & end time
            $orderTime = FidelityTime::where('day', $todaysDay)->first();
            $start = strtotime($orderTime->start_time);
            $end = strtotime($orderTime->end_time);

            // check if any of the start or end time is emply,
            // then show message 'shop is closed today'
            if (empty($start) || empty($end)) {
                return response()->json(['status' => 'error', 'message' => __('Fidelity points are not available on').' '. $todaysDay]);
            }

            // check if current time is not between retrieved start & end time,
            // then show message 'shop is closed now'
            if ($currentTime < $start || $currentTime > $end) {
                return response()->json(['status' => 'error', 'message' => __('Fidelity points are available from :starttime to :endtime on :today', ['starttime' => $orderTime->start_time, 'endtime' => $orderTime->end_time, "today" => $todaysDay])]);
            }
            $auth_user = Auth::user();
            if ($auth_user->fidelity_points <= 0) {
                return response()->json([ 'status' => 'error', 'message' => __('Sorry, you do not have fidelity points')]);
            }
            else {
                $cartTotal = cartTotal();
                $be = BasicExtended::first();
                if(($cartTotal/$be->base_fidelity_rate) >= $auth_user->fidelity_points) {
                    $reductionAmount = $auth_user->fidelity_points * $be->base_fidelity_rate;
                }
                else {
                    $reductionAmount = $cartTotal;
                }
                session()->put('fidelity_discount', round($reductionAmount, 2));
                return response()->json(['status' => 'success','message' => __('Fidelity points discount applied successfully'), 'btn_state' => '<button id="fidelity-btn" class="btn btn-primary btn-danger" type="button" onclick="useFidelityPoints();"><i class="fas fa-tag"></i>   '.__("Cancel my fidelity discount").'</button>']);
            }
        }
    }
    public function coupon(Request $request) {
        $coupon = Coupon::where('code', $request->coupon);
        $be = BasicExtended::first();

        if ($coupon->count() == 0) {
            return response()->json(['status' => 'error', 'message' => __("Coupon is not valid")]);
        } else {
            $coupon = $coupon->first();
            if (cartTotal() < $coupon->minimum_spend) {
                return response()->json(['status' => 'error', 'message' => __("Cart Total must be minimum") . ' ' . $coupon->minimum_spend . " " . $be->base_currency_text]);
            }
            $start = Carbon::parse($coupon->start_date);
            $end = Carbon::parse($coupon->end_date);
            $today = Carbon::now();
            // return response()->json($end->lessThan($today));

            // if coupon is active
            if ($today->greaterThanOrEqualTo($start) && $today->lessThan($end)) {
                $cartTotal = cartTotal();
                $value = $coupon->value;
                $type = $coupon->type;

                if ($type == 'fixed') {
                    if ($value > cartTotal()) {
                        return response()->json(['status' => 'error', 'message' => __('Coupon discount is greater than cart total')]);
                    }
                    $couponAmount = $value;
                } else {
                    $couponAmount = ($cartTotal * $value) / 100;
                }
                session()->put('coupon', round($couponAmount, 2));

                return response()->json(['status' => 'success', 'message' => __("Coupon applied successfully")]);
            } else {
                return response()->json(['status' => 'error', 'message' => __("Coupon is not valid")]);
            }
        }
    }

    public function timeframes(Request $request) {
        $date = Carbon::parse($request->date);
        $day = strtolower($date->format('l'));

        $timeframes = TimeFrame::where('day', $day)->get();

        if (count($timeframes) > 0) {
            // if (condition) {
            //     # code...
            // }
            return response()->json(['status' => 'success', 'timeframes' => $timeframes]);
        } else {
            return response()->json(['status' => 'error', 'message' => __('No delivery time frame is available on').' '.ucfirst($day) ]);
        }
    }
}
