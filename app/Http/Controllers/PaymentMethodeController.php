<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethode;

class PaymentMethodeController extends Controller
{
    public function index(Request $request)
    {
        $list = PaymentMethode::paginate(10);
        return $this->liteResponse(config("code.request.SUCCESS"), $list);
    }

    public function add(Request $request)
    {
        $add = PaymentMethode::create([
          'owner_id'=>auth()->id(),
          'card_number'=>$request->input('card_number'),
          'expiry_month'=>$request->input('expiry_month'),
          'expiry_year'=>$request->input('expiry_year'),
          'cvv'=>$request->input('cvv'),
          'card_type'=>$request->input('card_type'),
          'card_provider'=>$request->input('card_provider'),
          'is_default'=>$request->input('is_default')
        ]);

        if ($request->input('is_default')==true) {
            $request->request->add(['id'=>$add->getKey()]);
            $this->setDefault($request);
        }

        if ($add) {
            return $this->liteResponse(config("code.request.SUCCESS"), $add);
        }
        
        return $this->liteResponse(config('code.request.FAILURE'), $add);
    }

    public function update(Request $request)
    {
        $update = PaymentMethode::where([
                ['id','=', $request->input('id')],
                ['owner_id','=', auth()->id()],
            ])
            ->update([
                'card_number'=>$request->input('card_number'),
                'expiry_month'=>$request->input('expiry_month'),
                'expiry_year'=>$request->input('expiry_year'),
                'cvv'=>$request->input('cvv'),
                'card_type'=>$request->input('card_type'),
                'is_default'=>$request->input('is_default'),
            ]);
  
        if ($update) {
            return $this->liteResponse(config("code.request.SUCCESS"), $update);
        }
        return $this->liteResponse(config('code.request.FAILURE'), $update);
    }


    public function delete(Request $request)
    {
        $delete = PaymentMethode::where([
                ['id','=', $request->input('id')],
                ['owner_id','=', auth()->id()],
            ])
            ->delete();

        if ($delete) {
            return $this->liteResponse(config("code.request.SUCCESS"), $delete);
        }
        return $this->liteResponse(config('code.request.FAILURE'), $delete);
    }

    public function setDefault(Request $request)
    {
        $update = PaymentMethode::where([
                ['id','=', $request->input('id')],
                ['owner_id','=', auth()->id()],
            ])
            ->update([
                'is_default'=>true,
            ]);

        $update = PaymentMethode::where([
                ['id','<>', $request->input('id')],
                ['owner_id','=', auth()->id()],
            ])
            ->update([
                'is_default'=>false,
            ]);
  
        if ($update) {
            return $this->liteResponse(config("code.request.SUCCESS"), $update);
        }
        return $this->liteResponse(config('code.request.FAILURE'), $update);
    }
}
