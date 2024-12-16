<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\LuggageRequest;

class NotificationController extends Controller
{
    public function store(Request $request){

        try {
            $save = Notification::create([
                'title'=> $request->notif_title,
                'message'=> $request->notif_message,
                'type'=> $request->notif_type,
                'request_id'=> $request->notif_request_id,
                'owner_id'=> $request->notif_owner_id,
                'sender_id'=> $request->notif_sender_id,
              ]);
        
              if($save){
                return $this->liteResponse(config('code.request.SUCCESS'), $save);
              }
        
            return $this->liteResponse(config('code.request.FAILURE'), null);
        } catch (Exception $ex) {
            return $this->liteResponse(config('code.request.FAILURE'), $ex->getMessage());
        }
    
    }

  public function list(){
        $userNotif = Notification::with(["sender","luggageRequests"])->where('owner_id', auth()->id())->orderByDesc('created_at')->paginate(20);
        return $this->liteResponse(config('code.request.SUCCESS'), $userNotif);
  }

    
  public function notifMessage($luggageRequest) {

    $isValidator = $luggageRequest->as_validator;
    $weight = $luggageRequest->weight;
    $initiator = $luggageRequest->initiator; 
    $state = $luggageRequest->state;
    $cancelAt = $luggageRequest->cancel_at;

    $amountAddFees = ceil(($luggageRequest->weight * $luggageRequest->proposal_unit_price) + $luggageRequest->transaction_fees);
    $amountwithNoFees = ceil(($luggageRequest->weight * $luggageRequest->proposal_unit_price));
    $amoutRemoveFees = ceil(($luggageRequest->weight * $luggageRequest->proposal_unit_price) - $luggageRequest->transaction_fees);


    if (!$isValidator) {

      if ($initiator === LuggageRequest::INIT_BY_TRAVELER) {

        if ($state === LuggageRequest::STATE_DENIED) {
          return "Vous avez refusé une invitation à reserver $weight Kg pour un montant de $amountwithNoFees €";
        }

        if ($state === LuggageRequest::STATE_CANCEL || $cancelAt != null) {
          return "L'invitation à reserver $weight Kg pour un montant de $amountwithNoFees € a été annulée ";
        }

        if ($state === LuggageRequest::STATE_ACCEPTED) {
          return "Vous avez accepté une invitation à reserver $weight Kg pour un montant de $amountwithNoFees €";
        }

        return "Vous avez reçu une invitation à reserver $weight Kg pour un montant de $amountwithNoFees €";
      } else {
        
        if ($state === LuggageRequest::STATE_DENIED) {
          return "Vous avez refusé la demande d’acheminement de $weight Kg pour un montant de $amoutRemoveFees €";
        }

        if ($state === LuggageRequest::STATE_CANCEL || $cancelAt != null) {
          return "La demande d’acheminement de $weight Kg pour un montant de $amoutRemoveFees € a été annulée";
        }

        if ($state === LuggageRequest::STATE_ACCEPTED) {
          return "Vous avez accepté la demande d’acheminement de $weight Kg pour un montant de $amoutRemoveFees €";
        }

        return "Vous avez une demande d’acheminement de $weight Kg pour un montant de $amoutRemoveFees €";
      }
    } else {
      if ($initiator === LuggageRequest::INIT_BY_TRAVELER) {
        if ($state === LuggageRequest::STATE_DENIED) {
          return "Votre invitation à reserver $amoutRemoveFees € a été refusée ";
        }

        if ($state === LuggageRequest::STATE_CANCEL || $cancelAt != null) {
          return "Votre invitation à reserver $weight Kg pour un montant de $amoutRemoveFees € a été annulée";
        }

        if ($state === LuggageRequest::STATE_ACCEPTED) {
          return "Votre invitation à reserver $weight Kg pour un montant de $amoutRemoveFees € a été acceptée";
        }

        return "Vous avez envoyé une invitation à reserver $weight Kg pour un montant de $amoutRemoveFees €";
      } else {

        if ($state === LuggageRequest::STATE_DENIED) {
          return "Votre demande d’acheminement d’un colis de $weight Kg pour un montant de $amountwithNoFees € a été refusée";
        }

        if ($state === LuggageRequest::STATE_CANCEL || $cancelAt != null) {
          return "Votre demande d’acheminement de $weight Kg pour un montant de $amountwithNoFees € a été annulée";
        }

        if ($state === LuggageRequest::STATE_ACCEPTED) {
          return "Votre demande d’acheminement de $weight Kg pour un montant de $amountwithNoFees € a été accepté";
        }

        return "Vous avez envoyé une demande d’acheminement de $weight Kg pour un montant de $amountwithNoFees €";
      }
    }

  }

}
